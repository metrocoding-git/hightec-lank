<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Leave requests.
 *
 * Roles are controlled by permissions (Role Management):
 *  - leave.my          : "My Leaves" — apply and see ONLY your own leaves
 *  - leave.store       : submit a leave request
 *  - leave.cancel      : cancel your own request while it is still pending
 *  - leave.review-list : see other staff's requests (same branch)
 *  - leave.review      : approve / reject a request with a note
 *
 * The details page (leave.show) and the attachment are opened by the
 * applicant themselves or by a reviewer — checked here, not by a separate
 * permission, so one role can apply and another role can review.
 */
class LeaveRequestController extends Controller
{
    // ===================================================================
    // Applicant side — own leaves only
    // ===================================================================

    public function myLeaves(Request $request)
    {
        $user = Auth::user();
        $year = (int) $request->input('year', now()->year);

        $leaves = LeaveRequest::with('reviewer:id,name')
            ->where('user_id', $user->id)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->whereYear('from_date', $year)
            ->orderByDesc('from_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->appends($request->query());

        $summary = $this->yearSummary($user->id, $year);

        return view('pages.leave.my-leaves', [
            'leaves' => $leaves,
            'summary' => $summary,
            'year' => $year,
            'types' => LeaveRequest::TYPES,
            'dayParts' => LeaveRequest::DAY_PARTS,
            'canApply' => $user->hasPermission('leave.store'),
            'canCancel' => $user->hasPermission('leave.cancel'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'leave_type' => 'required|in:' . implode(',', LeaveRequest::TYPES),
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'day_part' => 'required|in:' . implode(',', array_keys(LeaveRequest::DAY_PARTS)),
            'reason' => 'required|string|max:2000',
            'contact_during_leave' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $user = Auth::user();
        $from = Carbon::parse($data['from_date'])->startOfDay();
        $to = Carbon::parse($data['to_date'])->startOfDay();

        if ($data['day_part'] !== 'full' && !$from->equalTo($to)) {
            return back()->withInput()->withErrors(['day_part' => 'A half day leave must be for a single date (From and To the same).']);
        }

        $days = $this->countDays($from, $to, $data['day_part']);
        if ($days <= 0) {
            return back()->withInput()->withErrors(['from_date' => 'The selected dates are only Sundays — there are no working days to take leave on.']);
        }

        // No overlapping pending/approved leave for the same person.
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('from_date', '<=', $to->toDateString())
            ->whereDate('to_date', '>=', $from->toDateString())
            ->first();

        if ($overlap) {
            $samePartOk = $overlap->from_date->equalTo($overlap->to_date)
                && $from->equalTo($to)
                && $overlap->from_date->equalTo($from)
                && $overlap->day_part !== 'full'
                && $data['day_part'] !== 'full'
                && $overlap->day_part !== $data['day_part'];

            if (!$samePartOk) {
                return back()->withInput()->withErrors([
                    'from_date' => 'You already have a ' . $overlap->status . ' leave from '
                        . $overlap->from_date->format('Y-m-d') . ' to ' . $overlap->to_date->format('Y-m-d')
                        . ' that overlaps these dates.',
                ]);
            }
        }

        $data['attachment'] = $request->hasFile('attachment')
            ? $request->file('attachment')->store('leave_attachments', 'public')
            : null;

        LeaveRequest::create(array_merge($data, [
            'user_id' => $user->id,
            'branch_id' => optional($user->branches()->first())->id,
            'days' => $days,
            'status' => 'pending',
        ]));

        return redirect()->route('leave.my')->with('message-success', 'Leave request submitted (' . rtrim(rtrim(number_format($days, 1), '0'), '.') . ' day(s)). It is waiting for review.');
    }

    public function cancel($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        if ((int) $leave->user_id !== (int) Auth::id()) {
            abort(403, 'You can only cancel your own leave requests.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('message-danger', 'Only a pending leave request can be cancelled.');
        }

        $leave->update(['status' => 'cancelled']);

        return back()->with('message-success', 'Leave request cancelled.');
    }

    // ===================================================================
    // Reviewer side
    // ===================================================================

    public function reviewList(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'pending');

        $query = $this->reviewScope(LeaveRequest::with(['user:id,name,role_id', 'user.role', 'branch:id,name', 'reviewer:id,name']))
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->when($request->filled('branch_id'), fn($q) => $q->where('branch_id', $request->branch_id))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('leave_type'), fn($q) => $q->where('leave_type', $request->leave_type))
            ->when($request->filled('date_range'), function ($q) use ($request) {
                $parts = explode(' to ', $request->date_range);
                $from = trim($parts[0]);
                $to = trim($parts[1] ?? $parts[0]);
                $q->whereDate('from_date', '<=', $to)->whereDate('to_date', '>=', $from);
            });

        $counts = $this->reviewScope(LeaveRequest::query())
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $leaves = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('from_date', $status === 'pending' ? 'asc' : 'desc')
            ->paginate(25)
            ->appends($request->query());

        $applicantIds = $this->reviewScope(LeaveRequest::query())->distinct()->pluck('user_id');

        return view('pages.leave.review-list', [
            'leaves' => $leaves,
            'counts' => $counts,
            'status' => $status,
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'applicants' => User::whereIn('id', $applicantIds)->orderBy('name')->get(['id', 'name']),
            'types' => LeaveRequest::TYPES,
            'canReview' => $user->hasPermission('leave.review'),
        ]);
    }

    public function review(Request $request, $id)
    {
        $data = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string|max:2000|required_if:decision,rejected',
        ], [
            'review_note.required_if' => 'Please give a reason when not accepting a leave request.',
        ]);

        $leave = $this->reviewScope(LeaveRequest::query())->findOrFail($id);

        if ((int) $leave->user_id === (int) Auth::id()) {
            return back()->with('message-danger', 'You cannot approve or reject your own leave request.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('message-danger', 'This leave request has already been ' . $leave->status . '.');
        }

        $leave->update([
            'status' => $data['decision'],
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('leave.show', $leave->id)
            ->with('message-success', 'Leave request ' . ($data['decision'] === 'approved' ? 'accepted' : 'not accepted') . '.');
    }

    // ===================================================================
    // Details (applicant: own only; reviewer: in their scope)
    // ===================================================================

    public function show($id)
    {
        $leave = LeaveRequest::with(['user', 'user.role', 'branch', 'reviewer'])->findOrFail($id);
        $this->authorizeView($leave);

        $isOwner = (int) $leave->user_id === (int) Auth::id();
        $year = $leave->from_date->year;

        // Reviewers see the applicant's other leaves this year to help decide.
        $history = LeaveRequest::where('user_id', $leave->user_id)
            ->where('id', '!=', $leave->id)
            ->whereYear('from_date', $year)
            ->orderByDesc('from_date')
            ->get();

        $overlapping = LeaveRequest::with('user:id,name')
            ->where('id', '!=', $leave->id)
            ->where('user_id', '!=', $leave->user_id)
            ->where('status', 'approved')
            ->when($leave->branch_id, fn($q) => $q->where('branch_id', $leave->branch_id))
            ->whereDate('from_date', '<=', $leave->to_date->toDateString())
            ->whereDate('to_date', '>=', $leave->from_date->toDateString())
            ->get();

        return view('pages.leave.show', [
            'leave' => $leave,
            'isOwner' => $isOwner,
            'summary' => $this->yearSummary($leave->user_id, $year),
            'year' => $year,
            'history' => $history,
            'overlapping' => $isOwner ? collect() : $overlapping,
            'canReview' => !$isOwner && Auth::user()->hasPermission('leave.review') && $leave->status === 'pending',
            'canCancel' => $isOwner && $leave->status === 'pending' && Auth::user()->hasPermission('leave.cancel'),
        ]);
    }

    public function attachment($id)
    {
        $leave = LeaveRequest::findOrFail($id);
        $this->authorizeView($leave);

        if (!$leave->attachment || !Storage::disk('public')->exists($leave->attachment)) {
            abort(404);
        }

        return Storage::disk('public')->response($leave->attachment);
    }

    // ===================================================================
    // Helpers
    // ===================================================================

    /** Applicant sees only their own; a reviewer sees requests in their branches. */
    private function authorizeView(LeaveRequest $leave): void
    {
        if ((int) $leave->user_id === (int) Auth::id()) {
            return;
        }

        $user = Auth::user();
        $inScope = $this->reviewScope(LeaveRequest::query())->whereKey($leave->id)->exists();

        if (!$user->hasPermission('leave.review-list') || !$inScope) {
            abort(403, 'You can only view your own leave requests.');
        }
    }

    /**
     * Reviewers only see requests from staff in the same branch(es) as
     * themselves (plus requests that have no branch). A reviewer with no
     * branch assigned sees everything.
     */
    private function reviewScope($query)
    {
        return $query->visibleToReviewer(Auth::user());
    }

    /** Working days in the range (Sundays not counted); half day = 0.5. */
    private function countDays(Carbon $from, Carbon $to, string $dayPart): float
    {
        if ($dayPart !== 'full') {
            return $from->isSunday() ? 0 : 0.5;
        }

        $days = 0;
        foreach (CarbonPeriod::create($from, $to) as $day) {
            if (!$day->isSunday()) {
                $days++;
            }
        }

        return (float) $days;
    }

    /** Days per leave type for one user in a year: approved / pending. */
    private function yearSummary(int $userId, int $year): array
    {
        $rows = LeaveRequest::where('user_id', $userId)
            ->whereYear('from_date', $year)
            ->whereIn('status', ['approved', 'pending'])
            ->selectRaw('leave_type, status, SUM(days) as d, COUNT(*) as n')
            ->groupBy('leave_type', 'status')
            ->get();

        $byType = [];
        foreach ($rows as $r) {
            $byType[$r->leave_type][$r->status] = (float) $r->d;
        }

        return [
            'by_type' => $byType,
            'approved_total' => (float) $rows->where('status', 'approved')->sum('d'),
            'pending_total' => (float) $rows->where('status', 'pending')->sum('d'),
            'pending_count' => (int) $rows->where('status', 'pending')->sum('n'),
        ];
    }
}

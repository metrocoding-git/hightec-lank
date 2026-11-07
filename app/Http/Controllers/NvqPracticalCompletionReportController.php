<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * NVQ Paid — Practical Completion report.
 *
 * "from eke date ekay time ekay select karama enna one e range eke NVQ pay
 * wechch aya ganna one. eta passe e set eken ape attendance walin gannone
 * practical time ethakota required practical time eken presatage ekak wise
 * complete ganna puluwan. eka default set wenna 100% ma sampurana ayawa..
 * e set eken start branch eken select wenna. eta passe ena set eken course
 * wise filter karanna"
 *
 *  1. Students who made an NVQ payment between From (date + time) and To
 *     (date + time) — invoices_payments.created_at on an NVQ invoice
 *     (returned invoices and refunded payments are not counted).
 *  2. For each of those, the matching enrolment (same student, the NVQ
 *     invoice's course — original or changed course) and its practical
 *     completion % = practice time done ÷ required practical time, over all
 *     practical subjects assigned to that intake + course. Time done on a
 *     subject counts up to that subject's required time only.
 *  3. Only students at or above "Practical completion %" are listed —
 *     default 100 % (fully completed).
 *  4. Branch = the branch the course was started in (student_courses.branch_id).
 *  5. Course — chosen from the courses found in the set above.
 */
class NvqPracticalCompletionReportController extends Controller
{
    private const DEFAULT_MIN_PERCENT = 100;

    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);
        $minPercent = $this->minPercent($request);

        $all = $this->buildRows($request, $from, $to, false);

        // Course choices come from the set itself (after date, branch and %).
        $courseOptions = $all->groupBy('course_id')
            ->map(fn($g) => ['id' => $g->first()['course_id'], 'name' => $g->first()['course_name'], 'count' => $g->count()])
            ->sortBy('name')
            ->values();

        $rows = $this->applyCourse($all, $request);

        $summary = [
            'students' => $rows->count(),
            'nvq_paid' => $rows->sum('nvq_paid_in_range'),
            'courses' => $rows->pluck('course_id')->unique()->count(),
            'all_in_range' => $this->paidStudentCount($from, $to, $request),
        ];

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $data = new LengthAwarePaginator(
            $rows->slice(($page - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('pages.reports.nvq-practical-completion', compact(
            'data', 'branches', 'courseOptions', 'summary', 'minPercent', 'from', 'to'
        ));
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = $this->applyCourse($this->buildRows($request, $from, $to, true), $request);

        $headings = [
            'No', 'Student No', 'Student Name', 'NIC', 'Phone', 'Course', 'Intake', 'Intake Start',
            'Start Branch', 'Enrolment Status', 'Practical Subjects Done', 'Practical Subjects Total',
            'Practice Time Done (h)', 'Required Practical Time (h)', 'Practical Completion %',
            'NVQ Paid In Range', 'NVQ Payments In Range', 'Last NVQ Payment', 'Receipt No(s)',
        ];

        $out = [];
        foreach ($rows->values() as $i => $r) {
            $out[] = [
                $i + 1,
                $r['admission_number'],
                $r['student_name'],
                $r['nic'],
                $r['phone'],
                $r['course_name'],
                $r['intake_name'],
                $r['intake_start'] ? Carbon::parse($r['intake_start']) : null,
                $r['branch_name'],
                ucfirst((string) $r['status']),
                $r['practical_done'],
                $r['practical_total'],
                round($r['practice_done_min'] / 60, 2),
                round($r['practice_required_min'] / 60, 2),
                $r['percent'] / 100,
                $r['nvq_paid_in_range'],
                $r['payments_in_range'],
                $r['last_paid_at'] ? Carbon::parse($r['last_paid_at']) : null,
                $r['receipts'],
            ];
        }

        $title = 'NVQ paid ' . $from->format('Y-m-d h:i A') . ' to ' . $to->format('Y-m-d h:i A')
            . ' — practical completion ≥ ' . $this->formatPercent($this->minPercent($request)) . '%';

        $export = new class($out, $headings, $title) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\ShouldAutoSize,
            \Maatwebsite\Excel\Concerns\WithColumnFormatting,
            \Maatwebsite\Excel\Concerns\WithStyles,
            \Maatwebsite\Excel\Concerns\WithTitle {
            public function __construct(private array $rows, private array $headings, private string $title) {}

            public function array(): array
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return [[$this->title], $this->headings];
            }

            public function columnFormats(): array
            {
                return [
                    'H' => 'yyyy-mm-dd',
                    'M' => '0.00',
                    'N' => '0.00',
                    'O' => '0.0%',
                    'P' => '#,##0.00',
                    'R' => 'yyyy-mm-dd hh:mm',
                ];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                $sheet->freezePane('A3');
                $sheet->setAutoFilter('A2:S' . max(2, count($this->rows) + 2));

                return [1 => ['font' => ['bold' => true, 'size' => 12]], 2 => ['font' => ['bold' => true]]];
            }

            public function title(): string
            {
                return 'NVQ Practical';
            }
        };

        return \Maatwebsite\Excel\Facades\Excel::download(
            $export,
            'NVQ-Practical-Completion_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function range(Request $request): array
    {
        $parse = function ($value, Carbon $fallback) {
            if (!$value) {
                return $fallback;
            }
            try {
                return Carbon::parse(str_replace('T', ' ', (string) $value));
            } catch (\Throwable $e) {
                return $fallback;
            }
        };

        $from = $parse($request->input('paid_from'), now()->startOfMonth());
        $to = $parse($request->input('paid_to'), now()->endOfDay());

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        // A "To" without seconds covers that whole minute.
        $to = $to->copy()->second(59);

        return [$from->copy()->second(0), $to];
    }

    private function minPercent(Request $request): float
    {
        $value = $request->filled('min_percent') ? (float) $request->min_percent : self::DEFAULT_MIN_PERCENT;

        return max(0, min(100, $value));
    }

    private function formatPercent(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1), '0'), '.');
    }

    private function applyCourse(Collection $rows, Request $request): Collection
    {
        return $request->filled('course_id')
            ? $rows->where('course_id', (int) $request->course_id)->values()
            : $rows;
    }

    /** Distinct students with an NVQ payment in the range (before the % filter). */
    private function paidStudentCount(Carbon $from, Carbon $to, Request $request): int
    {
        return $this->paymentsQuery($from, $to)
            ->when($request->filled('branch_id'), function ($q) use ($request) {
                $q->whereExists(function ($e) use ($request) {
                    $e->select(DB::raw(1))
                        ->from('student_courses as sc')
                        ->whereColumn('sc.student_id', 'f.student_id')
                        ->where(fn($w) => $w->whereColumn('sc.course_id', 'pc.course_id')->orWhereColumn('sc.change_course_id', 'pc.course_id'))
                        ->where('sc.branch_id', $request->branch_id);
                });
            })
            ->distinct()
            ->count('f.student_id');
    }

    private function paymentsQuery(Carbon $from, Carbon $to)
    {
        return DB::table('invoices_payments as p')
            ->join('fee_invoices as f', 'f.id', '=', 'p.invoice_id')
            ->join('paid_courses as pc', 'pc.invoice_id', '=', 'f.id')
            ->where('f.invoice_type', 'nvq')
            ->where(fn($q) => $q->whereNull('f.payment_status')->orWhere('f.payment_status', '!=', 'returned'))
            ->where(fn($q) => $q->whereNull('p.payment_status')->orWhere('p.payment_status', '!=', 'refunded'))
            ->where('p.amount', '>', 0)
            ->whereBetween('p.created_at', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')]);
    }

    private function buildRows(Request $request, Carbon $from, Carbon $to, bool $forExport): Collection
    {
        // 1) NVQ payments in the range, per student + NVQ course.
        $payments = $this->paymentsQuery($from, $to)
            ->groupBy('f.student_id', 'pc.course_id')
            ->select(
                'f.student_id',
                'pc.course_id',
                DB::raw('SUM(p.amount) as paid'),
                DB::raw('COUNT(p.id) as payments'),
                DB::raw('MAX(p.created_at) as last_paid_at'),
                DB::raw("GROUP_CONCAT(DISTINCT COALESCE(NULLIF(p.receipt_number, ''), NULLIF(p.payment_invoice_number, ''), f.invoice_number) ORDER BY p.id SEPARATOR ', ') as receipts")
            )
            ->get();

        if ($payments->isEmpty()) {
            return collect();
        }

        $studentIds = $payments->pluck('student_id')->unique()->values();
        $nvqCourseIds = $payments->pluck('course_id')->unique()->values();

        // 2) The enrolment each payment belongs to (original or changed course),
        //    skipping returned enrolments; latest enrolment wins.
        $enrolments = DB::table('student_courses as sc')
            ->join('students as s', 's.id', '=', 'sc.student_id')
            ->join('courses as c', 'c.id', '=', DB::raw('COALESCE(sc.change_course_id, sc.course_id)'))
            ->leftJoin('intakes as i', 'i.id', '=', 'sc.intake_id')
            ->leftJoin('branches as b', 'b.id', '=', 'sc.branch_id')
            ->whereIn('sc.student_id', $studentIds)
            ->where(fn($q) => $q->whereIn('sc.course_id', $nvqCourseIds)->orWhereIn('sc.change_course_id', $nvqCourseIds))
            ->where(fn($q) => $q->whereNull('sc.status')->orWhere('sc.status', '!=', 'return'))
            ->when($request->filled('branch_id'), fn($q) => $q->where('sc.branch_id', $request->branch_id))
            ->select(
                'sc.id', 'sc.student_id', 'sc.course_id', 'sc.change_course_id', 'sc.intake_id', 'sc.status',
                's.admission_number', 's.student_name', 's.national_id_number', 's.phone_number',
                'c.id as display_course_id', 'c.name as course_name',
                'i.name as intake_name', 'i.start_date as intake_start', 'b.name as branch_name'
            )
            ->orderByDesc('sc.id')
            ->get();

        if ($enrolments->isEmpty()) {
            return collect();
        }

        // 3) Practical subjects assigned per course + intake (attendance is
        //    recorded against the enrolment's original course_id).
        $courseIds = $enrolments->pluck('course_id')->unique()->values();
        $assignments = DB::table('assign_subjects as a')
            ->join('subjects as sub', 'sub.id', '=', 'a.subject_id')
            ->whereIn('a.course_id', $courseIds)
            ->where('sub.type', 'P')
            ->select('a.course_id', 'a.intake_id', 'a.subject_id', 'a.practical_time')
            ->get()
            ->groupBy(fn($a) => $a->course_id . '|' . $a->intake_id);

        // 4) Practice minutes done per student/course/subject — closed
        //    practical sessions, time-of-day difference wrapping past
        //    midnight (same as the Practical Card / NVQ Payment report).
        $minutes = DB::table('subject_attendance_sessions')
            ->whereIn('student_id', $studentIds)
            ->whereIn('course_id', $courseIds)
            ->whereNull('checkpoint_type')
            ->whereNotNull('subject_id')
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->groupBy('student_id', 'course_id', 'subject_id')
            ->select(
                'student_id', 'course_id', 'subject_id',
                DB::raw('SUM(FLOOR(MOD(TIME_TO_SEC(TIME(time_out)) - TIME_TO_SEC(TIME(time_in)) + 86400, 86400) / 60)) as mins')
            )
            ->get()
            ->keyBy(fn($m) => $m->student_id . '|' . $m->course_id . '|' . $m->subject_id);

        $minPercent = $this->minPercent($request);
        $rows = collect();
        $seen = [];

        foreach ($payments as $pay) {
            $enrolment = $enrolments->first(function ($e) use ($pay) {
                return (int) $e->student_id === (int) $pay->student_id
                    && ((int) $e->course_id === (int) $pay->course_id
                        || (int) $e->change_course_id === (int) $pay->course_id);
            });

            if (!$enrolment) {
                continue; // other branch (filter) or returned enrolment
            }

            if (isset($seen[$enrolment->id])) {
                // Two NVQ invoices for the same enrolment — add them up.
                $idx = $seen[$enrolment->id];
                $row = $rows[$idx];
                $row['nvq_paid_in_range'] += (float) $pay->paid;
                $row['payments_in_range'] += (int) $pay->payments;
                $row['last_paid_at'] = max($row['last_paid_at'], $pay->last_paid_at);
                $row['receipts'] = trim($row['receipts'] . ', ' . $pay->receipts, ', ');
                $rows[$idx] = $row;
                continue;
            }

            $assigned = $assignments->get($enrolment->course_id . '|' . $enrolment->intake_id, collect())->unique('subject_id');

            $required = $done = $subjectsDone = 0;
            foreach ($assigned as $a) {
                $planned = $this->plannedMinutes($a->practical_time);
                $mins = (int) optional($minutes->get($enrolment->student_id . '|' . $enrolment->course_id . '|' . $a->subject_id))->mins;
                $required += $planned;
                $done += $planned > 0 ? min($mins, $planned) : 0;
                if ($planned > 0 && $mins >= $planned) {
                    $subjectsDone++;
                }
            }

            $percent = $required > 0 ? round(($done / $required) * 100, 1) : 0.0;

            if ($percent < $minPercent) {
                continue;
            }

            $seen[$enrolment->id] = $rows->count();
            $rows->push([
                'enrolment_id' => $enrolment->id,
                'student_id' => $enrolment->student_id,
                'admission_number' => $enrolment->admission_number,
                'student_name' => $enrolment->student_name,
                'nic' => $enrolment->national_id_number,
                'phone' => $enrolment->phone_number,
                'course_id' => (int) $enrolment->display_course_id,
                'course_name' => $enrolment->course_name,
                'intake_name' => $enrolment->intake_name ?? '-',
                'intake_start' => $enrolment->intake_start,
                'branch_name' => $enrolment->branch_name ?? '-',
                'status' => $enrolment->status,
                'practical_done' => $subjectsDone,
                'practical_total' => $assigned->count(),
                'practice_done_min' => $done,
                'practice_required_min' => $required,
                'practice_done' => $this->hhmm($done),
                'practice_required' => $this->hhmm($required),
                'percent' => $percent,
                'nvq_paid_in_range' => (float) $pay->paid,
                'payments_in_range' => (int) $pay->payments,
                'last_paid_at' => $pay->last_paid_at,
                'receipts' => (string) $pay->receipts,
            ]);
        }

        return $rows->sortBy([['course_name', 'asc'], ['admission_number', 'asc']])->values();
    }

    private function plannedMinutes($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (preg_match('/^(\d{1,3}):(\d{2})$/', trim((string) $value), $m)) {
            return ((int) $m[1] * 60) + (int) $m[2];
        }

        return 0;
    }

    private function hhmm(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}

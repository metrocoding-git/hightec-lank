<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PaidCourse;
use App\Models\InvoicesPayments;
use App\Models\ItemIssueItem;
use App\Models\ItemSellItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{


    public function sellItems()
    {
        $itemSells = ItemSellItem::with(['item', 'item.category', 'itemSellBatch.student', 'itemSellBatch.user', 'itemSellBatch', 'itemSellBatch.role', 'itemSellBatch.student.studentCategory', 'itemSellBatch.createdBy'])->get();
        return view('pages.reports.sell-items', compact('itemSells'));
    }

    public function issueItems()
    {
        $itemIssues = ItemIssueItem::with([
            'item',
            'item.category',
            'batch.student',
            'batch.intake',
            'batch',
            'batch.course',
            'batch.student.studentCategory',
            'batch.createdBy'
        ])->get();

        // Add expiration data
        $itemIssues = $itemIssues->map(function ($issue) {

            $course = $issue->batch->course;
            $intake = $issue->batch->intake;

            if ($course && $intake) {
                $startDate = Carbon::parse($intake->start_date);
                $expirationDays = $course->expiration_date;

                if ($expirationDays && is_numeric($expirationDays)) {
                    $expireDate = $startDate->copy()->addDays((int)$expirationDays);
                    $isExpired = $expireDate->isPast();

                    $issue->expire_status = $isExpired ? 'Expired' : 'Not Expired';
                    $issue->expire_class = $isExpired ? 'bg-danger' : 'bg-success';
                    $issue->expire_date = $expireDate->format('Y-m-d');
                } else {
                    $issue->expire_status = 'No Expiration';
                    $issue->expire_class = 'bg-info';
                    $issue->expire_date = null;
                }
            } else {
                $issue->expire_status = 'No Course';
                $issue->expire_class = 'bg-secondary';
                $issue->expire_date = null;
            }

            return $issue;
        });

        return view('pages.reports.issue-items', compact('itemIssues'));
    }


    public function studentsPaid(Request $request)
    {
        $branches = Branch::all();

        $query = InvoicesPayments::with([
            'invoice',
            'invoice.createUser',
            'invoice.branch',
            'invoice.student',
            'invoice.student.studentCategory',
            'invoice.invoice_item',
            'invoice.invoice_item.fee_type',
            'invoice.paidCourses',
            'invoice.paidCourses.course',
            'branch',
        ]);

        // ----- DATE RANGE FILTER -----
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);

            $from = $dates[0] ?? null;
            $to = $dates[1] ?? null;

            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
        }

        // ----- PAYMENT METHOD FILTER -----
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // ----- BRANCH FILTER -----
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Results
        $feeInvoices = $query->orderBy('created_at', 'DESC')
            ->orderByRaw("CAST(SUBSTRING_INDEX(receipt_number, '-', -1) AS UNSIGNED) DESC")
            ->get();

        return view('pages.reports.students-paid', compact('feeInvoices', 'branches'));
    }

    public function coursePaid()
    {
        $paidCourses = PaidCourse::with(relations: ['course', 'invoice.student'])->get();
        return view('pages.reports.course-paid', compact('paidCourses'));
    }

    public function courseIntakePayments(Request $request)
    {
        $branches = Branch::all();

        // Default date range: last 30 days
        $to = now()->format('Y-m-d');
        $from = now()->subDays(30)->format('Y-m-d');

        // If user selects date range
        if ($request->date_range) {

            if (strpos($request->date_range, ' to ') !== false) {
                // Range
                $dates = explode(' to ', $request->date_range);
                $from = $dates[0] ?? null;
                $to = $dates[1] ?? null;
            } else {
                // Single date
                $from = $request->date_range;
                $to = $request->date_range;
            }
        }

        $subQuery = DB::table('student_courses')
            ->select(DB::raw('MIN(id) as id'))
            ->groupBy('student_id', 'course_id');

        $query = DB::table('student_courses')
            ->joinSub($subQuery, 'sc_min', function ($join) {
                $join->on('student_courses.id', '=', 'sc_min.id');
            })

            ->join('intakes', 'student_courses.intake_id', '=', 'intakes.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->join('paid_courses', function ($join) {
                $join->on('student_courses.course_id', '=', 'paid_courses.course_id');
            })
            ->join('fee_invoices', function ($join) use ($request) {
                $join->on('paid_courses.invoice_id', '=', 'fee_invoices.id')
                    ->whereColumn('fee_invoices.student_id', 'student_courses.student_id');

                if ($request->payment_status) {
                    $join->where('fee_invoices.payment_status', $request->payment_status);
                } else {
                    $join->whereIn('fee_invoices.payment_status', ['full', 'partial']);
                }
            })
            ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('student_categories', 'students.student_category_id', '=', 'student_categories.id')
            ->whereBetween('intakes.start_date', [$from, $to])
            ->whereIn('student_courses.status', ['start', 'Complete'])
            ->where('fee_invoices.invoice_type', 'course');

        // Optional branch filter
        if ($request->branch_id) {
            $query->where('fee_invoices.branch_id', $request->branch_id);
        }

        // Select required columns, distinct invoice per student-course
        $reportData = $query
            ->select(
                'fee_invoices.main_invoice_number',
                'students.admission_number',
                'student_categories.category_name as student_category_name',
                'branches.name as branch_name',
                'courses.name as course_name',
                'intakes.name as intake_name',
                'intakes.start_date as intake_start_date',
                'student_courses.status',
                'fee_invoices.total_amount',
                'fee_invoices.paid_amount',
                'fee_invoices.payment_method',
                'fee_invoices.create_date as invoice_create_date',
                'fee_invoices.updated_at as invoice_paid_date'
            )
            ->distinct('fee_invoices.id')
            ->get();

        return view('pages.reports.course-intake-payments', compact('reportData', 'branches'));
    }
}

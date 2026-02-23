<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\PaidCourse;
use App\Models\ItemSell;
use App\Models\ItemIssueBatch;
use App\Models\Fee_invoices;
use App\Models\Invoices_payments;
use App\Models\ItemIssueItem;
use App\Models\ItemSellBatch;
use App\Models\ItemSellItem;
use App\Models\StudentCourse;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $query = Invoices_payments::with([
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
    
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $from = $dates[0] ?? $from;
            $to = $dates[1] ?? $to;
        }
    
        // Load Paid Courses with invoice, payments, and course
        $query = PaidCourse::with([
            'invoice',
            'invoice.payments',
            'course.intakes',
        ]);
    
        // Filter by branch
        if ($request->branch_id) {
            $query->whereHas('invoice', function($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }
    
        $paidCourses = $query->get();
    
        $reportData = [];
    
        foreach ($paidCourses as $paidCourse) {
            $studentId = $paidCourse->invoice->student_id ?? null;
            $courseId = $paidCourse->course_id;
    
            // ✅ Only consider student_courses with status 'start' or 'Complete'
            $studentCourse = \App\Models\StudentCourse::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->whereIn('status', ['start', 'Complete'])
                ->first();
    
            if (!$studentCourse) {
                continue; // Skip any student that is not enrolled or pending
            }
    
            $status = $studentCourse->status;
    
            // Calculate net payment (including negative adjustments)
            $netPayment = PaidCourse::where('invoice_id', $paidCourse->invoice_id)
                ->where('course_id', $courseId)
                ->sum('paid_amount');
    
            foreach ($paidCourse->course->intakes as $intake) {
                // Check if intake falls within selected date range
                if ($intake->start_date >= $from && $intake->start_date <= $to) {
                    $reportData[] = [
                        'student' => $paidCourse->invoice->student->admission_number ?? 'N/A',
                        'branch' => $paidCourse->invoice->branch->name ?? 'N/A',
                        'course' => $paidCourse->course->name ?? 'N/A',
                        'intake_start_date' => $intake->start_date,
                        'status' => ucfirst($status), // Start or Complete
                        'payment_amount' => $netPayment,
                        'payment_method' => $paidCourse->payment_method,
                        'payment_date' => $paidCourse->created_at->format('Y-m-d'),
                    ];
                }
            }
        }
    
        return view('pages.reports.course-intake-payments', compact('reportData', 'branches'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\Intake;
use App\Models\InvoicePaymentReceipt;
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
            'invoice.studentCategory'
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

        // ----- PAYMENT TYPE FILTER -----
        if ($request->payment_type) {

            $query->whereHas('invoice', function ($q) use ($request) {

                if ($request->payment_type == 'course_fee') {
                    $q->whereIn('invoice_type', ['course', 'fee']);
                } else {
                    $q->where('invoice_type', $request->payment_type);
                }
            });
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

    public function studentsDatesPayments(Request $request)
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



        $query = DB::table('student_courses')

            ->join('intakes', 'student_courses.intake_id', '=', 'intakes.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->join('paid_courses', function ($join) {
                $join->on('student_courses.course_id', '=', 'paid_courses.course_id');
            })
            ->join('fee_invoices', function ($join) use ($request) {
                $join->on('paid_courses.invoice_id', '=', 'fee_invoices.id')
                    ->whereColumn('fee_invoices.student_id', 'student_courses.student_id');

                $join->where('fee_invoices.payment_status', 'full');
            })
            ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('student_categories', 'students.student_category_id', '=', 'student_categories.id')
            ->leftJoin('student_categories as course_categories', 'student_courses.student_category_id', '=', 'course_categories.id')

            // ->where(function ($q) use ($from, $to) {

            //     // Condition 1: Intake starts within range
            //     $q->whereBetween('intakes.start_date', [$from, $to])

            //         // OR Condition 2: Payment completed within range
            //         ->orWhere(function ($q2) use ($from, $to) {
            //             $q2->where('fee_invoices.payment_status', 'full')
            //                 ->whereBetween('fee_invoices.updated_at', [$from, $to]);
            //         });
            // })
            ->where(function ($q) use ($from, $to) {
                $q->where(function ($q1) use ($from, $to) {
                    $q1->whereBetween('intakes.start_date', [$from, $to])
                        ->where('fee_invoices.payment_status', 'full');
                })

                    ->orWhere(function ($q2) use ($from, $to) {
                        $q2->whereBetween('fee_invoices.updated_at', [$from, $to])
                            ->where('fee_invoices.payment_status', 'full')
                            ->where('intakes.start_date', '<', $from);
                    });
            })
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
                'fee_invoices.updated_at as invoice_paid_date',
                'course_categories.category_name as course_category_name'

            )
            ->distinct('fee_invoices.id')
            ->get();

        return view('pages.reports.course-dates-payments', compact('reportData', 'branches'));
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



        $query = DB::table('student_courses')
            ->join('intakes', 'student_courses.intake_id', '=', 'intakes.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->join('paid_courses', function ($join) {
                $join->on('student_courses.course_id', '=', 'paid_courses.course_id');
            })
            ->join('fee_invoices', function ($join) use ($request) {
                $join->on('paid_courses.invoice_id', '=', 'fee_invoices.id')
                    ->on('fee_invoices.student_id', 'student_courses.student_id');

                if ($request->payment_status) {
                    $join->where('fee_invoices.payment_status', $request->payment_status);
                } else {
                    $join->whereIn('fee_invoices.payment_status', ['full', 'partial']);
                }
            })
            ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
            ->join('students', 'student_courses.student_id', '=', 'students.id')
            ->join('student_categories', 'students.student_category_id', '=', 'student_categories.id')
            ->leftJoin('student_categories as course_categories', 'student_courses.student_category_id', '=', 'course_categories.id')
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
                'fee_invoices.updated_at as invoice_paid_date',
                'course_categories.category_name as course_category_name'
            )
            ->distinct('fee_invoices.id')
            ->get();

        return view('pages.reports.course-intake-payments', compact('reportData', 'branches'));
    }

    public function studentsIntakeBalance(Request $request)
    {
        $branches = Branch::all();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();

        // Default date range
        $to = now()->format('Y-m-d');
        $from = now()->subDays(30)->format('Y-m-d');

        if ($request->date_range) {
            if (strpos($request->date_range, ' to ') !== false) {
                $dates = explode(' to ', $request->date_range);
                $from = $dates[0] ?? null;
                $to = $dates[1] ?? null;
            } else {
                $from = $request->date_range;
                $to = $request->date_range;
            }
        }

        $paymentSub = DB::table('invoices_payments')
            ->select(
                'invoice_id',
                DB::raw('SUM(amount) as paid_amount')
            )
            ->whereDate('created_at', '<=', $to)
            ->groupBy('invoice_id');

        // $query = DB::table('student_courses')
        //     ->join('intakes', 'student_courses.intake_id', '=', 'intakes.id')
        //     ->join('courses', 'student_courses.course_id', '=', 'courses.id')
        //     ->join('students', 'student_courses.student_id', '=', 'students.id')

        //     ->join('fee_invoices', function ($join) {
        //         $join->on('students.id', '=', 'fee_invoices.student_id')
        //             ->where('fee_invoices.payment_status', '!=', 'returned')
        //             ->where('fee_invoices.invoice_type', 'course');
        //     })

        //     ->join('paid_courses', function ($join) {
        //         $join->on('fee_invoices.id', '=', 'paid_courses.invoice_id')
        //             ->on('paid_courses.course_id', '=', 'student_courses.course_id');
        //     })

        //     ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')

        //     ->whereIn('student_courses.status', ['start', 'Complete'])
        //     ->whereBetween('intakes.start_date', [$from, $to]);


        $query = DB::table('student_courses')
            ->join('intakes', 'student_courses.intake_id', '=', 'intakes.id')
            ->join('courses', 'student_courses.course_id', '=', 'courses.id')
            ->join('students', 'student_courses.student_id', '=', 'students.id')

            ->join('fee_invoices', function ($join) {
                $join->on('students.id', '=', 'fee_invoices.student_id')
                    ->where('fee_invoices.payment_status', '!=', 'returned');
            })

            ->join('paid_courses', function ($join) {
                $join->on('fee_invoices.id', '=', 'paid_courses.invoice_id')
                    ->on('paid_courses.course_id', '=', 'student_courses.course_id');
            })

            ->leftJoinSub($paymentSub, 'payments', function ($join) {
                $join->on('fee_invoices.id', '=', 'payments.invoice_id');
            })

            ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')

            ->whereIn('student_courses.status', ['start', 'Complete'])
            ->whereDate('intakes.start_date', '<=', $to)
            // ->whereBetween('intakes.start_date', [$from, $to])
            ->where('fee_invoices.invoice_type', 'course');

        if ($request->intake_id) {
            $query->where('intakes.id', $request->intake_id);
        }

        if ($request->branch_id) {
            $query->where('branches.id', $request->branch_id);
        }


        $reportData = $query
            ->select(
                'courses.name as course_name',
                'intakes.name as intake_name',

                DB::raw('SUM(fee_invoices.total_amount) as total_amount'),

                DB::raw('SUM(COALESCE(payments.paid_amount,0)) as paid_amount'),

                DB::raw('
            SUM(fee_invoices.total_amount) 
            - SUM(COALESCE(payments.paid_amount,0)) 
            as balance_amount
        ')
            )
            ->groupBy('courses.id', 'courses.name', 'intakes.name')
            ->orderBy('courses.name')
            ->get();

        // $reportData = $query
        //     ->select(
        //         'courses.name as course_name',
        //         'intakes.name as intake_name',

        //         DB::raw('COUNT(DISTINCT fee_invoices.id) as invoice_count'),

        //         DB::raw('SUM(fee_invoices.total_amount) as total_amount'),

        //         DB::raw('SUM(fee_invoices.paid_amount) as paid_amount'),

        //         DB::raw('SUM(fee_invoices.total_amount - fee_invoices.paid_amount) as balance_amount')
        //     )
        //     ->groupBy('courses.id', 'courses.name', 'intakes.name')
        //     ->orderBy('courses.name')
        //     ->get();

        return view('pages.reports.students-intake-balance', compact('reportData', 'branches', 'intakes'));
    }


    public function changeCourse(Request $request)
    {
        $data = DB::table('student_courses as sc')
            ->join('students', 'sc.student_id', '=', 'students.id')
            ->join('courses as old_course', 'sc.course_id', '=', 'old_course.id')
            ->join('courses as new_course', 'sc.change_course_id', '=', 'new_course.id')
            ->join('intakes', 'sc.intake_id', '=', 'intakes.id')
            ->leftJoin('users', 'sc.updated_by', '=', 'users.id')
            ->leftJoin('branches', 'sc.branch_id', '=', 'branches.id')

            ->whereNotNull('sc.change_course_id')

            ->select(
                'students.admission_number',
                'students.student_name as student_name',
                'old_course.name as old_course_name',
                'new_course.name as new_course_name',
                'intakes.name as intake_name',
                'branches.name as branch_name',
                'sc.updated_at as changed_date',
                'users.name as changed_by'
            )
            ->orderBy('sc.updated_at', 'desc')
            ->get();

        return view('pages.reports.change-course', compact('data'));
    }

    public function invoicePayments(Request $request)
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



        // $query = DB::table('invoice_payment_receipts')
        //     ->join('invoice_payment_receipt_items', 'invoice_payment_receipts.id', '=', 'invoice_payment_receipt_items.receipt_id')
        //     ->join('invoices_payments', 'invoice_payment_receipt_items.invoice_payment_id', '=', 'invoices_payments.id')
        //     ->join('users', 'invoice_payment_receipts.created_by', '=', 'users.id')
        //     ->join('fee_invoices', 'invoices_payments.invoice_id', '=', 'fee_invoices.id')
        //     ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
        //     ->whereBetween('invoice_payment_receipts.created_at', [$from, $to]);

        $query = InvoicePaymentReceipt::with([
            'items.payment.invoice.student',
            'items.payment.invoice.branch',
            'user',
        ])->whereBetween(DB::raw('DATE(invoice_payment_receipts.created_at)'), [$from, $to]);

        if ($request->branch_id) {
            $query->whereHas('items.payment.invoice.branch', function ($q) use ($request) {
                $q->where('id', $request->branch_id);
            });
        }


        // Select required columns, distinct invoice per student-course
        $reportData = $query->get();

        return view('pages.reports.invoice-payments', compact('reportData', 'branches'));
    }


    public function foodPaymentReport(Request $request)
    {
        $branches = Branch::all();
        $courses  = Course::where('has_food_fee', 1)->orderBy('name')->get();

        $query = \App\Models\FoodPayment::with([
            'invoice',
            'invoice.student',
            'invoice.student.studentCategory',
            'invoice.createUser',
            'invoice.branch',
            'course',
        ]);

        // ----- BRANCH -----
        if ($request->branch_id) {
            $query->whereHas('invoice', fn($q) => $q->where('branch_id', $request->branch_id));
        }

        // ----- COURSE -----
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // ----- LUNCHBOX LETTER (group) -----
        if ($request->letter) {
            $query->where('lunchbox_number', 'LIKE', $request->letter . '-%');
        }

        // ----- MEAL FILTER -----
        if ($request->meal) {
            if ($request->meal === 'breakfast') $query->where('has_breakfast', 1);
            if ($request->meal === 'lunch')     $query->where('has_lunch', 1);
            if ($request->meal === 'dinner')    $query->where('has_dinner', 1);
        }

        // ----- DATE RANGE (food from_date) -----
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $from  = $dates[0] ?? null;
            $to    = $dates[1] ?? ($dates[0] ?? null);
            if ($from) $query->whereDate('from_date', '>=', $from);
            if ($to)   $query->whereDate('from_date', '<=', $to);
        }

        // ----- MONTH / YEAR (from lunchbox_sequences via from_date) -----
        if ($request->month) {
            $query->whereMonth('from_date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('from_date', $request->year);
        }

        // ----- PAYMENT STATUS -----
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $foodPayments = $query->orderBy('lunchbox_number')->get();

        // Group by: year → month → branch → letter
        $grouped = $foodPayments->groupBy([
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('Y'),
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('m'),
            fn($r) => optional($r->invoice->branch)->name ?? 'N/A',
            fn($r) => substr($r->lunchbox_number ?? '-', 0, 1),
        ]);

        // Available letters for filter dropdown
        $letters = range('A', 'Z');

        return view('pages.reports.food-payment-report', compact(
            'foodPayments',
            'grouped',
            'branches',
            'courses',
            'letters'
        ));
    }
}

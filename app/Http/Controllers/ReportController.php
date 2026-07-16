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

        // Excel export request
        if ($request->get('export') === 'excel') {
            return $this->exportStudentsPaidExcel($request);
        }

        if ($request->ajax() && $request->has('draw')) {
            return $this->getStudentsPaidData($request);
        }

        return view('pages.reports.students-paid', compact('branches'));
    }

    private function exportStudentsPaidExcel(Request $request)
    {
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
            'invoice.foodPayment',
            'branch',
            'invoice.studentCategory'
        ]);

        // Apply same filters
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            if (!empty($dates[0])) $query->whereDate('created_at', '>=', $dates[0]);
            if (!empty($dates[1])) $query->whereDate('created_at', '<=', $dates[1]);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->payment_type) {
            $query->whereHas('invoice', function ($q) use ($request) {
                if ($request->payment_type == 'course_fee') {
                    $q->whereIn('invoice_type', ['course', 'fee']);
                } else {
                    $q->where('invoice_type', $request->payment_type);
                }
            });
        }

        $records = $query->orderBy('created_at', 'asc')->get();

        // Build CSV in memory and stream as .xlsx-compatible file
        $filename = 'Students-Payment-Report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'No',
            'Stu No',
            'Stu Category',
            'Paid Branch',
            'Invoice Number',
            'Receipt Number',
            'Payment Name',
            'Paid Amount',
            'Payment Method',
            'Payment Date',
            'Payment Invoice Number',
            'Bank Payment Amount',
            'Bank Payment Date',
            'Note',
            'Create User',
            'Invoice Payment Status',
            'Invoice Payment Amount',
            'Lunch Box Number',
        ];

        $callback = function () use ($records, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($records as $key => $row) {
                fputcsv($handle, [
                    $key + 1,
                    $row->invoice?->student?->admission_number ?? 'N/A',
                    $row->invoice?->studentCategory?->category_name
                        ?? $row->invoice?->student?->studentCategory?->category_name
                        ?? 'N/A',
                    $row->branch?->name ?? 'N/A',
                    $row->invoice?->main_invoice_number ?? 'N/A',
                    $row->receipt_number ?? 'N/A',
                    $this->getPaymentName($row),
                    number_format((float) $row->amount, 2, '.', ''),
                    $row->payment_method,
                    $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('m/d/Y') : 'N/A',
                    $row->payment_invoice_number ?? '-',
                    $row->bank_payment_amount ?? '-',
                    $row->bank_payment_date
                        ? \Carbon\Carbon::parse($row->bank_payment_date)->format('m/d/Y')
                        : '-',
                    $row->notes ?? '-',
                    $row->invoice?->createUser?->name ?? 'N/A',
                    $this->getInvoicePaymentStatus($row),
                    $row->payment_method == 'Invoice' ? $row->invoice_payment_amount : '-',
                    $row->payment_type === 'food'
                        ? ($row->invoice?->foodPayment?->lunchbox_number ?? '-')
                        : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStudentsPaidData(Request $request)
    {
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
            'invoice.foodPayment',
            'branch',
            'invoice.studentCategory'
        ]);

        // ----- DATE RANGE FILTER -----
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $from = $dates[0] ?? null;
            $to = $dates[1] ?? $from; // ← if no "to" part, use same date as "from"

            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
        }

        // Check if any FILTER is applied
        $hasFilters = $request->filled('date_range') ||
            $request->filled('payment_method') ||
            $request->filled('branch_id') ||
            $request->filled('payment_type');

        // Check if user is searching (typing in table search box)
        $isSearching = $request->filled('search.value');



        if (!$hasFilters && !$isSearching) {
            $today = now()->startOfDay();
            $query->whereDate('created_at', '>=', $today);
        }

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

        // Get total records before search
        $totalRecords = $query->count();

        // ----- SEARCH FILTER -----
        if ($request->search['value']) {
            $searchTerm = $request->search['value'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('receipt_number', 'like', "%{$searchTerm}%")
                    ->orWhere('payment_invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhere('notes', 'like', "%{$searchTerm}%")
                    ->orWhere('bank_payment_amount', 'like', "%{$searchTerm}%")
                    ->orWhereHas('invoice', function ($invoiceQ) use ($searchTerm) {
                        $invoiceQ->where('main_invoice_number', 'like', "%{$searchTerm}%")
                            ->orWhereHas('student', function ($studentQ) use ($searchTerm) {
                                $studentQ->where('admission_number', 'like', "%{$searchTerm}%")
                                    ->orWhere('first_name', 'like', "%{$searchTerm}%")
                                    ->orWhere('last_name', 'like', "%{$searchTerm}%");
                            });
                    });
            });
        }

        $filteredRecords = $query->count();

        // Sort
        $orderCol = $request->order[0]['column'] ?? 0;
        $orderDir = $request->order[0]['dir'] ?? 'desc';

        if ($orderCol == 0) {
            $query->orderBy('created_at', $orderDir);
        } else {
            $query->orderBy('created_at', 'DESC');
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 25;

        if ($length == -1) {
            // "All" selected — fetch everything without limit
            $feeInvoices = $query->offset($start)->get();
        } else {
            $feeInvoices = $query->offset($start)->limit($length)->get();
        }



        // Format data for DataTable
        $data = [];
        foreach ($feeInvoices as $key => $feeInvoice) {
            $data[] = [
                'id' => $key + 1,
                'stu_no' => $feeInvoice->invoice ? $feeInvoice->invoice->student->admission_number : 'N/A',
                'stu_category' => $feeInvoice->invoice?->studentCategory?->category_name ?? ($feeInvoice->invoice?->student?->studentCategory?->category_name ?? 'N/A'),
                'paid_branch' => $feeInvoice->branch ? $feeInvoice->branch->name : 'N/A',
                'invoice_number' => $feeInvoice->invoice ? $feeInvoice->invoice->main_invoice_number : 'N/A',
                'receipt_number' => $feeInvoice->receipt_number ?? 'N/A',
                'payment_name' => $this->getPaymentName($feeInvoice),
                'paid_amount' => number_format((float) $feeInvoice->amount, 2, '.', ''),
                'payment_method' => $feeInvoice->payment_method,
                'payment_date' => $feeInvoice->created_at ? \Carbon\Carbon::parse($feeInvoice->created_at)->format('m/d/Y') : 'N/A',
                'payment_invoice_number' => $feeInvoice->payment_invoice_number ?? '-',
                'bank_payment_amount' => $feeInvoice->bank_payment_amount ?? '-',
                'bank_payment_date' => $feeInvoice->bank_payment_date ? \Carbon\Carbon::parse($feeInvoice->bank_payment_date)->format('m/d/Y') : '-',
                'notes' => $feeInvoice->notes ?? '-',
                'create_user' => $feeInvoice->invoice && $feeInvoice->invoice->createUser ? $feeInvoice->invoice->createUser->name : 'N/A',
                'invoice_payment_status' => $this->getInvoicePaymentStatus($feeInvoice),
                'invoice_payment_amount' => $feeInvoice->payment_method == 'Invoice' ? $feeInvoice->invoice_payment_amount : '-',
                'box_number' => $feeInvoice->payment_type === 'food'
                    ? ($feeInvoice->invoice?->foodPayment?->lunchbox_number ?? '-')
                    : '-',
                'amount_is_negative' => $feeInvoice->amount < 0,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    private function getPaymentName($feeInvoice)
    {

        if ($feeInvoice->amount < 0) {

            $originalPayment = InvoicesPayments::whereHas('invoice', function ($q) use ($feeInvoice) {
                $q->where('main_invoice_number', $feeInvoice->invoice->main_invoice_number);
            })
                ->where('amount', '>', 0)
                ->orderBy('id', 'asc')
                ->first();

            if ($originalPayment) {

                switch ($originalPayment->payment_type) {
                    case 'food':
                        return 'Food Fee - Refund Payment';

                    case 'course':
                        return 'Course Payment - Refund Payment';

                    case 'nvq':
                        return 'NVQ Payment - Refund Payment';

                    case 'fee':
                        $feeName = $originalPayment->invoice->invoice_item[0]->fee_type->name ?? '';
                        return ($feeName ?: 'Fee') . ' - Refund Payment';

                    case 'item_sell':
                        return 'Item Sell - Refund Payment';

                    default:
                        return ucfirst($originalPayment->payment_type) . ' - Refund Payment';
                }
            }

            return 'Refund Payment';
        }

        if ($feeInvoice->payment_type === 'course') {
            return 'Course Payment - ' . ($feeInvoice->invoice->paidCourses->course->name ?? 'N/A');
        } elseif ($feeInvoice->payment_type === 'nvq') {
            return 'NVQ Payment - ' . ($feeInvoice->invoice->paidCourses->course->name ?? 'N/A');
        } elseif ($feeInvoice->payment_type === 'fee') {
            return 'Fee Payment - ' . ($feeInvoice->invoice->invoice_item[0]->fee_type->name ?? 'N/A');
        } elseif ($feeInvoice->payment_type === 'item_sell') {
            return 'Item Sell';
        } elseif ($feeInvoice->payment_type === 'food') {
            return 'Food Fee - ' . ($feeInvoice->invoice->paidCourses->course->name ?? 'N/A');
        }

        return 'N/A';
    }

    private function getInvoicePaymentStatus($feeInvoice)
    {
        if ($feeInvoice->payment_method == 'Invoice') {
            if ($feeInvoice->invoice_payment_confirm) {
                return 'Completed';
            } elseif ($feeInvoice->payment_invoice_number != null) {
                return 'Pending';
            } else {
                return 'Not Open';
            }
        }
        return '-';
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
        if ($request->filled('date_range')) {

            $dates = explode(' to ', $request->date_range);

            $from = trim($dates[0]);
            $to = trim($dates[1] ?? $from);

            if (empty($to)) {
                $to = $from;
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

                    if ($from == $to) {
                        $q1->whereDate('intakes.start_date', $from);
                    } else {
                        $q1->whereBetween('intakes.start_date', [$from, $to]);
                    }
                    $q1->where('fee_invoices.payment_status', 'full');
                })

                    ->orWhere(function ($q2) use ($from, $to) {
                        if ($from == $to) {
                            $q2->whereDate('fee_invoices.updated_at', $from);
                        } else {
                            $q2->whereBetween('fee_invoices.updated_at', [$from, $to]);
                        }
                        $q2->where('fee_invoices.payment_status', 'full')
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
                'course_categories.category_name as course_category_name',
                DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Cash") as cash_amount'),
                DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Bank Transfer") as bank_amount'),
                DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Invoice") as invoice_amount')

            )
            ->distinct('fee_invoices.id')
            ->get();

        return view('pages.reports.course-dates-payments', compact('reportData', 'branches'));
    }

    public function courseIntakePayments(Request $request)
    {
        $branches = Branch::all();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();
        $reportData = collect();

        // $paymentStatus = 'partial';

        $baseQuery = fn() => DB::table('student_courses')
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
            ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
            ->join('student_categories', 'students.student_category_id', '=', 'student_categories.id')
            ->leftJoin('student_categories as course_categories', 'student_courses.student_category_id', '=', 'course_categories.id')
            ->whereIn('student_courses.status', ['start', 'Complete'])
            ->where('fee_invoices.invoice_type', 'course')
            ->whereRaw('(fee_invoices.total_amount - (SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id)) > 0');
        $selectClause = [
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
            DB::raw('(fee_invoices.total_amount - fee_invoices.paid_amount) as due_amount'), // ← add this

            'fee_invoices.create_date as invoice_create_date',
            'fee_invoices.updated_at as invoice_paid_date',
            'course_categories.category_name as course_category_name',
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Cash") as cash_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Bank Transfer") as bank_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Invoice") as invoice_amount'),
        ];

        // ── MODE B: Intake only ─────────────────────────────────────────
        if ($request->filled('intake_id')) {

            $reportData = $baseQuery()
                ->where('intakes.id', $request->intake_id)
                ->select($selectClause)
                ->distinct('fee_invoices.id')
                ->get();

            // ── MODE A: To Date + Branch ────────────────────────────────────
        } else {

            $to = $request->filled('to_date') ? $request->to_date : now()->format('Y-m-d');

            $query = $baseQuery()
                ->whereDate('intakes.start_date', '<=', $to);

            if ($request->filled('branch_id')) {
                $query->where('fee_invoices.branch_id', $request->branch_id);
            }

            $reportData = $query
                ->select($selectClause)
                ->distinct('fee_invoices.id')
                ->get();
        }

        return view('pages.reports.course-intake-payments', compact('reportData', 'branches', 'intakes'));
    }

    public function studentsIntakeBalance(Request $request)
    {
        $branches = Branch::all();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();
        $reportData = collect();

        $selectClause = [
            'courses.name as course_name',
            'intakes.name as intake_name',
            DB::raw('SUM(fee_invoices.total_amount) as total_amount'),
            DB::raw('SUM(COALESCE(payments.paid_amount, 0)) as paid_amount'),
            DB::raw('SUM(fee_invoices.total_amount) - SUM(COALESCE(payments.paid_amount, 0)) as balance_amount'),
        ];

        // ── MODE 1: Intake filter (ignores date & branch) ──────────────
        if ($request->filled('intake_id')) {

            $paymentSub = DB::table('invoices_payments')
                ->select('invoice_id', DB::raw('SUM(amount) as paid_amount'))
                ->groupBy('invoice_id');

            $reportData = DB::table('student_courses')
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
                ->leftJoinSub($paymentSub, 'payments', fn($j) => $j->on('fee_invoices.id', '=', 'payments.invoice_id'))
                ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
                ->whereIn('student_courses.status', ['start', 'Complete'])
                ->where('fee_invoices.invoice_type', 'course')
                ->where('intakes.id', $request->intake_id)
                ->select($selectClause)
                ->groupBy('courses.id', 'courses.name', 'intakes.name')
                ->orderBy('courses.name')
                ->get();

            // ── MODE 2: To Date + Branch filter (ignores intake) ───────────
        } else {

            $to = $request->filled('to_date') ? $request->to_date : now()->format('Y-m-d');

            $paymentSub = DB::table('invoices_payments')
                ->select('invoice_id', DB::raw('SUM(amount) as paid_amount'))
                ->whereDate('created_at', '<=', $to)
                ->groupBy('invoice_id');

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
                ->leftJoinSub($paymentSub, 'payments', fn($j) => $j->on('fee_invoices.id', '=', 'payments.invoice_id'))
                ->join('branches', 'fee_invoices.branch_id', '=', 'branches.id')
                ->whereIn('student_courses.status', ['start', 'Complete'])
                ->whereDate('intakes.start_date', '<=', $to)
                ->where('fee_invoices.invoice_type', 'course');

            if ($request->filled('branch_id')) {
                $query->where('branches.id', $request->branch_id);
            }

            $reportData = $query
                ->select($selectClause)
                ->groupBy('courses.id', 'courses.name', 'intakes.name')
                ->orderBy('courses.name')
                ->get();
        }

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

        // ----- DATE RANGE FILTER (from_date to to_date range overlap) -----
        $hasFilters = $request->filled('branch_id') ||
            $request->filled('course_id') ||
            $request->filled('letter') ||
            $request->filled('meal') ||
            $request->filled('date_range') ||
            $request->filled('month') ||
            $request->filled('year') ||
            $request->has('status');

        if ($request->filled('date_range')) {
            // User picked an explicit date range in the filter
            $dates     = explode(' to ', $request->date_range);
            $filterFrom = $dates[0] ?? null;
            $filterTo   = $dates[1] ?? ($dates[0] ?? null);

            if ($filterFrom && $filterTo) {
                // Records whose [from_date, to_date] window OVERLAPS the selected range
                $query->whereDate('from_date', '<=', $filterTo)
                    ->whereDate('to_date',   '>=', $filterFrom);
            } elseif ($filterFrom) {
                $query->whereDate('from_date', '<=', $filterFrom)
                    ->whereDate('to_date',   '>=', $filterFrom);
            }
        } elseif (!$hasFilters) {
            // Default: show records whose date window includes today
            $today = today()->toDateString();
            $query->whereDate('from_date', '<=', $today)
                ->whereDate('to_date',   '>=', $today);
        }

        // ----- MONTH / YEAR -----
        if ($request->month) {
            $query->whereMonth('from_date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('from_date', $request->year);
        }

        // ----- PAYMENT STATUS (default active = 1) -----
        $status = $request->input('status', '1');
        if ($status !== '') {
            $query->where('status', $status);
        }

        $foodPayments = $query->orderBy('created_at')->get();

        // Group by: year → month → branch → letter
        $grouped = $foodPayments->groupBy([
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('Y'),
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('m'),
            fn($r) => optional($r->invoice->branch)->name ?? 'N/A',
            fn($r) => substr($r->lunchbox_number ?? '-', 0, 1),
        ]);

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

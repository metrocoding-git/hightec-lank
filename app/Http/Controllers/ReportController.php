<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\FoodPayment;
use App\Models\Intake;
use App\Models\InvoicePaymentReceipt;
use App\Models\PaidCourse;
use App\Models\InvoicesPayments;
use App\Models\ItemIssueItem;
use App\Models\ItemSellItem;
use App\Models\Subject;
use App\Models\SubjectAttendanceSession;
use App\Models\TrainingVehicle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{


    public function sellItems(Request $request)
    {
        $query = $this->applySellItemsFilters(ItemSellItem::query(), $request);

        // Analytics — iterate the filtered collection in PHP (same approach
        // used for Invoice Payments' analytics) rather than a raw join,
        // since the item-sell-batch table name isn't confirmed anywhere in
        // this codebase and a wrong guess there would crash the page.
        $analyticsItems = (clone $query)->with('itemSellBatch')->get();
        $dailyTotals = [];
        $statusTotals = [];
        $grandTotal = 0.0;
        foreach ($analyticsItems as $row) {
            $batch = $row->itemSellBatch;
            $amount = (float) $row->sub_total;
            $grandTotal += $amount;
            if ($batch && $batch->sell_date) {
                $day = \Carbon\Carbon::parse($batch->sell_date)->format('Y-m-d');
                $dailyTotals[$day] = ($dailyTotals[$day] ?? 0) + $amount;
            }
            $status = $this->sellStatusLabel($batch->status ?? null);
            $statusTotals[$status] = ($statusTotals[$status] ?? 0) + $amount;
        }
        ksort($dailyTotals);
        $dailyTrend = collect($dailyTotals)->map(fn($v, $k) => ['date' => $k, 'amount' => round($v, 2)])->values();
        $statusBreakdown = collect($statusTotals)->filter(fn($v) => $v != 0)->map(fn($v, $k) => ['label' => $k, 'value' => round($v, 2)])->values();
        $itemCount = $analyticsItems->count();

        $itemSells = $query
            ->with(['item', 'item.category', 'itemSellBatch.student', 'itemSellBatch.user', 'itemSellBatch', 'itemSellBatch.role', 'itemSellBatch.student.studentCategory', 'itemSellBatch.createdBy'])
            ->orderByDesc('id')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.sell-items', compact('itemSells', 'grandTotal', 'itemCount', 'dailyTrend', 'statusBreakdown'));
    }

    private function sellStatusLabel($status)
    {
        return match ($status) {
            'P' => 'Paid',
            'PP' => 'Partial',
            'U' => 'Unpaid',
            'R' => 'Returned',
            default => 'N/A',
        };
    }

    private function applySellItemsFilters($query, Request $request)
    {
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            $from = trim($dates[0] ?? '');
            $to = trim($dates[1] ?? $from);
            $query->whereHas('itemSellBatch', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('sell_date', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('sell_date', '<=', $to);
                }
            });
        } else {
            $hasOtherFilters = $request->filled('payment_method') || $request->filled('status') || $request->filled('search_text');
            if (!$hasOtherFilters) {
                $query->whereHas('itemSellBatch', function ($q) {
                    $q->whereDate('sell_date', '>=', now()->subDays(30)->startOfDay());
                });
            }
        }

        if ($request->filled('payment_method')) {
            $method = $request->payment_method;
            $query->whereHas('itemSellBatch', function ($q) use ($method) {
                $q->where('payment_method', $method);
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('itemSellBatch', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->whereHas('itemSellBatch', function ($q) use ($term) {
                $q->where('reference_no', 'like', "%{$term}%")
                    ->orWhere('others_name', 'like', "%{$term}%")
                    ->orWhere('invoice_number', 'like', "%{$term}%")
                    ->orWhereHas('student', function ($sq) use ($term) {
                        $sq->where('admission_number', 'like', "%{$term}%")
                            ->orWhere('student_name', 'like', "%{$term}%");
                    });
            });
        }

        return $query;
    }

    public function exportSellItems(Request $request)
    {
        $query = $this->applySellItemsFilters(ItemSellItem::query(), $request)
            ->with(['item', 'item.category', 'itemSellBatch.student', 'itemSellBatch.user', 'itemSellBatch', 'itemSellBatch.role', 'itemSellBatch.student.studentCategory', 'itemSellBatch.createdBy'])
            ->orderBy('id', 'asc');

        $filename = 'Item-Sell-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Reference No',
                'Item',
                'Item Category',
                'Buyer Name',
                'Create User',
                'Reg No',
                'Stu Category',
                'Date',
                'Sell Price',
                'Total Quantity',
                'Grand Total',
                'Total Price',
                'Total Qty',
                'Payment Method',
                'Bank Payment Amount',
                'Bank Payment Date',
                'Invoice Number',
                'Note',
                'Status',
                'Created At',
                'Updated At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $value) {
                $rowNo++;
                $batch = $value->itemSellBatch;
                $buyer = '-';
                if ($batch) {
                    if ($batch->role_id == 2 && $batch->student) {
                        $buyer = $batch->student->student_name;
                    } elseif ($batch->role_id == 0 && $batch->others_name) {
                        $buyer = $batch->others_name;
                    } elseif ($batch->role_id == 3 && $batch->user) {
                        $buyer = $batch->user->name;
                    }
                }
                fputcsv($handle, [
                    $rowNo,
                    $batch->reference_no ?? '-',
                    $value->item->item_name ?? '-',
                    $value->item->category->name ?? '-',
                    $buyer,
                    $batch->createdBy?->name ?? '-',
                    $batch->student->admission_number ?? '-',
                    $batch->student->studentCategory?->category_name ?? '-',
                    $batch->sell_date ? \Carbon\Carbon::parse($batch->sell_date)->format('m/d/Y') : '-',
                    number_format((float) $value->unit_price, 2, '.', ''),
                    $value->quantity,
                    number_format((float) $value->sub_total, 2, '.', ''),
                    number_format((float) ($batch->total_sell_price ?? 0), 2, '.', ''),
                    $batch->total_quantity ?? '-',
                    $batch->payment_method ?? '-',
                    $batch->bank_payment_amount ? number_format((float) $batch->bank_payment_amount, 2, '.', '') : '-',
                    $batch->bank_payment_date ? \Carbon\Carbon::parse($batch->bank_payment_date)->format('m/d/Y') : '-',
                    $batch->invoice_number ?: '-',
                    $batch->description ?? '-',
                    $this->sellStatusLabel($batch->status ?? null),
                    $batch->created_at ? \Carbon\Carbon::parse($batch->created_at)->format('Y-m-d h:i A') : '-',
                    $batch->updated_at ? \Carbon\Carbon::parse($batch->updated_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    // ARCHITECTURE (this pass): same conversion as sellItems() above —
    // filter form + ->paginate() + inline analytics + dedicated cursor export.
    public function issueItems(Request $request)
    {
        $courses = Course::orderBy('name')->get();
        $intakes = Intake::orderBy('name')->get();

        $query = $this->applyIssueItemsFilters(ItemIssueItem::query(), $request);

        $analyticsItems = (clone $query)->with('batch')->get();
        $dailyCounts = [];
        $statusCounts = [];
        foreach ($analyticsItems as $row) {
            $batch = $row->batch;
            if ($batch && $batch->issue_date) {
                $day = \Carbon\Carbon::parse($batch->issue_date)->format('Y-m-d');
                $dailyCounts[$day] = ($dailyCounts[$day] ?? 0) + 1;
            }
            $status = ($batch->issue_status ?? null) == 'I' ? 'Issued' : 'Returned';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }
        ksort($dailyCounts);
        $dailyTrend = collect($dailyCounts)->map(fn($v, $k) => ['date' => $k, 'count' => $v])->values();
        $statusBreakdown = collect($statusCounts)->map(fn($v, $k) => ['label' => $k, 'value' => $v])->values();
        $itemCount = $analyticsItems->count();

        $itemIssues = $query
            ->with([
                'item',
                'item.category',
                'batch.student',
                'batch.intake',
                'batch',
                'batch.course',
                'batch.student.studentCategory',
                'batch.createdBy'
            ])
            ->orderByDesc('id')
            ->paginate(50)
            ->appends($request->query())
            ->through(function ($issue) {
                $course = $issue->batch->course ?? null;
                $intake = $issue->batch->intake ?? null;

                if ($course && $intake) {
                    $startDate = Carbon::parse($intake->start_date);
                    $expirationDays = $course->expiration_date;

                    if ($expirationDays && is_numeric($expirationDays)) {
                        $expireDate = $startDate->copy()->addDays((int) $expirationDays);
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

        return view('pages.reports.issue-items', compact('itemIssues', 'itemCount', 'dailyTrend', 'statusBreakdown', 'courses', 'intakes'));
    }

    private function applyIssueItemsFilters($query, Request $request)
    {
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            $from = trim($dates[0] ?? '');
            $to = trim($dates[1] ?? $from);
            $query->whereHas('batch', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('issue_date', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('issue_date', '<=', $to);
                }
            });
        } else {
            $hasOtherFilters = $request->filled('issue_status') || $request->filled('course_id') || $request->filled('intake_id') || $request->filled('search_text');
            if (!$hasOtherFilters) {
                $query->whereHas('batch', function ($q) {
                    $q->whereDate('issue_date', '>=', now()->subDays(30)->startOfDay());
                });
            }
        }

        if ($request->filled('issue_status')) {
            $status = $request->issue_status;
            $query->whereHas('batch', function ($q) use ($status) {
                $q->where('issue_status', $status);
            });
        }

        if ($request->filled('course_id')) {
            $courseId = $request->course_id;
            $query->whereHas('batch', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($request->filled('intake_id')) {
            $intakeId = $request->intake_id;
            $query->whereHas('batch', function ($q) use ($intakeId) {
                $q->where('intake_id', $intakeId);
            });
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->whereHas('batch.student', function ($q) use ($term) {
                $q->where('admission_number', 'like', "%{$term}%")
                    ->orWhere('student_name', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    public function exportIssueItems(Request $request)
    {
        $query = $this->applyIssueItemsFilters(ItemIssueItem::query(), $request)
            ->with([
                'item',
                'item.category',
                'batch.student',
                'batch.intake',
                'batch',
                'batch.course',
                'batch.student.studentCategory',
                'batch.createdBy'
            ])
            ->orderBy('id', 'asc');

        $filename = 'Item-Issue-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Item Name',
                'Item Category',
                'Issued To',
                'Reg No',
                'Category',
                'Issue Date',
                'Quantity',
                'Intake Name',
                'Course Name',
                'Create User',
                'Course Expiration',
                'Note',
                'Status',
                'Created At',
                'Updated At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $batchData) {
                $rowNo++;
                $batch = $batchData->batch;
                $course = $batch->course ?? null;
                $intake = $batch->intake ?? null;
                $expireStatus = 'No Course';
                if ($course && $intake) {
                    $expirationDays = $course->expiration_date;
                    if ($expirationDays && is_numeric($expirationDays)) {
                        $startDate = Carbon::parse($intake->start_date);
                        $expireStatus = $startDate->copy()->addDays((int) $expirationDays)->isPast() ? 'Expired' : 'Not Expired';
                    } else {
                        $expireStatus = 'No Expiration';
                    }
                }

                fputcsv($handle, [
                    $rowNo,
                    $batchData->item->item_name ?? 'No Items',
                    $batchData->item->category->name ?? '-',
                    $batch->student->student_name ?? '-',
                    $batch->student->admission_number ?? '-',
                    $batch->student->studentCategory->category_name ?? '',
                    $batch->issue_date ? date('m/d/Y', strtotime($batch->issue_date)) : '',
                    $batchData->quantity,
                    $batch->intake->name ?? '-',
                    $batch->course->name ?? '-',
                    $batch->createdBy?->name ?? '-',
                    $expireStatus,
                    $batch->description ?? '-',
                    ($batch->issue_status ?? null) == 'I' ? 'Issued' : 'Returned',
                    $batch->created_at ? \Carbon\Carbon::parse($batch->created_at)->format('Y-m-d h:i A') : '-',
                    $batch->updated_at ? \Carbon\Carbon::parse($batch->updated_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }


    // ARCHITECTURE (this pass): rebuilt to match the Student Attendance
    // report's pattern per your request — a real filter form, a plain
    // server-paginated table ("load data page wise"), and a dedicated export
    // that streams EVERY filtered row regardless of what page you're on.
    // Replaces the earlier server-side-DataTable/AJAX version.
    public function studentsPaid(Request $request)
    {
        $branches = Branch::all();

        $query = $this->applyStudentsPaidFilters(InvoicesPayments::query(), $request);

        // Analytics — cheap SQL aggregates over the FULL filtered set (not
        // just the current page), same approach used on Student Attendance.
        $grandTotal = (float) (clone $query)->sum('amount');
        $count = (clone $query)->count();

        $methodBreakdown = (clone $query)
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->map(fn($row) => ['label' => $row->payment_method ?: 'Unknown', 'value' => round((float) $row->total, 2)])
            ->filter(fn($row) => $row['value'] != 0)
            ->values();

        $dailyTrend = (clone $query)
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($row) => ['date' => $row->day, 'amount' => round((float) $row->total, 2)])
            ->values();

        $payments = $query
            ->with([
                'invoice',
                'createUser',
                'updateUser',
                'invoice.branch',
                'invoice.student',
                'invoice.student.studentCategory',
                'invoice.invoice_item',
                'invoice.invoice_item.fee_type',
                'invoice.paidCourses',
                'invoice.paidCourses.course',
                'invoice.foodPayment',
                'branch',
                'invoice.studentCategory',
            ])
            ->orderByDesc('created_at')
            ->paginate(50)
            ->appends($request->query())
            // Pre-compute the display fields (payment name, invoice payment
            // status) here rather than in the Blade file, since those need
            // the getPaymentName()/getInvoicePaymentStatus() helpers below —
            // ->through() keeps the pagination metadata intact while
            // reshaping each row.
            ->through(function ($row) {
                return (object) [
                    'admission_number' => $row->invoice?->student?->admission_number ?? 'N/A',
                    'stu_category' => $row->invoice?->studentCategory?->category_name
                        ?? $row->invoice?->student?->studentCategory?->category_name
                        ?? 'N/A',
                    'branch_name' => $row->branch?->name ?? 'N/A',
                    'invoice_number' => $row->invoice?->main_invoice_number ?? 'N/A',
                    'receipt_number' => $row->receipt_number ?? 'N/A',
                    'payment_name' => $this->getPaymentName($row),
                    'amount' => (float) $row->amount,
                    'payment_method' => $row->payment_method,
                    'payment_date' => $row->created_at,
                    'payment_invoice_number' => $row->payment_invoice_number ?? '-',
                    'bank_payment_amount' => $row->bank_payment_amount,
                    'bank_payment_date' => $row->bank_payment_date,
                    'notes' => $row->notes ?? '-',
                    'create_user' => $row->createUser?->name ?? ($row->invoice?->createUser?->name ?? 'N/A'),
                    'update_user' => $row->updateUser?->name ?? ($row->invoice?->updateUser?->name ?? 'N/A'),
                    'invoice_payment_status' => $this->getInvoicePaymentStatus($row),
                    'invoice_payment_amount' => $row->payment_method == 'Invoice' ? $row->invoice_payment_amount : null,
                    'box_number' => $row->payment_type === 'food'
                        ? ($row->invoice?->foodPayment?->lunchbox_number ?? '-')
                        : '-',
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            });

        return view('pages.reports.students-paid', compact(
            'branches',
            'payments',
            'grandTotal',
            'count',
            'methodBreakdown',
            'dailyTrend'
        ));
    }

    private function applyStudentsPaidFilters($query, Request $request, bool $applyDefaultDateWindow = true)
    {
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $from = $dates[0] ?? null;
            $to = $dates[1] ?? $from;

            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
        } elseif ($applyDefaultDateWindow) {
            // Today only, when nothing else narrows the result set either —
            // keeps the default page load fast on a table that only grows.
            $hasOtherFilters = $request->filled('payment_method') || $request->filled('branch_id') || $request->filled('payment_type') || $request->filled('search_text');
            if (!$hasOtherFilters) {
                $query->whereDate('created_at', '>=', now()->startOfDay());
            }
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
        // Free-text search across receipt / invoice / student — added so this
        // report has the same kind of broad search Invoice Payments already had.
        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('receipt_number', 'like', "%{$term}%")
                    ->orWhere('payment_invoice_number', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%")
                    ->orWhereHas('invoice', function ($invoiceQ) use ($term) {
                        $invoiceQ->where('main_invoice_number', 'like', "%{$term}%")
                            ->orWhereHas('student', function ($studentQ) use ($term) {
                                $studentQ->where('admission_number', 'like', "%{$term}%")
                                    ->orWhere('first_name', 'like', "%{$term}%")
                                    ->orWhere('last_name', 'like', "%{$term}%");
                            });
                    });
            });
        }

        return $query;
    }

    // Dedicated export route/action (matches Student Attendance's own
    // reports.student-attendance.export pattern) rather than a query flag
    // on the main route.
    public function exportStudentsPaid(Request $request)
    {
        // Same query as the page view, minus pagination — ->cursor() keeps
        // this from loading every row into memory at once, same pattern
        // Student Attendance's own export already uses.
        $query = $this->applyStudentsPaidFilters(InvoicesPayments::query(), $request)
            ->with([
                'invoice',
                'createUser',
                'updateUser',
                'invoice.branch',
                'invoice.student',
                'invoice.student.studentCategory',
                'invoice.invoice_item',
                'invoice.invoice_item.fee_type',
                'invoice.paidCourses',
                'invoice.paidCourses.course',
                'invoice.foodPayment',
                'branch',
                'invoice.studentCategory',
            ])
            ->orderBy('created_at', 'asc');

        $filename = 'Students-Payment-Report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel renders names correctly

            fputcsv($handle, [
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
                'Update User',
                'Invoice Payment Status',
                'Invoice Payment Amount',
                'Lunch Box Number',
                'Created At',
                'Updated At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $row) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
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
                    $row->createUser?->name ?? ($row->createUser?->name ?? 'N/A'),
                    $row->updateUser?->name ?? ($row->updateUser?->name ?? 'N/A'),
                    $this->getInvoicePaymentStatus($row),
                    $row->payment_method == 'Invoice' ? $row->invoice_payment_amount : '-',
                    $row->payment_type === 'food'
                        ? ($row->invoice?->foodPayment?->lunchbox_number ?? '-')
                        : '-',
                    $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('Y-m-d h:i A') : '-',
                    $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d h:i A') : '-',
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
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

    // ARCHITECTURE (this pass): same conversion as sellItems()/issueItems() —
    // filter form + ->paginate() + inline analytics + dedicated cursor export.
    // This was the biggest rework of the three: the old version had zero
    // filters/pagination at all (loaded every paid-course row on every visit).
    public function coursePaid(Request $request)
    {
        $branches = Branch::orderBy('name')->get();
        $courses = Course::orderBy('name')->get();

        $query = $this->applyCoursePaidFilters(PaidCourse::query(), $request);

        $analyticsRows = (clone $query)->get(['course_price', 'paid_amount', 'due_amount', 'payment_status', 'payment_method']);
        $grandPaid = 0.0;
        $grandDue = 0.0;
        $statusTotals = [];
        foreach ($analyticsRows as $row) {
            $grandPaid += (float) $row->paid_amount;
            $grandDue += (float) $row->due_amount;
            $status = $this->coursePaidStatusLabel($row->payment_status);
            $statusTotals[$status] = ($statusTotals[$status] ?? 0) + (float) $row->paid_amount;
        }
        $statusBreakdown = collect($statusTotals)->filter(fn($v) => $v != 0)->map(fn($v, $k) => ['label' => $k, 'value' => round($v, 2)])->values();
        $courseCount = $analyticsRows->count();

        $paidCourses = $query
            ->with(['course', 'invoice.student', 'invoice.branch', 'invoice.createUser'])
            ->orderByDesc('id')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.course-paid', compact('paidCourses', 'branches', 'courses', 'grandPaid', 'grandDue', 'courseCount', 'statusBreakdown'));
    }

    private function coursePaidStatusLabel($status)
    {
        return match ($status) {
            'full' => 'Fully Paid',
            'partial' => 'Partial',
            'not' => 'Not Paid',
            default => ucfirst((string) $status) ?: 'N/A',
        };
    }

    // Every proven-working filter against paid_courses data in this codebase
    // (studentsDatesPayments/courseIntakePayments/studentsIntakeBalance) goes
    // through fee_invoices (branch, dates) or the confirmed direct columns
    // course_id/payment_status/payment_method — never a guessed column
    // directly on paid_courses beyond those, per the no-guessing rule.
    private function applyCoursePaidFilters($query, Request $request)
    {
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            $from = trim($dates[0] ?? '');
            $to = trim($dates[1] ?? $from);
            $query->whereHas('invoice', function ($q) use ($from, $to) {
                if ($from) {
                    $q->whereDate('created_at', '>=', $from);
                }
                if ($to) {
                    $q->whereDate('created_at', '<=', $to);
                }
            });
        } else {
            $hasOtherFilters = $request->filled('branch_id') || $request->filled('course_id') || $request->filled('payment_status') || $request->filled('payment_method') || $request->filled('search_text');
            if (!$hasOtherFilters) {
                $query->whereHas('invoice', function ($q) {
                    $q->whereDate('created_at', '>=', now()->subDays(30)->startOfDay());
                });
            }
        }

        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->whereHas('invoice', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->whereHas('invoice.student', function ($q) use ($term) {
                $q->where('admission_number', 'like', "%{$term}%")
                    ->orWhere('student_name', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    public function exportCoursePaid(Request $request)
    {
        $query = $this->applyCoursePaidFilters(PaidCourse::query(), $request)
            ->with(['course', 'invoice.student', 'invoice.createUser'])
            ->orderBy('id', 'asc');

        $filename = 'Course-Paid-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Student Name',
                'Course Name',
                'Course Price',
                'Paid Amount',
                'Due Amount',
                'Payment Status',
                'Payment Method',
                'Created By',
                'Created At',
                'Updated At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $paidCourse) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
                    $paidCourse->invoice && $paidCourse->invoice->student ? $paidCourse->invoice->student->student_name : 'N/A',
                    $paidCourse->course ? $paidCourse->course->name : 'N/A',
                    number_format((float) $paidCourse->course_price, 2, '.', ''),
                    number_format((float) $paidCourse->paid_amount, 2, '.', ''),
                    number_format((float) $paidCourse->due_amount, 2, '.', ''),
                    $this->coursePaidStatusLabel($paidCourse->payment_status),
                    $paidCourse->payment_method,
                    $paidCourse->invoice?->createUser?->name ?? '-',
                    $paidCourse->created_at ? \Carbon\Carbon::parse($paidCourse->created_at)->format('Y-m-d h:i A') : '-',
                    $paidCourse->updated_at ? \Carbon\Carbon::parse($paidCourse->updated_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    // ARCHITECTURE (this pass): same conversion as the reports above — filter
    // form + ->paginate() + inline analytics + dedicated cursor export. The
    // underlying joins/aggregation are UNCHANGED (this report already works
    // via proven raw table joins) — only pagination, a search filter, and
    // export/analytics wrap around it.
    private function buildStudentsDatesPaymentsQuery(Request $request)
    {
        $to = now()->format('Y-m-d');
        $from = now()->subDays(30)->format('Y-m-d');

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

        if ($request->filled('branch_id')) {
            $query->where('fee_invoices.branch_id', $request->branch_id);
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('students.admission_number', 'like', "%{$term}%")
                    ->orWhere('students.student_name', 'like', "%{$term}%")
                    ->orWhere('fee_invoices.main_invoice_number', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    private function studentsDatesPaymentsSelect()
    {
        return [
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
            // "want add created at and updated at" — fee_invoices.updated_at is
            // already selected above (as invoice_paid_date / "Last Payment
            // Date"), so only created_at is genuinely new here.
            'fee_invoices.created_at as invoice_created_at',
            'course_categories.category_name as course_category_name',
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Cash") as cash_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Bank Transfer") as bank_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Invoice") as invoice_amount'),
        ];
    }

    public function studentsDatesPayments(Request $request)
    {
        $branches = Branch::all();

        $query = $this->buildStudentsDatesPaymentsQuery($request);

        $analyticsRows = (clone $query)
            ->select('fee_invoices.id', 'fee_invoices.paid_amount', 'fee_invoices.payment_method')
            ->distinct('fee_invoices.id')
            ->get();
        $grandPaid = 0.0;
        $methodTotals = [];
        foreach ($analyticsRows as $row) {
            $grandPaid += (float) $row->paid_amount;
            $method = $row->payment_method ?: 'Unknown';
            $methodTotals[$method] = ($methodTotals[$method] ?? 0) + (float) $row->paid_amount;
        }
        $methodBreakdown = collect($methodTotals)->filter(fn($v) => $v != 0)->map(fn($v, $k) => ['label' => $k, 'value' => round($v, 2)])->values();
        $invoiceCount = $analyticsRows->count();

        $reportData = (clone $query)
            ->select($this->studentsDatesPaymentsSelect())
            ->distinct('fee_invoices.id')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.course-dates-payments', compact('reportData', 'branches', 'grandPaid', 'invoiceCount', 'methodBreakdown'));
    }

    public function exportStudentsDatesPayments(Request $request)
    {
        $query = $this->buildStudentsDatesPaymentsQuery($request)
            ->select($this->studentsDatesPaymentsSelect())
            ->distinct('fee_invoices.id');

        $filename = 'Course-Dates-Payments-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Invoice No',
                'Student No',
                'Student Category',
                'Branch',
                'Course',
                'Intake',
                'Last Payment Date',
                'Invoice Create Date',
                'Intake Date',
                'Status',
                'Total Amount',
                'Paid Amount',
                'Cash',
                'Bank Transfer',
                'Invoice',
                'Created At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $data) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
                    $data->main_invoice_number,
                    $data->admission_number,
                    $data->course_category_name ?? ($data->student_category_name ?? '-'),
                    $data->branch_name,
                    $data->course_name,
                    $data->intake_name,
                    $data->invoice_paid_date ? \Carbon\Carbon::parse($data->invoice_paid_date)->format('Y-m-d') : '-',
                    $data->invoice_create_date ? \Carbon\Carbon::parse($data->invoice_create_date)->format('Y-m-d') : '-',
                    $data->intake_start_date,
                    ucfirst($data->status),
                    number_format((float) $data->total_amount, 2, '.', ''),
                    number_format((float) $data->paid_amount, 2, '.', ''),
                    number_format((float) $data->cash_amount, 2, '.', ''),
                    number_format((float) $data->bank_amount, 2, '.', ''),
                    number_format((float) $data->invoice_amount, 2, '.', ''),
                    $data->invoice_created_at ? \Carbon\Carbon::parse($data->invoice_created_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    // ARCHITECTURE (this pass): same conversion as above — filter form +
    // ->paginate() + inline analytics + dedicated cursor export, joins
    // unchanged. Also adds a search_text filter (student/invoice) usable
    // alongside either mode.
    private function buildCourseIntakePaymentsQuery(Request $request)
    {
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

        if ($request->filled('intake_id')) {
            $query = $baseQuery()->where('intakes.id', $request->intake_id);
        } else {
            $to = $request->filled('to_date') ? $request->to_date : now()->format('Y-m-d');
            $query = $baseQuery()->whereDate('intakes.start_date', '<=', $to);
            if ($request->filled('branch_id')) {
                $query->where('fee_invoices.branch_id', $request->branch_id);
            }
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('students.admission_number', 'like', "%{$term}%")
                    ->orWhere('students.student_name', 'like', "%{$term}%")
                    ->orWhere('fee_invoices.main_invoice_number', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    private function courseIntakePaymentsSelect()
    {
        return [
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
            DB::raw('(fee_invoices.total_amount - fee_invoices.paid_amount) as due_amount'),
            'fee_invoices.create_date as invoice_create_date',
            'fee_invoices.updated_at as invoice_paid_date',
            // fee_invoices.updated_at is already selected above (as
            // invoice_paid_date / "Last Payment Date") — only created_at is new.
            'fee_invoices.created_at as invoice_created_at',
            'course_categories.category_name as course_category_name',
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Cash") as cash_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Bank Transfer") as bank_amount'),
            DB::raw('(SELECT COALESCE(SUM(ip.amount),0) FROM invoices_payments ip WHERE ip.invoice_id = fee_invoices.id AND ip.payment_method = "Invoice") as invoice_amount'),
        ];
    }

    public function courseIntakePayments(Request $request)
    {
        $branches = Branch::all();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();

        $query = $this->buildCourseIntakePaymentsQuery($request);

        $analyticsRows = (clone $query)
            ->select('fee_invoices.id', 'fee_invoices.total_amount', 'fee_invoices.paid_amount', DB::raw('(fee_invoices.total_amount - fee_invoices.paid_amount) as due_amount'))
            ->distinct('fee_invoices.id')
            ->get();
        $grandDue = 0.0;
        $grandPaid = 0.0;
        foreach ($analyticsRows as $row) {
            $grandDue += (float) $row->due_amount;
            $grandPaid += (float) $row->paid_amount;
        }
        $invoiceCount = $analyticsRows->count();

        $reportData = (clone $query)
            ->select($this->courseIntakePaymentsSelect())
            ->distinct('fee_invoices.id')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.course-intake-payments', compact('reportData', 'branches', 'intakes', 'grandDue', 'grandPaid', 'invoiceCount'));
    }

    public function exportCourseIntakePayments(Request $request)
    {
        $query = $this->buildCourseIntakePaymentsQuery($request)
            ->select($this->courseIntakePaymentsSelect())
            ->distinct('fee_invoices.id');

        $filename = 'Course-Intake-Payments-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Invoice No',
                'Student No',
                'Student Category',
                'Branch',
                'Course',
                'Intake',
                'Last Payment Date',
                'Invoice Create Date',
                'Intake Date',
                'Status',
                'Total Amount',
                'Paid Amount',
                'Due Amount',
                'Cash',
                'Bank Transfer',
                'Invoice',
                'Created At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $data) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
                    $data->main_invoice_number,
                    $data->admission_number,
                    $data->course_category_name ?? ($data->student_category_name ?? '-'),
                    $data->branch_name,
                    $data->course_name,
                    $data->intake_name,
                    $data->invoice_paid_date ? \Carbon\Carbon::parse($data->invoice_paid_date)->format('Y-m-d') : '-',
                    $data->invoice_create_date ? \Carbon\Carbon::parse($data->invoice_create_date)->format('Y-m-d') : '-',
                    $data->intake_start_date,
                    ucfirst($data->status),
                    number_format((float) $data->total_amount, 2, '.', ''),
                    number_format((float) $data->paid_amount, 2, '.', ''),
                    number_format((float) $data->due_amount, 2, '.', ''),
                    number_format((float) $data->cash_amount, 2, '.', ''),
                    number_format((float) $data->bank_amount, 2, '.', ''),
                    number_format((float) $data->invoice_amount, 2, '.', ''),
                    $data->invoice_created_at ? \Carbon\Carbon::parse($data->invoice_created_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    // ARCHITECTURE (this pass): same conversion as above. This report is
    // already a grouped aggregate (one row per course+intake) rather than a
    // flat list, so "pagination" here paginates the aggregate rows — export
    // still streams every group via cursor(), same pattern as elsewhere.
    private function buildStudentsIntakeBalanceQuery(Request $request)
    {
        if ($request->filled('intake_id')) {
            $paymentSub = DB::table('invoices_payments')
                ->select('invoice_id', DB::raw('SUM(amount) as paid_amount'))
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
                ->where('fee_invoices.invoice_type', 'course')
                ->where('intakes.id', $request->intake_id);
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
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('courses.name', 'like', "%{$term}%")
                    ->orWhere('intakes.name', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    private function studentsIntakeBalanceSelect()
    {
        return [
            'courses.name as course_name',
            'intakes.name as intake_name',
            DB::raw('SUM(fee_invoices.total_amount) as total_amount'),
            DB::raw('SUM(COALESCE(payments.paid_amount, 0)) as paid_amount'),
            DB::raw('SUM(fee_invoices.total_amount) - SUM(COALESCE(payments.paid_amount, 0)) as balance_amount'),
        ];
    }

    public function studentsIntakeBalance(Request $request)
    {
        $branches = Branch::all();
        $intakes = Intake::orderBy('start_date', 'DESC')->get();

        $query = $this->buildStudentsIntakeBalanceQuery($request);

        $analyticsRows = (clone $query)
            ->select($this->studentsIntakeBalanceSelect())
            ->groupBy('courses.id', 'courses.name', 'intakes.name')
            ->get();
        $grandTotal = 0.0;
        $grandPaid = 0.0;
        $grandBalance = 0.0;
        foreach ($analyticsRows as $row) {
            $grandTotal += (float) $row->total_amount;
            $grandPaid += (float) $row->paid_amount;
            $grandBalance += (float) $row->balance_amount;
        }
        $groupCount = $analyticsRows->count();

        $reportData = (clone $query)
            ->select($this->studentsIntakeBalanceSelect())
            ->groupBy('courses.id', 'courses.name', 'intakes.name')
            ->orderBy('courses.name')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.students-intake-balance', compact('reportData', 'branches', 'intakes', 'grandTotal', 'grandPaid', 'grandBalance', 'groupCount'));
    }

    public function exportStudentsIntakeBalance(Request $request)
    {
        $query = $this->buildStudentsIntakeBalanceQuery($request)
            ->select($this->studentsIntakeBalanceSelect())
            ->groupBy('courses.id', 'courses.name', 'intakes.name')
            ->orderBy('courses.name');

        $filename = 'Students-Intake-Balance-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Intake', 'Course', 'Total Amount', 'Paid Amount', 'Balance']);

            $rowNo = 0;
            foreach ($query->cursor() as $data) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
                    $data->intake_name,
                    $data->course_name,
                    number_format((float) $data->total_amount, 2, '.', ''),
                    number_format((float) $data->paid_amount, 2, '.', ''),
                    number_format((float) $data->balance_amount, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }


    // ARCHITECTURE (this pass): same conversion as the other raw-query
    // reports — filter form + ->paginate() + inline analytics + dedicated
    // cursor export. This report had zero filters at all before.
    private function buildChangeCourseQuery(Request $request)
    {
        $query = DB::table('student_courses as sc')
            ->join('students', 'sc.student_id', '=', 'students.id')
            ->join('courses as old_course', 'sc.course_id', '=', 'old_course.id')
            ->join('courses as new_course', 'sc.change_course_id', '=', 'new_course.id')
            ->join('intakes', 'sc.intake_id', '=', 'intakes.id')
            ->leftJoin('users', 'sc.updated_by', '=', 'users.id')
            ->leftJoin('branches', 'sc.branch_id', '=', 'branches.id')
            ->whereNotNull('sc.change_course_id');

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            $from = trim($dates[0] ?? '');
            $to = trim($dates[1] ?? $from);
            if ($from) {
                $query->whereDate('sc.updated_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('sc.updated_at', '<=', $to);
            }
        } else {
            $hasOtherFilters = $request->filled('branch_id') || $request->filled('new_course_id') || $request->filled('search_text');
            if (!$hasOtherFilters) {
                $query->whereDate('sc.updated_at', '>=', now()->subDays(30)->startOfDay());
            }
        }

        if ($request->filled('branch_id')) {
            $query->where('sc.branch_id', $request->branch_id);
        }

        if ($request->filled('new_course_id')) {
            $query->where('sc.change_course_id', $request->new_course_id);
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('students.admission_number', 'like', "%{$term}%")
                    ->orWhere('students.student_name', 'like', "%{$term}%");
            });
        }

        return $query;
    }

    private function changeCourseSelect()
    {
        return [
            'students.admission_number',
            'students.student_name as student_name',
            'old_course.name as old_course_name',
            'new_course.name as new_course_name',
            'intakes.name as intake_name',
            'branches.name as branch_name',
            'sc.updated_at as changed_date',
            'users.name as changed_by',
            // "Changed Date" above is already sc.updated_at — created_at is
            // the only genuinely new value here. student_courses has no
            // confirmed creator column, so no "Created By" is added.
            'sc.created_at as created_date',
        ];
    }

    public function changeCourse(Request $request)
    {
        $branches = Branch::all();
        $courses = Course::orderBy('name')->get();

        $query = $this->buildChangeCourseQuery($request);

        $analyticsRows = (clone $query)->select('sc.updated_at as changed_date', 'new_course.name as new_course_name')->get();
        $dailyCounts = [];
        $courseCounts = [];
        foreach ($analyticsRows as $row) {
            if ($row->changed_date) {
                $day = \Carbon\Carbon::parse($row->changed_date)->format('Y-m-d');
                $dailyCounts[$day] = ($dailyCounts[$day] ?? 0) + 1;
            }
            $courseName = $row->new_course_name ?: 'Unknown';
            $courseCounts[$courseName] = ($courseCounts[$courseName] ?? 0) + 1;
        }
        ksort($dailyCounts);
        $dailyTrend = collect($dailyCounts)->map(fn($v, $k) => ['date' => $k, 'count' => $v])->values();
        arsort($courseCounts);
        $courseBreakdown = collect(array_slice($courseCounts, 0, 8, true))->map(fn($v, $k) => ['label' => $k, 'value' => $v])->values();
        $changeCount = $analyticsRows->count();

        $data = (clone $query)
            ->select($this->changeCourseSelect())
            ->orderBy('sc.updated_at', 'desc')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.change-course', compact('data', 'branches', 'courses', 'dailyTrend', 'courseBreakdown', 'changeCount'));
    }

    public function exportChangeCourse(Request $request)
    {
        $query = $this->buildChangeCourseQuery($request)
            ->select($this->changeCourseSelect())
            ->orderBy('sc.updated_at', 'desc');

        $filename = 'Change-Course-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Student No', 'Student Name', 'Old Course', 'New Course', 'Intake', 'Branch', 'Changed By', 'Changed Date', 'Created At']);

            $rowNo = 0;
            foreach ($query->cursor() as $row) {
                $rowNo++;
                fputcsv($handle, [
                    $rowNo,
                    $row->admission_number,
                    $row->student_name,
                    $row->old_course_name,
                    $row->new_course_name,
                    $row->intake_name,
                    $row->branch_name ?? '-',
                    $row->changed_by ?? '-',
                    $row->changed_date ? \Carbon\Carbon::parse($row->changed_date)->format('Y-m-d') : '-',
                    $row->created_date ? \Carbon\Carbon::parse($row->created_date)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    // ARCHITECTURE (this pass): rebuilt to match Student Attendance's pattern
    // — real filters, a server-paginated table, and a dedicated export that
    // streams every filtered row. Replaces the earlier server-side-DataTable
    // version.
    public function invoicePayments(Request $request)
    {
        $branches = Branch::orderBy('name')->get();

        $paymentMethods = InvoicesPayments::whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        // Analytics — computed inline at page load (same approach as
        // Student Attendance) rather than via a separate ajax call. Iterates
        // the same items.payment relation the CSV export already uses,
        // bounded by whatever filters are active.
        $analyticsQuery = InvoicePaymentReceipt::with(['items.payment']);
        $this->applyInvoicePaymentFilters($analyticsQuery, $request);
        $analyticsReceipts = $analyticsQuery->get();

        $methodTotals = [];
        $dailyTotals = [];
        $grandTotal = 0.0;
        foreach ($analyticsReceipts as $receipt) {
            $day = optional($receipt->created_at)->format('Y-m-d');
            foreach ($receipt->items as $item) {
                $amount = (float) ($item->paid_amount ?? 0);
                $method = $item->payment->payment_method ?? 'Unknown';
                $methodTotals[$method] = ($methodTotals[$method] ?? 0) + $amount;
                if ($day) {
                    $dailyTotals[$day] = ($dailyTotals[$day] ?? 0) + $amount;
                }
                $grandTotal += $amount;
            }
        }
        ksort($dailyTotals);
        $methodBreakdown = collect($methodTotals)->map(fn($v, $k) => ['label' => $k, 'value' => round($v, 2)])->values();
        $dailyTrend = collect($dailyTotals)->map(fn($v, $k) => ['date' => $k, 'amount' => round($v, 2)])->values();
        $receiptCount = $analyticsReceipts->count();

        $listQuery = InvoicePaymentReceipt::with([
            'items.payment.invoice.student',
            'items.payment.invoice.branch',
            'user',
        ])->withCount('items');
        $this->applyInvoicePaymentFilters($listQuery, $request);

        $receipts = $listQuery
            ->orderBy('invoice_payment_receipts.created_at', 'desc')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.invoice-payments', compact(
            'branches',
            'paymentMethods',
            'receipts',
            'grandTotal',
            'receiptCount',
            'methodBreakdown',
            'dailyTrend'
        ));
    }

    private function applyInvoicePaymentFilters($query, Request $request)
    {
        // ----- DATE RANGE (on receipt created_at) -----
        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            $from = trim($dates[0] ?? '');
            $to   = trim($dates[1] ?? $from);

            if ($from) {
                $query->whereDate('invoice_payment_receipts.created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('invoice_payment_receipts.created_at', '<=', $to);
            }
        } else {
            // Default to last 30 days when no filter is supplied at all
            $query->whereDate('invoice_payment_receipts.created_at', '>=', now()->subDays(30)->startOfDay());
        }

        // ----- BRANCH (receipt has no branch_id column itself; it hangs off the invoice) -----
        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->whereHas('items.payment.invoice.branch', function ($q) use ($branchId) {
                $q->where('id', $branchId);
            });
        }

        // ----- PAYMENT METHOD (matches if ANY item on the receipt used this method) -----
        if ($request->filled('payment_method')) {
            $method = $request->payment_method;
            $query->whereHas('items.payment', function ($q) use ($method) {
                $q->where('payment_method', $method);
            });
        }

        // ----- FREE-TEXT SEARCH: receipt no / cheque no / note / invoice no / student -----
        if ($request->filled('search_text')) {
            $term = $request->search_text;

            $query->where(function ($q) use ($term) {
                $q->where('invoice_payment_receipts.receipt_number', 'like', "%{$term}%")
                    ->orWhere('invoice_payment_receipts.cheque_number', 'like', "%{$term}%")
                    ->orWhere('invoice_payment_receipts.note', 'like', "%{$term}%")
                    ->orWhereHas('items.payment', function ($pq) use ($term) {
                        $pq->where('payment_invoice_number', 'like', "%{$term}%");
                    })
                    ->orWhereHas('items.payment.invoice', function ($iq) use ($term) {
                        $iq->where('main_invoice_number', 'like', "%{$term}%");
                    })
                    ->orWhereHas('items.payment.invoice.student', function ($sq) use ($term) {
                        $sq->where('admission_number', 'like', "%{$term}%")
                            ->orWhere('student_name', 'like', "%{$term}%");
                    });
            });
        }

        return $query;
    }

    public function getReceiptItemsData($id)
    {
        $receipt = InvoicePaymentReceipt::with(['items.payment.invoice'])->findOrFail($id);

        $items = $receipt->items->values()->map(function ($item, $idx) {
            return [
                'item_no'         => $idx + 1,
                'receipt_no'      => $item->payment->receipt_number ?? '-',
                'main_invoice_no' => $item->payment->invoice->main_invoice_number ?? '-',
                'paid_amount'     => number_format((float) $item->paid_amount, 2),
                'inv_payment_no'  => $item->payment->payment_invoice_number ?? '-',
                'payment_method'  => $item->payment->payment_method ?? '-',
            ];
        });

        return response()->json(['items' => $items]);
    }

    public function exportInvoicePayments(Request $request)
    {
        $query = InvoicePaymentReceipt::with([
            'items.payment.invoice.student',
            'items.payment.invoice.branch',
            'user',
        ]);

        $this->applyInvoicePaymentFilters($query, $request)
            ->orderBy('invoice_payment_receipts.created_at', 'asc');

        $filename = 'Invoice-Payments-Report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'No',
            'Receipt No',
            'Created Date',
            'Cheque No',
            'Total Amount',
            'Payment Method(s)',
            'Note',
            'Created By',
            'Inv. Payment No',
            'Student Name',
            'Reg. No',
            'Branch',
            'Updated At',
            'Item No',
            'Item Receipt No',
            'Item Main Invoice No',
            'Item Paid Amount',
            'Item Inv. Payment No',
        ];

        // ->cursor() rather than ->get() so an export with no date filter
        // (every receipt ever) streams row-by-row instead of loading the
        // whole filtered set into memory at once.
        return response()->stream(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel renders names correctly
            fputcsv($out, $columns);

            $rowNo = 0;
            foreach ($query->cursor() as $receipt) {
                $rowNo++;
                $firstPayment = $receipt->items->first()?->payment;
                $invoice      = $firstPayment?->invoice;
                $student      = $invoice?->student;
                $branch       = $invoice?->branch;
                $methods      = $receipt->items
                    ->pluck('payment.payment_method')
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');

                $base = [
                    $rowNo,
                    $receipt->receipt_number,
                    optional($receipt->created_at)->format('Y-m-d h:i A'),
                    $receipt->cheque_number ?? '-',
                    number_format((float) $receipt->total_amount, 2, '.', ''),
                    $methods ?: '-',
                    $receipt->note ?? '-',
                    trim(($receipt->user->first_name ?? '') . ' ' . ($receipt->user->last_name ?? '')) ?: 'N/A',
                    $firstPayment->payment_invoice_number ?? '-',
                    $student->student_name ?? '-',
                    $student->admission_number ?? '-',
                    $branch->name ?? '-',
                    optional($receipt->updated_at)->format('Y-m-d h:i A') ?? '-',
                ];

                if ($receipt->items->isEmpty()) {
                    fputcsv($out, array_merge($base, ['-', '-', '-', '-', '-']));
                    continue;
                }

                foreach ($receipt->items as $idx => $item) {
                    fputcsv($out, array_merge($base, [
                        $idx + 1,
                        $item->payment->receipt_number ?? '-',
                        $item->payment->invoice->main_invoice_number ?? '-',
                        number_format((float) $item->paid_amount, 2, '.', ''),
                        $item->payment->payment_invoice_number ?? '-',
                    ]));
                }
            }

            fclose($out);
        }, 200, $headers);
    }

    // ARCHITECTURE (this pass): filter form + inline analytics + dedicated
    // cursor export, matching the other reports. The "grouped by year/month/
    // branch/lunchbox" browse view stays a full-collection view (grouping
    // needs the whole filtered set to nest correctly — it can't be sliced
    // page-wise without breaking the group headers), but it is already
    // bounded to "today" by default the same way it was before; the
    // separate flat "Full List" table below it is now ->paginate()'d
    // ("load data page wise") while Export CSV streams every filtered row
    // via ->cursor() regardless of page.
    private function buildFoodPaymentQuery(Request $request)
    {
        $query = \App\Models\FoodPayment::with([
            'invoice',
            'invoice.student',
            'invoice.student.studentCategory',
            'invoice.createUser',
            'invoice.branch',
            'course',
        ]);

        if ($request->branch_id) {
            $query->whereHas('invoice', fn($q) => $q->where('branch_id', $request->branch_id));
        }

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->letter) {
            $query->where('lunchbox_number', 'LIKE', $request->letter . '-%');
        }

        if ($request->meal) {
            if ($request->meal === 'breakfast') $query->where('has_breakfast', 1);
            if ($request->meal === 'lunch')     $query->where('has_lunch', 1);
            if ($request->meal === 'dinner')    $query->where('has_dinner', 1);
        }

        $hasFilters = $request->filled('branch_id') ||
            $request->filled('course_id') ||
            $request->filled('letter') ||
            $request->filled('meal') ||
            $request->filled('date_range') ||
            $request->filled('month') ||
            $request->filled('year') ||
            $request->filled('search_text') ||
            $request->has('status');

        if ($request->filled('date_range')) {
            $dates     = explode(' to ', $request->date_range);
            $filterFrom = $dates[0] ?? null;
            $filterTo   = $dates[1] ?? ($dates[0] ?? null);

            if ($filterFrom && $filterTo) {
                $query->whereDate('from_date', '<=', $filterTo)
                    ->whereDate('to_date',   '>=', $filterFrom);
            } elseif ($filterFrom) {
                $query->whereDate('from_date', '<=', $filterFrom)
                    ->whereDate('to_date',   '>=', $filterFrom);
            }
        } elseif (!$hasFilters) {
            $today = today()->toDateString();
            $query->whereDate('from_date', '<=', $today)
                ->whereDate('to_date',   '>=', $today);
        }

        if ($request->month) {
            $query->whereMonth('from_date', $request->month);
        }
        if ($request->year) {
            $query->whereYear('from_date', $request->year);
        }

        $status = $request->input('status', '1');
        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($request->filled('search_text')) {
            $term = $request->search_text;
            $query->where(function ($q) use ($term) {
                $q->where('lunchbox_number', 'like', "%{$term}%")
                    ->orWhereHas('invoice', function ($iq) use ($term) {
                        $iq->where('main_invoice_number', 'like', "%{$term}%")
                            ->orWhereHas('student', function ($sq) use ($term) {
                                $sq->where('admission_number', 'like', "%{$term}%")
                                    ->orWhere('student_name', 'like', "%{$term}%");
                            });
                    });
            });
        }

        return $query;
    }

    public function foodPaymentReport(Request $request)
    {
        $branches = Branch::all();
        $courses  = Course::where('has_food_fee', 1)->orderBy('name')->get();
        $letters = range('A', 'Z');

        $query = $this->buildFoodPaymentQuery($request);

        // The grouped browse view needs the whole filtered set to nest
        // correctly (year → month → branch → lunchbox letter) — reused here
        // for the summary cards and meal-breakdown chart too, so it's only
        // fetched once per request.
        $foodPayments = (clone $query)->orderBy('created_at')->get();

        $grouped = $foodPayments->groupBy([
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('Y'),
            fn($r) => \Carbon\Carbon::parse($r->from_date)->format('m'),
            fn($r) => optional($r->invoice->branch)->name ?? 'N/A',
            fn($r) => substr($r->lunchbox_number ?? '-', 0, 1),
        ]);

        $mealBreakdown = collect([
            ['label' => 'Breakfast', 'value' => $foodPayments->where('has_breakfast', 1)->count()],
            ['label' => 'Lunch', 'value' => $foodPayments->where('has_lunch', 1)->count()],
            ['label' => 'Dinner', 'value' => $foodPayments->where('has_dinner', 1)->count()],
        ])->filter(fn($row) => $row['value'] > 0)->values();

        // Flat, paginated slice of the SAME filtered query for the "Full
        // List" table below the grouped view — this is what actually
        // avoids loading everything into one on-screen table.
        $foodPaymentsFlat = (clone $query)
            ->orderBy('created_at')
            ->paginate(50)
            ->appends($request->query());

        return view('pages.reports.food-payment-report', compact(
            'foodPayments',
            'foodPaymentsFlat',
            'grouped',
            'branches',
            'courses',
            'letters',
            'mealBreakdown'
        ));
    }

    public function exportFoodPayment(Request $request)
    {
        $query = $this->buildFoodPaymentQuery($request)->orderBy('created_at');

        $filename = 'Food-Payment-Report_' . now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'No',
                'Lunchbox No.',
                'Group',
                'Year',
                'Month',
                'Branch',
                'Student No.',
                'Student Name',
                'Category',
                'Course',
                'Food From',
                'Food To',
                'Breakfast',
                'Lunch',
                'Dinner',
                'Due Amount',
                'Status',
                'Invoice No.',
                'Create Date',
                'Update Date',
                'Created By',
                'Created At',
                'Updated At',
            ]);

            $rowNo = 0;
            foreach ($query->cursor() as $fp) {
                $rowNo++;
                $student = $fp->invoice?->student;
                fputcsv($handle, [
                    $rowNo,
                    $fp->lunchbox_number ?? '-',
                    substr($fp->lunchbox_number ?? '-', 0, 1),
                    \Carbon\Carbon::parse($fp->from_date)->format('Y'),
                    \Carbon\Carbon::parse($fp->from_date)->format('F'),
                    $fp->invoice?->branch?->name ?? 'N/A',
                    $student?->admission_number ?? 'N/A',
                    $student?->student_name ?? 'N/A',
                    $student?->studentCategory?->category_name ?? 'N/A',
                    $fp->course?->name ?? 'N/A',
                    \Carbon\Carbon::parse($fp->from_date)->format('d/m/Y'),
                    \Carbon\Carbon::parse($fp->to_date)->format('d/m/Y'),
                    $fp->has_breakfast ? 'Yes' : '-',
                    $fp->has_lunch ? 'Yes' : '-',
                    $fp->has_dinner ? 'Yes' : '-',
                    number_format((float) $fp->due_amount, 2, '.', ''),
                    ucfirst((string) $fp->payment_status),
                    $fp->invoice?->main_invoice_number ?? 'N/A',
                    $fp->invoice?->created_at ?? 'N/A',
                    $fp->invoice?->updated_at ?? 'N/A',
                    $fp->invoice?->createUser?->name ?? '-',
                    $fp->created_at ? \Carbon\Carbon::parse($fp->created_at)->format('Y-m-d h:i A') : '-',
                    $fp->updated_at ? \Carbon\Carbon::parse($fp->updated_at)->format('Y-m-d h:i A') : '-',
                ]);
            }
            fclose($handle);
        }, $filename, $headers);
    }


    private function studentAttendanceQuery(Request $request): Builder
    {
        $query = SubjectAttendanceSession::with([
            'student.branch',
            'subject',
            'course',
            'intake',
            'intake.branch',
            'vehicle',
            'markedInBy',
            'markedInBy.branches',
            'markedOutBy',
        ]);


        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->where(function ($q) use ($branchId) {
                // Case 1: markedInBy has EXACTLY ONE branch, and it's this one.
                $q->whereHas('markedInBy', function ($userQuery) use ($branchId) {
                    $userQuery
                        ->has('branches', '=', 1)
                        ->whereHas('branches', function ($branchQuery) use ($branchId) {
                            $branchQuery->where('branches.id', $branchId);
                        });
                })
                    // Case 2: markedInBy does NOT have exactly one branch
                    // (no markedInBy at all, 0 branches, or 2+ branches) —
                    // the accessor falls back to the intake's branch.
                    ->orWhere(function ($q2) use ($branchId) {
                        $q2->whereDoesntHave('markedInBy', function ($userQuery) {
                            $userQuery->has('branches', '=', 1);
                        })
                            ->whereHas('intake', function ($intakeQuery) use ($branchId) {
                                $intakeQuery->where('branch_id', $branchId);
                            });
                    });
            });
        }
        if ($request->filled('intake_id')) {
            $query->where('intake_id', $request->intake_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('type')) {

            switch ($request->type) {
                case 'course':
                    $query->whereNull('checkpoint_type')->whereNull('subject_id');
                    break;
                case 'subject':
                    $query->whereNull('checkpoint_type')
                        ->whereHas('subject', fn($q) => $q->where('type', 'T'));
                    break;
                case 'practical':
                    $query->whereNull('checkpoint_type')
                        ->whereHas('subject', fn($q) => $q->where('type', 'P'));
                    break;
                case 'before_checking':
                    $query->where('checkpoint_type', 'before_checking');
                    break;
                case 'after_checking':
                    $query->where('checkpoint_type', 'after_checking');
                    break;
            }
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('marked_in_by')) {
            $query->where('marked_in_by', $request->marked_in_by);
        }
        if ($request->filled('marked_out_by')) {
            $query->where('marked_out_by', $request->marked_out_by);
        }
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('time_out');
            } elseif ($request->status === 'in_progress') {
                $query->whereNull('time_out');
            }
        }
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        if ($request->filled('admission_number')) {
            $admissionNumber = $request->admission_number;
            $query->whereHas('student', function ($q) use ($admissionNumber) {
                $q->where('admission_number', 'like', "%{$admissionNumber}%");
            });
        }
        if ($request->filled('student_name')) {
            $studentName = $request->student_name;
            $query->whereHas('student', function ($q) use ($studentName) {
                $q->where('student_name', 'like', "%{$studentName}%");
            });
        }

        return $query;
    }
    public function studentAttendance(Request $request)
    {
        $intakes = Intake::all();
        $courses = Course::all();
        $subjects = Subject::all();
        $vehicles = TrainingVehicle::orderBy('name')->get(['id', 'name']);
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        // ADJUST: 'name' is assumed to be the display-name column on your User
        // model — swap for 'first_name' or similar if that's not right.
        $users = User::orderBy('name')->get(['id', 'name']);

        $query = $this->studentAttendanceQuery($request);

        // Summary over the whole filtered set, computed before pagination cuts
        // it down to one page.
        $totalCount = (clone $query)->count();
        $inProgressCount = (clone $query)->whereNull('time_out')->count();
        $totalMinutes = (clone $query)->whereNotNull('time_out')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, time_in, time_out)) as total')
            ->value('total') ?? 0;

        $typeBreakdown = collect([
            ['label' => 'Daily Attendance', 'value' => (clone $query)->whereNull('checkpoint_type')->whereNull('subject_id')->count()],
            ['label' => 'Theory', 'value' => (clone $query)->whereNull('checkpoint_type')->whereHas('subject', fn($q) => $q->where('type', 'T'))->count()],
            ['label' => 'Practical', 'value' => (clone $query)->whereNull('checkpoint_type')->whereHas('subject', fn($q) => $q->where('type', 'P'))->count()],
            ['label' => 'Before Checking', 'value' => (clone $query)->where('checkpoint_type', 'before_checking')->count()],
            ['label' => 'After Checking', 'value' => (clone $query)->where('checkpoint_type', 'after_checking')->count()],
        ])->filter(fn($row) => $row['value'] > 0)->values();

        $trendQuery = (clone $query);
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $trendQuery->where('attendance_date', '>=', now()->subDays(30));
        }
        $dailyTrend = $trendQuery
            ->selectRaw('DATE(attendance_date) as day, COUNT(*) as cnt')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn($row) => ['date' => $row->day, 'count' => (int) $row->cnt])
            ->values();

        $sessions = $query->orderByDesc('attendance_date')->orderByDesc('time_in')->paginate(50)->appends($request->query());

        return view('pages.reports.student-attendance', compact(
            'intakes',
            'courses',
            'subjects',
            'vehicles',
            'branches',
            'users',
            'sessions',
            'totalCount',
            'inProgressCount',
            'totalMinutes',
            'typeBreakdown',
            'dailyTrend'
        ));
    }

    public function exportStudentAttendance(Request $request)
    {
        $query = $this->studentAttendanceQuery($request)
            ->orderByDesc('attendance_date')
            ->orderByDesc('time_in');

        $filename = 'attendance-' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // Excel needs a UTF-8 BOM to display non-ASCII names correctly.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'Date',
                'Register No.',
                'Student',
                'Branch',
                'Intake',
                'Course',
                'Type',
                'Subject',
                'Time In',
                'In By',
                'Time Out',
                'Out By',
                'Duration',
                'Vehicle',
                'Note',
                // "student-attendance report want add created at and updated
                // at only" — appended at the end so every existing column
                // (and anything reading this CSV by position) is unaffected.
                'Created At',
                'Updated At',
            ]);

            foreach ($query->cursor() as $session) {
                fputcsv($out, [
                    optional($session->attendance_date)->format('Y-m-d'),
                    $session->student->admission_number ?? '',
                    $session->student->student_name ?? '',
                    $session->attendance_branch?->name ?? '',
                    $session->intake->name ?? '',
                    $session->course->name ?? '',
                    $session->type_label,
                    $session->checkpoint_type ? '—' : ($session->subject->name ?? 'Daily Attendance'),
                    optional($session->time_in)->format('h:i A'),
                    $session->markedInBy->name ?? '',
                    $session->time_out ? $session->time_out->format('h:i A') : 'Still in',
                    $session->markedOutBy->name ?? '',
                    $session->duration_label,
                    $session->vehicle->name ?? '',
                    $session->note ?? '',
                    optional($session->created_at)->format('Y-m-d h:i A') ?? '-',
                    optional($session->updated_at)->format('Y-m-d h:i A') ?? '-',
                ]);
            }

            fclose($out);
        }, $filename, $headers);
    }
}
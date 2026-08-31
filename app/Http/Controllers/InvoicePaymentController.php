<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InvoicePaymentReceipt;
use App\Models\InvoicePaymentReceiptItem;
use App\Models\InvoicesPayments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class InvoicePaymentController extends Controller
{

    public function index()
    {
        $branches = Branch::all();
        return view('pages.student.fees.invoice-payments-pending', compact('branches'));
    }

    public function pendingIndex()
    {
        $branches = Branch::all();
        return view('pages.student.fees.invoice-payments', compact('branches'));
    }



    // public function datatable(Request $request)
    // {
    //     // Base query: only payments where payment_method = 'Invoice'
    //     $query = InvoicesPayments::with(['invoice.student', 'invoice.branch', 'branch', 'invoice.paidCourses', 'invoice.student.studentCategory'])
    //         ->where('payment_method', 'Invoice')
    //         ->where('payment_type', '!=', 'refund')
    //         ->where(function ($q) {
    //             $q->where('payment_status', '!=', 'refunded')
    //                 ->orWhereNull('payment_status');
    //         })
    //         ->whereHas('invoice', function ($q) {
    //             $q->where('payment_status', '!=', 'returned');
    //         });
    //     // Optional filters
    //     if ($request->branch_id) {
    //         $query->where('branch_id', $request->branch_id);
    //     }

    //     if ($request->payment_status) {
    //         if ($request->payment_status == 'not_open') {
    //             $query->where('payment_invoice_number', null);
    //         } elseif ($request->payment_status == 'pending') {
    //             $query->where('payment_invoice_number', '!=', null)->where('invoice_payment_confirm', null);
    //         } elseif ($request->payment_status == 'completed') {
    //             $query->where('invoice_payment_confirm', true);
    //         }
    //     }

    //     if ($request->has('search') && $request->search['value'] != '') {
    //         $search = $request->search['value'];

    //         $query->where(function ($q) use ($search) {
    //             $q->where('customer_name', 'like', "%{$search}%")
    //                 ->orWhere('payment_invoice_number', 'like', "%{$search}%")
    //                 ->orWhereHas('invoice.student', function ($q2) use ($search) {
    //                     $q2->where('student_name', 'like', "%{$search}%")
    //                         ->orWhere('admission_number', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('invoice.branch', function ($q3) use ($search) {
    //                     $q3->where('name', 'like', "%{$search}%");
    //                 })
    //                 ->orWhereHas('invoice.student.studentCategory', function ($q4) use ($search) {
    //                     $q4->where('category_name', 'like', "%{$search}%");
    //                 });
    //         });
    //     }


    //     return DataTables::of($query)
    //         ->addColumn('select', function ($row) {
    //             if ($row->invoice_payment_confirm) {
    //                 $status = 'completed';
    //             } elseif ($row->payment_invoice_number != null) {
    //                 $status = 'pending';
    //             } else {
    //                 $status = 'not_open';
    //             }
    //             return '<input type="checkbox" class="payment-checkbox" value="' . $row->id . '" data-status="' . $status . '"   data-student="' . ($row->invoice->student_id ?? '') . '">';
    //         })
    //         ->addColumn('invoice_no', function ($row) {
    //             return $row->invoice->main_invoice_number ?? '-';
    //         })
    //         ->addColumn('invoice_item_no', function ($row) {
    //             return $row->receipt_number ?? '-';
    //         })
    //         ->addColumn('customer_name', function ($row) {
    //             return $row->customer_name ?? '-';
    //         })
    //         ->addColumn('invoice_receipt_no', function ($row) {
    //             return $row->payment_invoice_number ?? '-';
    //         })
    //         ->addColumn('branch', function ($row) {
    //             return $row->invoice->branch->name ?? '-';
    //         })
    //         ->addColumn('student_name', function ($row) {
    //             return $row->invoice->student->student_name ?? '-';
    //         })
    //         ->addColumn('student_category', function ($row) {
    //             return $row->invoice->student->studentCategory->category_name ?? '-';
    //         })

    //         ->addColumn('admission_number', function ($row) {
    //             return $row->invoice->student->admission_number ?? '-';
    //         })
    //         ->addColumn('amount', function ($row) {
    //             return number_format($row->amount, 2);
    //         })

    //         ->addColumn('confirm_cheque_number', function ($row) {
    //             return $row->invoice_payment_cheque_number ?? '-';
    //         })
    //         ->addColumn('confirm_payment_amount', function ($row) {
    //             return $row->invoice_payment_amount ? number_format($row->invoice_payment_amount, 2) : '-';
    //         })
    //         ->addColumn('confirm_note', function ($row) {
    //             return $row->invoice_payment_confirm_note ?? '-';
    //         })
    //         ->addColumn('status', function ($row) {
    //             if ($row->invoice_payment_confirm) {
    //                 return 'Completed';
    //             } elseif ($row->payment_invoice_number != null) {
    //                 return 'Pending';
    //             } else {
    //                 return 'Not Open';
    //             }
    //         })
    //         ->addColumn('fee_name', function ($row) {
    //             return $row->payment_method;
    //         })
    //         ->editColumn('fee_name', function ($row) {
    //             if ($row->invoice->invoice_type === 'fee') {
    //                 return optional($row->invoice->invoice_item->first()?->fee_type)->name ?? '-';
    //             }
    //             if ($row->invoice->invoice_type === 'course') {
    //                 return optional(optional($row->invoice->paidCourses)->course)->name ?? '-';
    //             }
    //             if ($row->invoice->invoice_type === 'nvq') {
    //                 return  'NCQ - ' . (optional(optional($row->invoice->paidCourses)->course)->name) ?? '-';
    //             }
    //             if ($row->invoice->invoice_type === 'item_sell') {
    //                 return  'Item Sell';
    //             }
    //             return '-';
    //         })


    //         ->addColumn('date', function ($row) {
    //             return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
    //         })
    //         ->addColumn('note', function ($row) {
    //             return $row->invoice_payment_note ?? '-';
    //         })
    //         ->addColumn('action', function ($row) {

    //             if ($row->payment_invoice_number) {
    //                 return '<a href="' . route('invoice-payments.print', $row->payment_invoice_number) . '" 
    //                 target="_blank" 
    //                 class="primary-btn small btn-primary text-white">
    //                 Reprint
    //             </a>';
    //             }

    //             return '-';
    //         })
    //         ->rawColumns(['action', 'select'])
    //         ->make(true);
    // }



    public function notStartDatatable(Request $request)
    {
        // =========================
        // BASE QUERY
        // =========================
        $query = InvoicesPayments::with([
            'invoice.student',
            'invoice.branch',
            'invoice.paidCourses.course',
            'invoice.student.studentCategory',
            'invoice.invoice_item.fee_type'
        ])
            ->where('payment_method', 'Invoice')
            ->where('payment_type', '!=', 'refund')
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'refunded')
                    ->orWhereNull('payment_status');
            })
            ->whereHas('invoice', function ($q) {
                $q->where('payment_status', '!=', 'returned');
            })->whereNull('payment_invoice_number');;

        // =========================
        // BRANCH FILTER
        // =========================
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }


        // =========================
        // SEARCH
        // =========================
        if ($request->has('search') && $request->search['value'] != '') {

            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {

                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('payment_invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('invoice.student', function ($q2) use ($search) {
                        $q2->where('student_name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice.branch', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('invoice.student.studentCategory', function ($q4) use ($search) {
                        $q4->where('category_name', 'like', "%{$search}%");
                    });
            });
        }

        // =========================
        // DATATABLE RESPONSE
        // =========================
        return DataTables::of($query)

            // CHECKBOX + STATUS
            // ── PERMISSION GATE (Section 28) ──────────────────────────────────
            // No checkbox at all if the role can't do either bulk action — matches
            // the "Bulk Update" button on the blade side, which is already hidden
            // for these same two keys ($canBulkUpdateInvoicePayments).
            ->addColumn('select', function ($row) {

                $user = Auth::user();
                if (
                    !$user->hasPermission('invoice-payments.bulk-not-open') &&
                    !$user->hasPermission('invoice-payments.bulk-pending')
                ) {
                    return '';
                }

                if ($row->invoice_payment_confirm) {
                    $status = 'completed';
                } elseif ($row->payment_invoice_number != null) {
                    $status = 'pending';
                } else {
                    $status = 'not_open';
                }

                return '<label class="custom-checkbox">
                <input type="checkbox"
                    class="payment-checkbox"
                    value="' . $row->id . '"
                    data-status="' . $status . '"
                    data-student="' . ($row->invoice->student_id ?? '') . '">
                <span class="custom-checkbox-indicator"></span>
            </label>';
            })

            // INVOICE NO
            ->addColumn('invoice_no', function ($row) {
                return $row->invoice->main_invoice_number ?? '-';
            })

            // RECEIPT NO
            ->addColumn('invoice_item_no', function ($row) {
                return $row->receipt_number ?? '-';
            })

            // BRANCH
            ->addColumn('branch', function ($row) {
                return $row->invoice->branch->name ?? '-';
            })

            // STUDENT
            ->addColumn('student_name', function ($row) {
                return $row->invoice->student->student_name ?? '-';
            })

            // CATEGORY
            ->addColumn('student_category', function ($row) {
                return $row->invoice->student->studentCategory->category_name ?? '-';
            })

            // ADMISSION
            ->addColumn('admission_number', function ($row) {
                return $row->invoice->student->admission_number ?? '-';
            })

            // AMOUNT
            ->addColumn('amount', function ($row) {
                return number_format($row->amount ?? 0, 2);
            })

            // CHEQUE
            ->addColumn('confirm_cheque_number', function ($row) {
                return $row->invoice_payment_cheque_number ?? '-';
            })

            // CONFIRM AMOUNT
            ->addColumn('confirm_payment_amount', function ($row) {
                return number_format($row->invoice_payment_amount ?? 0, 2);
            })

            // NOTE
            ->addColumn('confirm_note', function ($row) {
                return $row->invoice_payment_confirm_note ?? '-';
            })

            // STATUS (FIXED LOGIC)
            ->addColumn('status', function ($row) {

                if (!$row->payment_invoice_number) {
                    return 'Not Open';
                }

                if ($row->invoice_payment_confirm) {
                    return 'Completed';
                }

                return 'Pending';
            })

            // FEE NAME
            ->editColumn('fee_name', function ($row) {

                if ($row->invoice->invoice_type === 'fee') {
                    return optional($row->invoice->invoice_item->first()?->fee_type)->name ?? '-';
                }

                if ($row->invoice->invoice_type === 'course') {
                    return optional($row->invoice->paidCourses->first()?->course)->name ?? '-';
                }

                if ($row->invoice->invoice_type === 'nvq') {
                    return 'NVQ - ' . (optional($row->invoice->paidCourses->first()?->course)->name ?? '-');
                }

                if ($row->invoice->invoice_type === 'item_sell') {
                    return 'Item Sell';
                }

                return '-';
            })

            // DATE
            ->addColumn('date', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
            })

            // NOTE
            ->addColumn('note', function ($row) {
                return $row->invoice_payment_note ?? '-';
            })

            // ACTION
            // ── PERMISSION GATE (Section 28) ──────────────────────────────────
            // Same 'invoice-payments.print' key used by pendingDatatable() below —
            // it's the exact same route on both tables.
            ->addColumn('action', function ($row) {

                if ($row->payment_invoice_number && Auth::user()->hasPermission('invoice-payments.print')) {
                    return '<a href="' . route('invoice-payments.print', $row->payment_invoice_number) . '"
                    target="_blank"
                    class="primary-btn small btn-primary text-white">
                    <i class="fa-solid fa-print"></i> Reprint
                </a>';
                }

                return '-';
            })

            ->rawColumns(['action', 'select'])
            ->make(true);
    }


    public function pendingDatatable(Request $request)
    {

        $query = InvoicesPayments::query()
            ->where('payment_method', 'Invoice')
            ->whereNotNull('payment_invoice_number')
            ->selectRaw("
            payment_invoice_number,
            MIN(id) as id,
            MAX(created_at) as created_at,
            MAX(payment_invoice_open_date) as payment_invoice_open_date,
 
 
            SUM(amount) as total_amount,
            SUM(invoice_payment_amount) as total_confirm_amount,
 
            COUNT(*) as total_count,
            SUM(CASE WHEN invoice_payment_confirm = 1 THEN 1 ELSE 0 END) as confirmed_count,
            SUM(CASE WHEN invoice_payment_confirm IS NULL THEN 1 ELSE 0 END) as pending_count,
            MAX(CASE WHEN invoice_payment_confirm = 1 THEN updated_at END) as last_confirm_date
        ")

            ->groupBy('payment_invoice_number')
            ->orderByRaw("pending_count DESC")
            ->orderBy('created_at', 'desc');

        // Branch filter
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // ----- DATE RANGE FILTER -----
        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);

            $from = isset($dates[0]) && !empty($dates[0]) ? $dates[0] : null;
            $to = isset($dates[1]) && !empty($dates[1]) ? $dates[1] : $from;

            if ($from) {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();

                if ($request->payment_status === 'completed') {
                    $query->havingRaw('last_confirm_date >= ?', [$fromDate])
                        ->havingRaw('last_confirm_date <= ?', [$toDate]);
                } else {
                    $query->whereDate('payment_invoice_open_date', '>=', $fromDate)
                        ->whereDate('payment_invoice_open_date', '<=', $toDate);
                }
            }
        }

        // ----- STATUS FILTER -----
        if ($request->payment_status && $request->payment_status !== 'all') {

            if ($request->payment_status === 'pending') {
                $query->having('pending_count', '>', 0);
            } elseif ($request->payment_status === 'completed') {
                $query->having('pending_count', '=', 0);
            }
        }

        if ($request->has('search') && $request->search['value'] != '') {

            $search = $request->search['value'];

            $query->where(function ($q) use ($search) {

                $q->where('payment_invoice_number', 'like', "%{$search}%")

                    ->orWhereExists(function ($sub) use ($search) {

                        $sub->select(DB::raw(1))
                            ->from('invoices_payments as ip')
                            ->leftJoin('fee_invoices as i', 'i.id', '=', 'ip.invoice_id')
                            ->leftJoin('students as s', 's.id', '=', 'i.student_id')
                            ->leftJoin('student_categories as sc', 'sc.id', '=', 's.student_category_id')
                            ->leftJoin('branches as b', 'b.id', '=', 'i.branch_id')

                            ->whereColumn(
                                'ip.payment_invoice_number',
                                'invoices_payments.payment_invoice_number'
                            )

                            ->where(function ($qq) use ($search) {

                                $qq->where('ip.customer_name', 'like', "%{$search}%")
                                    ->orWhere('s.student_name', 'like', "%{$search}%")
                                    ->orWhere('s.admission_number', 'like', "%{$search}%")
                                    ->orWhere('sc.category_name', 'like', "%{$search}%")
                                    ->orWhere('b.name', 'like', "%{$search}%");
                            });
                    });
            });
        }


        return DataTables::of($query)

            // checkbox
            // ── PERMISSION GATE (Section 28) ──────────────────────────────────
            // No checkbox if the role can't do either bulk action on THIS window
            // (Payment Update / Back) — same keys invoice-payments.blade.php's own
            // $canUpdateInvoicePayment/$canBackInvoicePayment already gate.
            ->addColumn('select', function ($row) {

                $user = Auth::user();
                if (
                    !$user->hasPermission('invoice-payments.bulk-pending') &&
                    !$user->hasPermission('invoice-payments.bulk-back')
                ) {
                    return '';
                }

                $status = $row->pending_count > 0 ? 'pending' : 'completed';
                return '<label class="custom-checkbox">
            <input type="checkbox"
                class="payment-checkbox"
                value="' . $row->payment_invoice_number . '"
                data-status="' . $status . '"
                data-total="' . $row->total_amount . '"
                data-confirm="' . $row->total_confirm_amount . '">
            <span class="custom-checkbox-indicator"></span>
        </label>';
            })

            // invoice number
            ->addColumn('invoice_receipt_no', function ($row) {
                $payment = InvoicesPayments::with('invoice')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->payment_invoice_number ?? '-';
            })


            // customer
            ->addColumn('customer_name', function ($row) {
                return InvoicesPayments::where('payment_invoice_number', $row->payment_invoice_number)
                    ->value('customer_name') ?? '-';
            })

            // branch
            ->addColumn('branch', function ($row) {
                $payment = InvoicesPayments::with('invoice.branch')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->invoice?->branch?->name ?? '-';
            })

            // student
            ->addColumn('student_name', function ($row) {
                $payment = InvoicesPayments::with('invoice.student')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->invoice?->student?->student_name ?? '-';
            })

            ->addColumn('student_category', function ($row) {
                $payment = InvoicesPayments::with('invoice.student')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->invoice?->student?->studentCategory?->category_name ?? '-';
            })

            ->addColumn('admission_number', function ($row) {
                $payment = InvoicesPayments::with('invoice.student')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->invoice?->student?->admission_number ?? '-';
            })



            // total amount
            ->addColumn('amount', function ($row) {
                return number_format($row->total_amount, 2);
            })

            // confirm amount
            ->addColumn('confirm_amount', function ($row) {
                return number_format($row->total_confirm_amount, 2);
            })

            // status
            ->addColumn('status', function ($row) {

                if ($row->pending_count > 0) {
                    return 'Pending';
                }

                return 'Completed';
            })

            // date
            ->addColumn('create_date', function ($row) {
                return $row->payment_invoice_open_date ? date('Y-m-d', strtotime($row->payment_invoice_open_date)) : '-';
            })

            ->addColumn('note', function ($row) {
                $payment = InvoicesPayments::with('invoice.student')
                    ->where('payment_invoice_number', $row->payment_invoice_number)
                    ->first();

                return $payment?->invoice_payment_note ?? '-';
            })

            ->addColumn('complete_date', function ($row) {

                if ($row->pending_count == 0) {
                    // Completed - get last confirmed payment's updated date
                    $lastPayment = InvoicesPayments::where('payment_invoice_number', $row->payment_invoice_number)
                        ->where('invoice_payment_confirm', 1)
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    return $lastPayment?->updated_at ? date('Y-m-d', strtotime($lastPayment->updated_at)) : '-';
                }

                return '-';
            })

            // action
            // ── PERMISSION GATE (Section 28) ──────────────────────────────────
            ->addColumn('action', function ($row) {

                if (!Auth::user()->hasPermission('invoice-payments.print')) {
                    return '-';
                }

                return '<a href="' . route('invoice-payments.print', $row->payment_invoice_number) . '"
            target="_blank"
            class="btn btn-primary btn-sm">
            <i class="fa-solid fa-print"></i> Print
        </a>';
            })

            ->rawColumns(['select', 'action'])
            ->make(true);
    }
    function getBranchCode($branchName)
    {
        // Take first 2 letters of the branch name in uppercase
        return strtoupper(substr($branchName, 0, 2));
    }
    public function bulkNotOpen(Request $request)
    {
        $ids = $request->payment_ids;
        if (empty($ids)) {
            return response()->json(['message' => 'No payments selected.'], 400);
        }

        $branch = Auth::user()->branches->first();
        $branch = $branch->name;
        $branchCode = $branch ? $this->getBranchCode($branch) : 'XX';


        $lastPaymentNumber = InvoicesPayments::where('payment_invoice_number', 'like', "INV-P-{$branchCode}-%")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(payment_invoice_number, "-", -1) AS UNSIGNED)) as max_number')
            ->value('max_number');

        $lastNumber = $lastPaymentNumber ?? 0;

        $newInvoiceNumber = 'INV-P-' . $branchCode . '-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);


        foreach ($ids as $id) {
            $payment = InvoicesPayments::find($id);
            if (!$payment) continue;

            $payment->payment_invoice_number = $newInvoiceNumber;
            $payment->payment_invoice_open_date = now();


            $payment->customer_name = $request->customer_name;
            $payment->invoice_payment_note = $request->note;
            $payment->update_user_id = Auth::id();
            $payment->save();
        }

        return response()->json(['message' => 'Selected payments updated to Pending with invoice numbers.', 'invoice_number' => $newInvoiceNumber]);
    }
    public function bulkPending(Request $request)
    {
        DB::beginTransaction();

        try {
            $ids = $request->payment_ids;

            $remainingAmount = (float) $request->amount;



            $payments = InvoicesPayments::whereIn('payment_invoice_number', $ids)
                ->orderBy('id', 'asc')
                ->get();

            if (!$payments->isEmpty()) {
                $receipt = InvoicePaymentReceipt::create([
                    'receipt_number' => 'RCPT-' . date('YmdHis'),
                    'cheque_number'  => $request->cheque_number,
                    'total_amount'   => $request->amount,
                    'note'           => $request->note,
                    'created_by'     => Auth::id(),
                ]);


                foreach ($payments as $payment) {

                    if ($remainingAmount <= 0) break;

                    $alreadyPaid = (float) $payment->invoice_payment_amount;
                    $totalAmount = (float) $payment->amount;

                    $balance = $totalAmount - $alreadyPaid;

                    if ($balance <= 0) continue;

                    $paidNow = min($remainingAmount, $balance);

                    $payment->invoice_payment_amount = $alreadyPaid + $paidNow;

                    if (($alreadyPaid + $paidNow) >= $totalAmount) {
                        $payment->invoice_payment_confirm = true;
                    }

                    $payment->invoice_payment_cheque_number = $request->cheque_number;
                    $payment->invoice_payment_confirm_note = $request->note;
                    $payment->update_user_id = Auth::id();
                    $payment->save();

                    InvoicePaymentReceiptItem::create([
                        'receipt_id' => $receipt->id,
                        'invoice_payment_id' => $payment->id,
                        'paid_amount' => $paidNow,
                    ]);

                    // 🔹 Reduce remaining
                    $remainingAmount -= $paidNow;
                }
            }



            DB::commit();

            return response()->json([
                'message' => 'Payments processed successfully.',
                'receipt_id' => $receipt->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error processing payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function printInvoice($invoice_number)
    {
        $payments = InvoicesPayments::with([
            'invoice.student',
            'invoice.invoice_item.fee_type',
            'invoice.paidCourses.course'
        ])
            ->where('payment_invoice_number', $invoice_number)
            ->get();

        if ($payments->isEmpty()) {
            abort(404);
        }

        // Calculate totals
        $total = $payments->sum('amount');


        return view('pages.student.fees.invoice-print', compact('payments', 'invoice_number', 'total'));
    }


    public function bulkBack(Request $request)
    {
        DB::beginTransaction();

        try {

            $ids = $request->payment_ids;

            if (empty($ids)) {
                return response()->json(['message' => 'No payments selected.'], 400);
            }

            foreach ($ids as $invoiceNumber) {

                $payments = InvoicesPayments::where('payment_invoice_number', $invoiceNumber)->get();

                if ($payments->isEmpty()) continue;

                // =========================
                // CHECK IF ANY RECEIPT EXISTS
                // =========================
                $receiptItem = InvoicePaymentReceiptItem::whereIn(
                    'invoice_payment_id',
                    $payments->pluck('id')
                )->latest('id')->first();

                if ($receiptItem) {

                    // =========================
                    // ROLLBACK RECEIPT (PARTIAL / FULL PAYMENT DONE)
                    // =========================
                    $receiptId = $receiptItem->receipt_id;

                    $items = InvoicePaymentReceiptItem::where('receipt_id', $receiptId)->get();

                    foreach ($items as $item) {

                        $payment = InvoicesPayments::find($item->invoice_payment_id);

                        if (!$payment) continue;

                        // rollback amount
                        $payment->invoice_payment_amount = max(
                            0,
                            (float)$payment->invoice_payment_amount - (float)$item->paid_amount
                        );

                        // reset confirm
                        if ($payment->invoice_payment_amount < $payment->amount) {
                            $payment->invoice_payment_confirm = null;
                        }

                        $payment->update_user_id = Auth::id();

                        $payment->save();

                        $item->delete();
                    }

                    InvoicePaymentReceipt::where('id', $receiptId)->delete();
                } else {

                    // =========================
                    // NO PAYMENT DONE YET (JUST PENDING)
                    // =========================
                    foreach ($payments as $payment) {

                        $payment->payment_invoice_number = null;
                        $payment->invoice_payment_note = null;
                        $payment->customer_name = null;
                        $payment->payment_invoice_open_date = null;

                        $payment->update_user_id = Auth::id();
                        $payment->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Successfully moved back to Not Open'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

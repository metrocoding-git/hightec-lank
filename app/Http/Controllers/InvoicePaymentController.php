<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FeeInvoices;
use App\Models\InvoicesPayments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class InvoicePaymentController extends Controller
{

    public function index()
    {
        $branches = Branch::all();
        return view('pages.student.fees.invoice-payments', compact('branches'));
    }

    public function datatable(Request $request)
    {
        // Base query: only payments where payment_method = 'Invoice'
        $query = InvoicesPayments::with(['invoice.student', 'invoice.branch', 'branch', 'invoice.paidCourses', 'invoice.student.studentCategory'])
            ->where('payment_method', 'Invoice')
            ->where('payment_type', '!=', 'refund')
            ->where(function ($q) {
                $q->where('payment_status', '!=', 'refunded')
                    ->orWhereNull('payment_status');
            })
            ->whereHas('invoice', function ($q) {
                $q->where(function ($invoiceQ) {
                    $invoiceQ->where('payment_status', '!=', 'returned')
                        ->orWhereNull('payment_status');
                });
            });
        // Optional filters
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->payment_status) {
            if ($request->payment_status == 'not_open') {
                $query->whereNull('payment_invoice_number');
            } elseif ($request->payment_status == 'pending') {
                $query->whereNotNull('payment_invoice_number')
                    ->whereNull('invoice_payment_confirm')
                    ->whereHas('invoice', function ($q) {
                        $q->where(function ($invoiceQ) {
                            $invoiceQ->where('payment_status', '!=', 'full')
                                ->orWhereNull('payment_status');
                        })
                        ->where(function ($invoiceQ) {
                            $invoiceQ->where('due_amount', '>', 0)
                                ->orWhereNull('due_amount');
                        });
                    });
            } elseif ($request->payment_status == 'completed') {
                $query->where(function ($q) {
                    $q->where('invoice_payment_confirm', true)
                        ->orWhereHas('invoice', function ($invoiceQ) {
                            $invoiceQ->where('payment_status', 'full')
                                ->orWhere('due_amount', '<=', 0);
                        });
                });
            }
        }

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


        return DataTables::of($query)
            ->addColumn('select', function ($row) {
                $isInvoiceSettled = (optional($row->invoice)->payment_status === 'full')
                    || ((float) (optional($row->invoice)->due_amount ?? 0) <= 0
                        && (float) (optional($row->invoice)->total_amount ?? 0) > 0);

                if ($row->invoice_payment_confirm || $isInvoiceSettled) {
                    $status = 'completed';
                } elseif ($row->payment_invoice_number != null) {
                    $status = 'pending';
                } else {
                    $status = 'not_open';
                }
                return '<input type="checkbox" class="payment-checkbox" value="' . $row->id . '" data-status="' . $status . '">';
            })
            ->addColumn('invoice_no', function ($row) {
                return $row->invoice->single_invoice
                    ? $row->invoice->main_invoice_number
                    : 'INV-' . $row->invoice_id;
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer_name ?? '-';
            })
            ->addColumn('invoice_receipt_no', function ($row) {
                return $row->payment_invoice_number ?? '-';
            })
            ->addColumn('branch', function ($row) {
                return $row->invoice->branch->name ?? '-';
            })
            ->addColumn('student_name', function ($row) {
                return $row->invoice->student->student_name ?? '-';
            })
            ->addColumn('student_category', function ($row) {
                return $row->invoice->student->studentCategory->category_name ?? '-';
            })

            ->addColumn('admission_number', function ($row) {
                return $row->invoice->student->admission_number ?? '-';
            })
            ->addColumn('amount', function ($row) {
                $confirmedAmount = (float) ($row->invoice_payment_amount ?? 0);
                $remaining = max((float) $row->amount - $confirmedAmount, 0);
                return number_format($remaining, 2);
            })
            ->addColumn('invoice_paid_amount', function ($row) {
                return number_format((float) (optional($row->invoice)->paid_amount ?? 0), 2);
            })
            ->addColumn('invoice_pending_amount', function ($row) {
                $total = (float) (optional($row->invoice)->total_amount ?? 0);
                $paid = (float) (optional($row->invoice)->paid_amount ?? 0);
                return number_format(max($total - $paid, 0), 2);
            })
            ->addColumn('balance_amount', function ($row) {
                return number_format((float) $row->amount, 2);
            })

            ->addColumn('confirm_cheque_number', function ($row) {
                return $row->invoice_payment_cheque_number ?? '-';
            })
            ->addColumn('confirm_payment_amount', function ($row) {
                return $row->invoice_payment_amount ? number_format($row->invoice_payment_amount, 2) : '-';
            })
            ->addColumn('confirm_note', function ($row) {
                return $row->invoice_payment_confirm_note ?? '-';
            })
            ->addColumn('status', function ($row) {
                $isInvoiceSettled = (optional($row->invoice)->payment_status === 'full')
                    || ((float) (optional($row->invoice)->due_amount ?? 0) <= 0
                        && (float) (optional($row->invoice)->total_amount ?? 0) > 0);

                if ($row->invoice_payment_confirm || $isInvoiceSettled) {
                    return 'Completed';
                } elseif ((float) ($row->invoice_payment_amount ?? 0) > 0) {
                    return 'Partial';
                } elseif ($row->payment_invoice_number != null) {
                    return 'Pending';
                } else {
                    return 'Not Open';
                }
            })
            ->addColumn('fee_name', function ($row) {
                return $row->payment_method;
            })
            ->editColumn('fee_name', function ($row) {
                if ($row->invoice->invoice_type === 'fee') {
                    return optional($row->invoice->invoice_item->first()?->fee_type)->name ?? '-';
                }
                if ($row->invoice->invoice_type === 'course') {
                    return optional(optional($row->invoice->paidCourses)->course)->name ?? '-';
                }
                if ($row->invoice->invoice_type === 'nvq') {
                    return  'NCQ - ' . (optional(optional($row->invoice->paidCourses)->course)->name) ?? '-';
                }
                if ($row->invoice->invoice_type === 'item_sell') {
                    return  'Item Sell';
                }
                return '-';
            })


            ->addColumn('date', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
            })
            ->addColumn('note', function ($row) {
                return $row->invoice_payment_note ?? '-';
            })
            ->addColumn('action', function ($row) {

                if ($row->payment_invoice_number) {
                    return '<a href="' . route('invoice-payments.print', $row->payment_invoice_number) . '" 
                    target="_blank" 
                    class="primary-btn small btn-primary text-white">
                    Reprint
                </a>';
                }

                return '-';
            })
            ->rawColumns(['action', 'select'])
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

            $payment->customer_name = $request->customer_name;
            $payment->invoice_payment_note = $request->note;
            $payment->update_user_id = Auth::id();
            $payment->save();
        }

        return response()->json(['message' => 'Selected payments updated to Pending with invoice numbers.', 'invoice_number' => $newInvoiceNumber]);
    }
    public function bulkPending(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'required|integer|exists:invoices_payments,id',
            'amount' => 'required|numeric|min:0.01',
            'cheque_number' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $paymentIds = $request->payment_ids;
        $incomingAmount = (float) $request->amount;
        $remainingAmount = $incomingAmount;
        $allocatedAmount = 0;

        DB::transaction(function () use ($paymentIds, $request, &$remainingAmount, &$allocatedAmount) {
            $payments = $this->getPaymentsForBulkProcessing($paymentIds, true);

            foreach ($payments as $payment) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $invoice = FeeInvoices::find($payment->invoice_id);
                $invoiceOutstanding = $invoice
                    ? max((float) $invoice->total_amount - (float) $invoice->paid_amount, 0)
                    : 0;

                $alreadyConfirmed = (float) ($payment->invoice_payment_amount ?? 0);
                $rowOutstanding = max((float) $payment->amount - $alreadyConfirmed, 0);

                if ($rowOutstanding <= 0) {
                    $isInvoiceFull = $invoice && (float) $invoice->paid_amount >= (float) $invoice->total_amount;
                    $payment->invoice_payment_confirm = $isInvoiceFull;
                    $payment->payment_status = $payment->invoice_payment_confirm ? 'full' : 'partial';
                    $payment->update_user_id = Auth::id();
                    $payment->save();
                    continue;
                }

                // Never allocate more than invoice due, even if row amount is higher.
                $applyAmount = min($remainingAmount, $rowOutstanding, $invoiceOutstanding);
                if ($applyAmount <= 0) {
                    $isInvoiceFull = $invoice && (float) $invoice->paid_amount >= (float) $invoice->total_amount;
                    if ($isInvoiceFull) {
                        $payment->invoice_payment_confirm = true;
                        $payment->payment_status = 'full';
                        $payment->update_user_id = Auth::id();
                        $payment->save();
                    }
                    continue;
                }

                $payment->invoice_payment_cheque_number = $request->cheque_number;
                $payment->invoice_payment_confirm_note = $request->note;
                $payment->invoice_payment_amount = $alreadyConfirmed + $applyAmount;
                if ($invoice) {
                    $invoiceApplied = min($applyAmount, $invoiceOutstanding);

                    if ($invoiceApplied > 0) {
                        $invoice->paid_amount = (float) $invoice->paid_amount + $invoiceApplied;
                        $invoice->due_amount = max((float) $invoice->total_amount - (float) $invoice->paid_amount, 0);

                        if ((float) $invoice->paid_amount <= 0) {
                            $invoice->payment_status = 'not';
                        } elseif ((float) $invoice->paid_amount < (float) $invoice->total_amount) {
                            $invoice->payment_status = 'partial';
                        } else {
                            $invoice->payment_status = 'full';
                        }

                        $invoice->save();

                        $allocatedAmount += $invoiceApplied;
                        $remainingAmount -= $invoiceApplied;
                    }
                }

                $isInvoiceFull = $invoice && (float) $invoice->paid_amount >= (float) $invoice->total_amount;
                // Completion is finalized by invoice settlement.
                $payment->invoice_payment_confirm = $isInvoiceFull;
                $payment->payment_status = $payment->invoice_payment_confirm ? 'full' : 'partial';
                $payment->update_user_id = Auth::id();
                $payment->save();
            }

            // Final consistency pass: sync all selected rows to invoice settlement state.
            $selectedPayments = InvoicesPayments::with('invoice')
                ->whereIn('id', $paymentIds)
                ->lockForUpdate()
                ->get();

            foreach ($selectedPayments as $selectedPayment) {
                $invoice = $selectedPayment->invoice;
                $isInvoiceSettled = $invoice
                    && (
                        (float) ($invoice->paid_amount ?? 0) >= (float) ($invoice->total_amount ?? 0)
                        || (float) ($invoice->due_amount ?? 0) <= 0
                    );

                $selectedPayment->invoice_payment_confirm = $isInvoiceSettled ? true : null;
                $selectedPayment->payment_status = $isInvoiceSettled ? 'full' : ((float) ($selectedPayment->invoice_payment_amount ?? 0) > 0 ? 'partial' : null);
                $selectedPayment->update_user_id = Auth::id();
                $selectedPayment->save();
            }
        });

        $message = 'Payment allocation updated successfully.';
        if ($remainingAmount > 0) {
            $message .= ' Unallocated amount: ' . number_format($remainingAmount, 2);
        }

        return response()->json([
            'message' => $message,
            'allocated_amount' => number_format($allocatedAmount, 2),
            'remaining_amount' => number_format($remainingAmount, 2),
        ]);
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
        $ids = $request->payment_ids;
        $status = $request->status;

        if (empty($ids)) {
            return response()->json(['message' => 'No payments selected.'], 400);
        }

        $revertedAmount = 0;

        if ($status === 'completed') {
            DB::transaction(function () use ($ids, &$revertedAmount) {
                $payments = $this->getPaymentsForBulkProcessing($ids, true);

                foreach ($payments as $payment) {
                    $appliedAmount = (float) ($payment->invoice_payment_amount ?? 0);
                    if ($appliedAmount > 0) {
                        $this->reverseAppliedAmountFromInvoice($payment->invoice_id, $appliedAmount);
                        $revertedAmount += $appliedAmount;
                    }

                    // Revert to pending state (keep payment invoice number/customer info)
                    $payment->invoice_payment_confirm = null;
                    $payment->invoice_payment_cheque_number = null;
                    $payment->invoice_payment_amount = null;
                    $payment->invoice_payment_confirm_note = null;
                    $payment->payment_status = null;
                    $payment->update_user_id = Auth::id();
                    $payment->save();
                }
            });

            return response()->json([
                'message' => 'Reverted to Pending successfully.',
                'reverted_amount' => number_format($revertedAmount, 2),
            ]);
        }

        if ($status === 'pending') {
            DB::transaction(function () use ($ids, &$revertedAmount) {
                $payments = $this->getPaymentsForBulkProcessing($ids, true);

                foreach ($payments as $payment) {
                    $appliedAmount = (float) ($payment->invoice_payment_amount ?? 0);
                    if ($appliedAmount > 0) {
                        $this->reverseAppliedAmountFromInvoice($payment->invoice_id, $appliedAmount);
                        $revertedAmount += $appliedAmount;
                    }

                    // Revert to not open state
                    $payment->payment_invoice_number = null;
                    $payment->customer_name = null;
                    $payment->invoice_payment_note = null;
                    $payment->invoice_payment_confirm = null;
                    $payment->invoice_payment_cheque_number = null;
                    $payment->invoice_payment_amount = null;
                    $payment->invoice_payment_confirm_note = null;
                    $payment->payment_status = null;
                    $payment->update_user_id = Auth::id();
                    $payment->save();
                }
            });

            return response()->json([
                'message' => 'Reverted to Not Open successfully.',
                'reverted_amount' => number_format($revertedAmount, 2),
            ]);
        }

        return response()->json(['message' => 'Invalid status.'], 400);
    }

    private function getPaymentsForBulkProcessing(array $paymentIds, bool $lockForUpdate = false)
    {
        $query = InvoicesPayments::with('invoice')->whereIn('id', $paymentIds);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->get()
            ->sortBy(function ($payment) {
                $invoice = $payment->invoice;
                $invoiceCreateDate = optional($invoice)->create_date
                    ? optional($invoice)->create_date->format('Ymd')
                    : '99991231';
                $invoiceCreatedAt = optional($invoice)->created_at
                    ? optional($invoice)->created_at->format('YmdHis')
                    : '99991231235959';

                return $invoiceCreateDate
                    . '-'
                    . $invoiceCreatedAt
                    . '-'
                    . str_pad((string) ($payment->invoice_id ?? 0), 10, '0', STR_PAD_LEFT)
                    . '-'
                    . str_pad((string) $payment->id, 10, '0', STR_PAD_LEFT);
            })
            ->values();
    }

    private function reverseAppliedAmountFromInvoice($invoiceId, float $appliedAmount): void
    {
        if ($appliedAmount <= 0) {
            return;
        }

        $invoice = FeeInvoices::where('id', $invoiceId)->lockForUpdate()->first();
        if (!$invoice) {
            return;
        }

        $currentPaid = (float) ($invoice->paid_amount ?? 0);
        $totalAmount = (float) ($invoice->total_amount ?? 0);

        $newPaidAmount = max($currentPaid - $appliedAmount, 0);
        $invoice->paid_amount = $newPaidAmount;
        $invoice->due_amount = max($totalAmount - $newPaidAmount, 0);

        if ($newPaidAmount <= 0) {
            $invoice->payment_status = 'not';
        } elseif ($newPaidAmount < $totalAmount) {
            $invoice->payment_status = 'partial';
        } else {
            $invoice->payment_status = 'full';
        }

        $invoice->save();
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InvoicesPayments;
use Illuminate\Support\Facades\Auth;
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
                $q->where('payment_status', '!=', 'returned');
            });
        // Optional filters
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->payment_status) {
            if ($request->payment_status == 'not_open') {
                $query->where('payment_invoice_number', null);
            } elseif ($request->payment_status == 'pending') {
                $query->where('payment_invoice_number', '!=', null)->where('invoice_payment_confirm', null);
            } elseif ($request->payment_status == 'completed') {
                $query->where('invoice_payment_confirm', true);
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
                if ($row->invoice_payment_confirm) {
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
                return number_format($row->amount, 2);
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
                if ($row->invoice_payment_confirm) {
                    return 'Completed';
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
        $ids = $request->payment_ids;
        foreach ($ids as $id) {
            $payment = InvoicesPayments::find($id);
            if (!$payment) continue;
            $payment->invoice_payment_confirm = true;
            $payment->invoice_payment_cheque_number = $request->cheque_number;
            $payment->invoice_payment_amount = $request->amount;
            $payment->invoice_payment_confirm_note = $request->note;
            $payment->update_user_id = Auth::id();
            $payment->save();
        }


        return response()->json(['message' => 'Selected payments confirmed successfully.']);
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

        if ($status === 'completed') {
            $payments = InvoicesPayments::whereIn('id', $ids)->get();

            foreach ($payments as $payment) {
                // Revert to pending
                $payment->invoice_payment_confirm = null;

                $payment->cheque_number = null;
                $payment->amount = null;
                $payment->invoice_payment_note = null;

                // Track who reverted
                $payment->update_user_id = Auth::id();

                $payment->save();
            }

            return response()->json(['message' => 'Reverted to Pending successfully.']);
        }

        if ($status === 'pending') {
            // Get all selected payments
            $payments = InvoicesPayments::whereIn('id', $ids)->get();

            foreach ($payments as $payment) {
                $payment->payment_invoice_number = null;
                $payment->customer_name = null;
                $payment->invoice_payment_note = null;

                // Track the user who reverted
                $payment->update_user_id = Auth::id();

                $payment->save();
            }

            return response()->json(['message' => 'Reverted to Not Open successfully.']);
        }

        return response()->json(['message' => 'Invalid status.'], 400);
    }
}

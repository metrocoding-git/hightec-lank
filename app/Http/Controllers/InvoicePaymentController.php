<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InvoicesPayments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class InvoicePaymentController extends Controller
{
    private function normalizePaymentIds($rawIds): array
    {
        if ($rawIds === null) {
            return [];
        }

        $items = is_array($rawIds) ? $rawIds : [$rawIds];

        return collect($items)
            ->flatMap(function ($item) {
                return explode(',', (string) $item);
            })
            ->map(function ($id) {
                return (int) trim($id);
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->unique()
            ->values()
            ->all();
    }

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


        $payments = $query->get();

        $resolveFeeName = function ($payment) {
            if (!$payment->invoice) {
                return '-';
            }

            if ($payment->invoice->invoice_type === 'fee') {
                return optional($payment->invoice->invoice_item->first()?->fee_type)->name ?? '-';
            }
            if ($payment->invoice->invoice_type === 'course') {
                return optional(optional($payment->invoice->paidCourses)->course)->name ?? '-';
            }
            if ($payment->invoice->invoice_type === 'nvq') {
                return 'NCQ - ' . (optional(optional($payment->invoice->paidCourses)->course)->name ?? '-');
            }
            if ($payment->invoice->invoice_type === 'item_sell') {
                return 'Item Sell';
            }

            return '-';
        };

        $rows = $payments
            ->groupBy(function ($payment) {
                if (!empty($payment->payment_invoice_number)) {
                    $normalizedReceipt = strtoupper(trim((string) $payment->payment_invoice_number));
                    return 'group:' . $normalizedReceipt;
                }
                return 'single:' . $payment->id;
            })
            ->map(function ($group) use ($resolveFeeName) {
                $first = $group->first();

                $invoiceNumbers = $group->map(function ($p) {
                    if (!$p->invoice) {
                        return '-';
                    }

                    return $p->invoice->single_invoice
                        ? $p->invoice->main_invoice_number
                        : 'INV-' . $p->invoice_id;
                })->filter()->unique()->values();

                $studentNames = $group->map(fn($p) => $p->invoice?->student?->student_name)->filter()->unique()->values();
                $categories = $group->map(fn($p) => $p->invoice?->student?->studentCategory?->category_name)->filter()->unique()->values();
                $admissionNumbers = $group->map(fn($p) => $p->invoice?->student?->admission_number)->filter()->unique()->values();
                $feeNames = $group->map(fn($p) => $resolveFeeName($p))->filter()->unique()->values();

                $row = new \stdClass();
                $row->ids = $group->pluck('id')->implode(',');
                $row->status_key = $first->invoice_payment_confirm ? 'completed' : (!empty($first->payment_invoice_number) ? 'pending' : 'not_open');
                $row->invoice_no = $invoiceNumbers->first() ?? '-';
                $row->invoice_receipt_no = !empty($first->payment_invoice_number)
                    ? strtoupper(trim((string) $first->payment_invoice_number))
                    : '-';
                $row->customer_name = $first->customer_name ?? '-';
                $row->branch = $first->invoice?->branch?->name ?? '-';
                $row->student_name = $studentNames->first() ?? '-';
                $row->student_category = $categories->first() ?? '-';
                $row->admission_number = $admissionNumbers->first() ?? '-';
                $row->fee_name = $feeNames->first() ?? '-';
                $row->amount = number_format((float) $group->sum('amount'), 2);
                $row->confirm_cheque_number = $first->invoice_payment_cheque_number ?? '-';
                $row->confirm_payment_amount = $first->invoice_payment_amount ? number_format((float) $first->invoice_payment_amount, 2) : '-';
                $row->confirm_note = $first->invoice_payment_confirm_note ?? '-';
                $row->status = $first->invoice_payment_confirm ? 'Completed' : (!empty($first->payment_invoice_number) ? 'Pending' : 'Not Open');
                $row->date = $first->created_at ? $first->created_at->format('Y-m-d') : '-';
                $row->note = $first->invoice_payment_note ?? '-';
                $row->action = $first->payment_invoice_number
                    ? '<a href="' . route('invoice-payments.print', $first->payment_invoice_number) . '" target="_blank" class="primary-btn small btn-primary text-white">Reprint</a>'
                    : '-';

                return $row;
            })
            ->values();

        return DataTables::of($rows)
            ->addColumn('select', function ($row) {
                return '<input type="checkbox" class="payment-checkbox" value="' . $row->ids . '" data-status="' . $row->status_key . '">';
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
        $ids = $this->normalizePaymentIds($request->payment_ids);
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
        $ids = $this->normalizePaymentIds($request->payment_ids);

        if (empty($ids)) {
            return response()->json(['message' => 'No payments selected.'], 400);
        }

        $payments = InvoicesPayments::whereIn('id', $ids)->orderBy('id')->get();
        if ($payments->isEmpty()) {
            return response()->json(['message' => 'No valid payments found.'], 400);
        }

        // Keep one common receipt number for all selected rows so they stay grouped as one record.
        $groupReceiptNumber = $payments
            ->pluck('payment_invoice_number')
            ->filter(function ($value) {
                return !empty($value);
            })
            ->map(function ($value) {
                return strtoupper(trim((string) $value));
            })
            ->first();

        foreach ($payments as $payment) {
            if (!empty($groupReceiptNumber)) {
                $payment->payment_invoice_number = $groupReceiptNumber;
            }

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
        $ids = $this->normalizePaymentIds($request->payment_ids);
        $status = $request->status;

        if (empty($ids)) {
            return response()->json(['message' => 'No payments selected.'], 400);
        }

        if ($status === 'completed') {
            $payments = InvoicesPayments::whereIn('id', $ids)->get();

            foreach ($payments as $payment) {
                // Revert to pending
                $payment->invoice_payment_confirm = null;
                $payment->invoice_payment_cheque_number = null;
                $payment->invoice_payment_amount = null;
                $payment->invoice_payment_confirm_note = null;

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

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Invoices_payments;
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
    $query = Invoices_payments::with(['invoice.student', 'invoice.branch', 'branch', 'invoice.paidCourses'])
        ->where('payment_method', 'Invoice');

    // Optional filters
    if ($request->branch_id) {
        $query->where('branch_id', $request->branch_id);
    }

    if ($request->payment_type) {
        $query->where('payment_type', $request->payment_type);
    }

    return DataTables::of($query)
        ->addColumn('invoice_no', function($row) {
            return $row->invoice->single_invoice 
                ? $row->invoice->main_invoice_number 
                : 'INV-'.$row->invoice_id;
        })
        ->addColumn('branch', function($row) {
            return $row->invoice->branch->name ?? '-';
        })
        ->addColumn('student_name', function($row) {
            return $row->invoice->student->student_name ?? '-';
        })
        ->addColumn('admission_number', function($row) {
            return $row->invoice->student->admission_number ?? '-';
        })
        ->addColumn('amount', function($row) {
            return number_format($row->amount, 2);
        })
        ->addColumn('status', function($row) {
            if ($row->payment_invoice_number != null) {
                return 'Pending';
            } elseif ($row->invoice_payment_confirm) {
                return 'Completed';
            } else {
                return 'Not Open';
            }
        })
        ->addColumn('fee_name', function($row) {
            return $row->payment_method;
        })
        ->editColumn('fee_name', function ($row) {
                if ($row->invoice->invoice_type === 'fee') {
                    return optional($row->invoice->invoice_item->first()?->fee_type)->name ?? '-';
                }
                if ($row->invoice->invoice_type === 'course') {
                    return optional(optional($row->invoice->paidCourses)->course)->name ?? '-';
                }
                return '-';
            })
 

        ->addColumn('date', function($row) {
            return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
        })
        ->addColumn('action', function($row){
            return '<button class="primary-btn small" 
                        data-toggle="modal" 
                        data-target="#paymentStatusModal" 
                        data-payment-id="'.$row->id.'">Update</button>';
        })
        ->rawColumns(['action'])
        ->make(true);
}

    public function updateStatus(Request $request)
    {
        $payment = InvoicePayment::find($request->payment_id);
        $payment->status = $request->status ?? 'completed';
        $payment->save();
        return redirect()->back()->with('success', 'Payment status updated.');
    }
}

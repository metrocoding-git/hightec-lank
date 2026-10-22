<?php

namespace App\Http\Controllers;

use App\Models\ReturnFee;
use App\Models\FeeInvoices;
use App\Models\Branch;
use App\Models\Course;
use App\Models\FoodPayment;
use App\Models\Intake;
use App\Models\InvoicesPayments;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnFeeController extends Controller
{
    public function feeReturn(Request $request)
    {
        $branches = Branch::all();
        $coursess = Course::all();
        $intakes = Intake::all();

        $query = ReturnFee::with(['student', 'role', 'invoice']);

        if ($request->filled('branch')) {
            $branchId = $request->input('branch');
            $query->whereHas('student', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($request->filled('intake')) {
            $intakeId = $request->input('intake');
            $query->whereHas('student.intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($request->filled('course')) {
            $courseId = $request->input('course');
            $query->whereHas('student.courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        $returnDetails = $query->orderBy('created_at', 'desc')->get();

        $branch_id = $request->input('branch');
        $intake_id = $request->input('intake');
        $course_id = $request->input('course');

        return view('pages.student.fees.fee-return', compact('returnDetails', 'coursess', 'intakes', 'branches', 'branch_id', 'intake_id', 'course_id'));
    }

    /**
     * Store a newly created return fee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'admission_number' => 'required|string|max:255',
            'return_user_id' => 'nullable|exists:users,id',
            'balance' => 'required|numeric|min:0',
            'return_amount' => 'required|numeric|min:0.00',
            'invoice_id' => 'required|exists:fee_invoices,id',
            'short_note' => 'nullable|string',
            'status' => 'nullable|boolean'
        ]);

        // Check if return amount is not greater than the current paid amount
        $invoice = FeeInvoices::findOrFail($validated['invoice_id']);

        // "invoice list cant be return if has any ... invoice payment" — an
        // invoice with an "Invoice" payment-method payment that hasn't been
        // refunded yet can't be returned.
        if (FeeInvoices::hasOpenInvoiceMethodPayment($invoice->id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This invoice has an "Invoice" payment that has not been refunded. Refund that payment first, then return the invoice.');
        }

        if ($validated['return_amount'] > $invoice->paid_amount) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Return amount cannot be greater than the paid amount (' . number_format($invoice->paid_amount, 2) . ').');
        }

        // Set default status and return_user_id
        $validated['status'] = $request->has('status') ? 1 : 0;
        $validated['return_user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            // Log the return attempt
            Log::info('Processing return fee', [
                'invoice_id' => $validated['invoice_id'],
                'student_id' => $validated['student_id'],
                'return_amount' => $validated['return_amount'],
                'current_paid' => $invoice->paid_amount
            ]);

            // Create return fee record first
            $returnFee = ReturnFee::create($validated);

            InvoicesPayments::create([
                'invoice_id'      => $invoice->id,
                'amount'          => -$validated['return_amount'],
                'payment_method'  => 'Cash', // same spelling as every other Cash row
                'payment_type'    => 'return', // shown as "Return" (not Refund) in the Collection Report
                'receipt_number'  => 'RET-' . time() . '-' . uniqid(),
                'create_user_id'  => Auth::id(),
                'branch_id'       => $invoice->branch_id,
                'notes'           => $validated['short_note'] ?? 'Return fee processed',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);


            if ($invoice->invoice_type == 'food') {
                $food_payment = FoodPayment::where('invoice_id', $validated['invoice_id'])->first();

                $food_payment->paid_amount = 0.00;
                $food_payment->due_amount = $food_payment->total_food_amount;
                $food_payment->status = 0;
                $food_payment->save();
            }


            // Calculate new paid amount
            $newPaidAmount = $invoice->paid_amount - $validated['return_amount'];

            // Ensure paid amount doesn't go below 0
            $newPaidAmount = max(0, $newPaidAmount);

            // Determine payment status - set to 'returned' if return was processed
            // if ($newPaidAmount == 0) {
            //     $paymentStatus = 'not';
            // } elseif ($newPaidAmount >= $invoice->total_amount) {
            //     $paymentStatus = 'full';
            // } else {
            //     $paymentStatus = 'returned'; // Set to returned when partial payment remains after return
            // }
            $paymentStatus = 'returned';
            // Update invoice
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $paymentStatus
            ]);

            DB::commit();

            Log::info('Return fee processed successfully', [
                'return_fee_id' => $returnFee->id,
                'new_paid_amount' => $newPaidAmount,
                'payment_status' => $paymentStatus
            ]);

            return redirect()->back()
                ->with('success', 'Return fee processed successfully. Invoice updated.');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to process return fee', [
                'invoice_id' => $validated['invoice_id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process return fee: ' . $e->getMessage());
        }
    }

    public function viewFeeInvoice($id)
    {
        $invoice = FeeInvoices::with('invoice_item.fee_type', 'student', 'branch')->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return view('pages.student.fees.partials.invoice-view-content', compact('invoice'));
        }

        return view('pages.student.fees.fee-invoice-view', compact('invoice'));
    }
}

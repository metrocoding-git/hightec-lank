<?php

namespace App\Http\Controllers;

use App\Models\ReturnFee;
use App\Models\Student;
use App\Models\Role;
use App\Models\Fee_invoices as FeeInvoice;
use App\Models\Invoice_item as InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReturnFeeController extends Controller
{
    /**
     * Store a newly created return fee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'admission_number' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'father_name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'return_amount' => 'required|numeric|min:0.01',
            'short_note' => 'nullable|string',
            'status' => 'boolean',
            'invoice_id' => 'required|exists:fee_invoices,id'
        ]);

        // Check if return amount is not greater than balance
        if ($validated['return_amount'] > $validated['balance']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Return amount cannot be greater than the balance amount.');
        }

        $validated['status'] = $request->has('status') ? 1 : 0;

        DB::beginTransaction();
        try {
            // Create return fee record
            ReturnFee::create($validated);

            // Update invoice balance
            $invoice = FeeInvoice::findOrFail($validated['invoice_id']);
            $newPaidAmount = $invoice->paid_amount + $validated['return_amount'];

            // Ensure paid amount doesn't go below 0
            $newPaidAmount = max(0, $newPaidAmount);

            // Update invoice
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'payment_status' => $newPaidAmount == 0 ? 'not' : ($newPaidAmount == $invoice->total_amount ? 'full' : 'partial')
            ]);

            // Update invoice items proportionally
            $this->updateInvoiceItems($invoice, $validated['return_amount']);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Return fee processed successfully. Invoice balance updated.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process return fee: ' . $e->getMessage());
        }
    }

    /**
     * Update invoice items proportionally based on return amount.
     */
    private function updateInvoiceItems(FeeInvoice $invoice, float $returnAmount): void
    {
        $invoiceItems = $invoice->invoice_item;

        if ($invoiceItems->isEmpty()) {
            return;
        }

        $totalAmount = $invoice->total_amount;
        $returnRatio = $returnAmount / $totalAmount;

        foreach ($invoiceItems as $item) {
            $returnFromItem = $item->paid_amount * $returnRatio;
            $newPaidAmount = max(0, $item->paid_amount - $returnFromItem);

            $item->update([
                'paid_amount' => $newPaidAmount
            ]);
        }
    }
}

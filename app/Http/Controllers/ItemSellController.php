<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\Intake;
use App\Models\InventoryItem;
use App\Models\InvoicesPayments;
use App\Models\ItemSellBatch;
use App\Models\ItemSellItem;
use App\Models\ItemInventory;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemSellController extends Controller
{
    public function itemSell()
    {
        $items = InventoryItem::all();
        $classes = Course::all();
        $sections = Intake::all();
        $roles = Role::all();
        $students = Student::all();
        $users = User::all();
        $branches = Auth::user()->branches;
        return view('pages.account.inventory.item-sell', compact('items', 'classes', 'sections', 'roles', 'students', 'users', 'branches'));
    }

    public function saveItemSellData(Request $request)
    {
        // Base validation
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'payment_method' => 'required|string',
            'sell_date' => 'required|string',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer|exists:inventory_items,id',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'totalPaid' => 'nullable|numeric',
            'subTotal' => 'nullable|numeric',
            'totalDue' => 'nullable|numeric',
            'bank_payment_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        $branch = Branch::find($request->branch_id);
        $branchLetter = strtoupper(substr($branch->name, 0, 2));
        $lastRecord = ItemSellBatch::where('branch_id', $request->branch_id)
            ->where('invoice_number', 'like', "INV-S-$branchLetter-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->invoice_number, -4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $invoice_number = "INV-S-" . $branchLetter . "-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Conditional validation
        if ($request->payment_method === 'Bank Transfer') {
            $request->validate([
                'bank_payment_amount' => 'required|numeric|min:0.01',
                'bank_payment_date' => 'required|date',
            ]);
        }

        try {
            $sellDate = Carbon::parse($request->sell_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid sell date format.',
            ], 422);
        }

        try {
            // Handle file upload
            $bankPaymentDocumentPath = null;
            if ($request->hasFile('bank_payment_document')) {
                $bankPaymentDocumentPath = $request->file('bank_payment_document')->store('bank_payment_documents', 'public');
            }

            $bankPaymentDate = null;
            if ($request->payment_method === 'Bank Transfer' && $request->bank_payment_date) {
                $bankPaymentDate = Carbon::parse($request->bank_payment_date)->format('Y-m-d');
            }

            $studentCategoryId = Student::where('id', $request->student)->value('student_category_id');


            // Create ItemSellBatch record
            $itemSellBatch = ItemSellBatch::create([
                'branch_id' => $request->branch_id,
                'payment_method' => $request->payment_method,
                'bank_payment_amount' => $request->payment_method === 'Bank Transfer' ? $request->bank_payment_amount : null,
                'bank_payment_date' => $bankPaymentDate,
                'bank_payment_document' => $request->payment_method === 'Bank Transfer' ? $bankPaymentDocumentPath : null,
                'role_id' => $request->role_id,
                'student_id' => $request->student ?? null,
                'user_id' => $request->staff_id ?? null,
                'others_name' => $request->customer_name ?? null,
                'reference_no' => $request->reference_no,
                'sell_date' => $sellDate,
                'description' => $request->description,
                'total_quantity' => $request->subTotalQuantityValue ?? 0,
                'total_sell_price' => $request->subTotalValue ?? 0,
                'total_paid' => $request->totalPaid ?? 0,
                'total_due' => $request->totalDue ?? 0,
                'status' => ($request->totalDue > 0) ? 'PP' : 'P',
                'created_by' => Auth::id(),
                'student_category_id' => $studentCategoryId,
                'invoice_number' => $invoice_number
            ]);

            $payment_status = ($request->totalDue > 0) ? 'partial' : 'full';

            $fee_invoice = FeeInvoices::create([
                'branch_id' => $request->branch_id,
                'student_id' => $request->student ?? null,
                'create_date' => $sellDate,
                'due_date' => $sellDate,
                'payment_status' => $payment_status,
                'payment_method' => $request->payment_method,
                'total_amount' => $request->subTotalValue ?? 0,
                'paid_amount' => $request->totalPaid ?? 0,
                'due_amount' => $request->totalDue ?? 0,
                'main_invoice_number' => $invoice_number,
                'create_user_id' => Auth::id(),
                'bank_paid_amount' => $request->payment_method === 'Bank Transfer'
                    ? $request->bank_payment_amount
                    : null,
                'bank_paid_date' => $request->payment_method === 'Bank Transfer' && $bankPaymentDate
                    ? $bankPaymentDate
                    : null,
                'notes' => $request->description,
                'invoice_type' => 'item_sell',
            ]);

            InvoicesPayments::create([
                'invoice_id' => $fee_invoice->id,
                'amount' => $request->subTotalValue ?? 0,
                'payment_method' => $request->payment_method,
                'payment_type' => 'item_sell',
                'payment_file' =>  $request->payment_method === 'Bank Transfer' ? $bankPaymentDocumentPath : null,
                'receipt_number' => $invoice_number,
                'create_user_id' => Auth::id(),
                'bank_payment_amount' => $request->payment_method === 'Bank Transfer' ? $request->bank_payment_amount : null,
                'bank_payment_date' => $bankPaymentDate,
                'branch_id' => $request->branch_id,
                'notes' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            // Create ItemSellItem records for each item in the batch
            foreach ($request->item_id as $index => $itemId) {
                $availableQuantity = ItemInventory::getCurrentBalance($itemId, $request->branch_id);
                if ($request->quantity[$index] > $availableQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity for item cannot exceed available quantity (' . $availableQuantity . ')',
                    ], 422);
                }


                ItemSellItem::create([
                    'item_sell_batch_id' => $itemSellBatch->id,
                    'item_id' => $itemId,
                    'unit_price' => $request->unit_price[$index],
                    'quantity' => $request->quantity[$index],
                    'sub_total' => $request->totalValue[$index],
                ]);

                // Track inventory changes for this item sell (decrease inventory)
                $currentBalance = ItemInventory::getCurrentBalance($itemId, $request->branch_id);;


                $newBalance = $currentBalance - $request->quantity[$index];


                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'sell',
                    'branch_id' => $request->branch_id,
                    'transaction_id' => $itemSellBatch->id,
                    'quantity' => $request->quantity[$index],
                    'current_balance' => $newBalance,
                    'reference_no' => $request->reference_no,
                    'transaction_date' => $sellDate,
                    'description' => $request->description ?? 'Item sold to customer',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item sold successfully!',
                'id' => $itemSellBatch->id
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save item sell.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function itemSellList(Request $request)
    {
        // Performance fix (Section 38): same issue as Item Issue List -- this
        // pulled every sell batch ever created on every page view. Added a
        // date-range filter on sell_date, defaulting to the last month, so a
        // normal page load only pulls a bounded window of rows. Older data is
        // still reachable by widening the range in the filter form above the
        // table.
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $fromDateInput = $request->filled('from_date') ? $request->from_date : now()->subMonth()->format('Y-m-d');
        $toDateInput = $request->filled('to_date') ? $request->to_date : now()->format('Y-m-d');

        $fromDate = Carbon::parse($fromDateInput)->startOfDay();
        $toDate = Carbon::parse($toDateInput)->endOfDay();

        $allItemSellLists = ItemSellBatch::with(['role', 'student', 'user', 'student.studentCategory', 'createdBy'])
            ->whereBetween('sell_date', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.account.inventory.item-sell-list', compact('allItemSellLists', 'fromDateInput', 'toDateInput'));
    }

    public function viewItemSell($id)
    {
        $itemSell = ItemSellBatch::with(['role', 'student', 'user', 'items.item'])->findOrFail($id);

        $buyerName = 'N/A';
        if ($itemSell->role_id == 2 && $itemSell->student) {
            $buyerName = $itemSell->student->student_name;
        } elseif ($itemSell->role_id == 3 && $itemSell->user) {
            $buyerName = $itemSell->user->name;
        } elseif ($itemSell->others_name) {
            $buyerName = $itemSell->others_name;
        }

        return response()->json([
            'itemSell' => [
                'id' => $itemSell->id,
                'reference_no' => $itemSell->reference_no,
                'buyer_name' => $buyerName,
                'items' => $itemSell->items, // Return all items in the batch
                'sell_date' => $itemSell->sell_date,
                'total_quantity' => $itemSell->total_quantity,
                'sell_price' => $itemSell->total_sell_price,
                'total_paid' => $itemSell->total_paid,
                'total_due' => $itemSell->total_due,
                'course' => null, // Course data not available in current structure
                'intake' => null, // Intake data not available in current structure
                'description' => $itemSell->description,
                'status' => $itemSell->status,
                'payment_method' => $itemSell->payment_method,
                'invoice_number' => $itemSell->invoice_number,
                'bank_payment_amount' => $itemSell->bank_payment_amount,
                'bank_payment_date' => $itemSell->bank_payment_date,
                'bank_payment_document' => $itemSell->bank_payment_document,
            ]
        ]);
    }

    public function itemSellSaveInvoiceNumber(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_sell_batches,id',
            'invoice_number' => 'required|string|max:50',
        ]);

        $ItemReceive = ItemSellBatch::findOrFail($request->id);
        $ItemReceive->invoice_number = $request->invoice_number;
        $ItemReceive->save();
    }



    public function returnItemSell(Request $request, $id)
    {
        $itemSellBatch = ItemSellBatch::with('items')->findOrFail($id);

        // Guard against returning an already-returned batch a second time —
        // the dropdown link already hides once status is 'R', but this route
        // is reachable directly by URL too, so it needs its own check.
        if ($itemSellBatch->status === 'R') {
            return redirect()
                ->back()
                ->with('error', 'This item batch has already been returned.');
        }

        // Returns are never blocked any more — the refund method is decided
        // by returnPaymentMethod() (bank out vs cash out) instead.

        try {
            $returnDate = Carbon::today()->format('Y-m-d');
            $returnReason = $request->return_reason ?? 'Item returned from sell (Batch ID: ' . $itemSellBatch->id . ')';

            // "if do any return in item sell that want do recorded as a cash
            // negative transaction" — logged once per batch return, before
            // the batch/items are zeroed out below.
            $this->recordCashNegativeReturn($itemSellBatch, $returnReason);

            foreach ($itemSellBatch->items as $item) {
                // BUG FIX: originally captured $availableQuantity up front but
                // then read $item->quantity (already zeroed by the update()
                // just below it) — and via a typo ($item->quantityy) — when
                // decrementing the batch totals, so returned quantity/value
                // never actually made it back out of the batch's own totals.
                // Captured once, up front, and used consistently below.
                $returnedQuantity = (float) $item->quantity;

                if ($returnedQuantity > 0) {
                    $currentBalance = ItemInventory::getCurrentBalance($item->item_id, $itemSellBatch->branch_id);

                    ItemInventory::create([
                        'item_id' => $item->item_id,
                        'transaction_type' => 'sell_return',
                        'transaction_id' => $itemSellBatch->id,
                        'branch_id' => $itemSellBatch->branch_id,
                        'quantity' => $returnedQuantity,
                        'current_balance' => $currentBalance + $returnedQuantity,
                        'reference_no' => $itemSellBatch->reference_no,
                        'transaction_date' => $returnDate,
                        'description' => $returnReason,
                    ]);
                }

                $item->update([
                    'quantity' => 0,
                    'sub_total' => 0,
                ]);
            }

            // Whole-batch return (there's no partial-item return flow in this
            // form) — zero everything out, same end state directReturnItemSell()
            // already puts the batch into.
            $itemSellBatch->update([
                'total_quantity' => 0,
                'total_sell_price' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'status' => 'R',
            ]);

            return redirect()
                ->back()
                ->with('success', 'Item return processed successfully!');
        } catch (\Throwable $e) {

            return redirect()
                ->back()
                ->with('error', 'Failed to process item return.');
        }
    }

    public function storeItemSellPayment(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|string',
        ]);

        try {
            $itemSellBatch = ItemSellBatch::findOrFail($id);

            $paymentDate = Carbon::createFromFormat('m/d/Y', $request->payment_date)->format('Y-m-d');

            $newTotalPaid = $itemSellBatch->total_paid + $request->amount;
            $newTotalDue = $itemSellBatch->total_due - $request->amount;

            if ($newTotalDue < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining due.',
                ], 422);
            }

            $itemSellBatch->update([
                'total_paid' => $newTotalPaid,
                'total_due' => $newTotalDue,
                'status' => ($newTotalDue == 0) ? 'P' : 'PP',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment added successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateItemSellStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:P,PP,U,R',
        ]);

        try {
            $itemSellBatch = ItemSellBatch::findOrFail($id);

            $itemSellBatch->update([
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function directReturnItemSell(Request $request, $id)
    {
        try {
            $itemSellBatch = ItemSellBatch::with('items')->findOrFail($id);

            // Guard against returning an already-returned batch a second time.
            if ($itemSellBatch->status === 'R') {
                return response()->json([
                    'success' => false,
                    'message' => 'This item batch has already been returned.',
                ], 422);
            }

            // This is the endpoint the Return button actually calls. Returns
            // are never blocked — see returnPaymentMethod() for the rule that
            // decides bank out vs cash out.
            $refundMethod = $this->returnPaymentMethod($itemSellBatch);

            // "if do any return in item sell that want do recorded as a cash
            // negative transaction" — this endpoint previously never touched
            // FeeInvoices/InvoicesPayments at all, so a return made through
            // the actual UI button left no financial record behind — only
            // returnItemSell() (unreachable from the UI, see note above) did.
            // Logged before the batch/items are zeroed out below.
            $this->recordCashNegativeReturn(
                $itemSellBatch,
                'Full item return from sell (Batch ID: ' . $itemSellBatch->id . ')'
            );

            foreach ($itemSellBatch->items as $itemSellItem) {
                // Update inventory for each item in the batch
                $currentBalance = ItemInventory::getCurrentBalance($itemSellItem->item_id, $itemSellBatch->branch_id);
                $newBalance = $currentBalance + $itemSellItem->quantity;

                ItemInventory::create([
                    'item_id' => $itemSellItem->item_id,
                    'transaction_type' => 'sell_return',
                    'transaction_id' => $itemSellBatch->id,
                    'branch_id' => $itemSellBatch->branch_id,
                    'quantity' => $itemSellItem->quantity,
                    'current_balance' => $newBalance,
                    'reference_no' => $itemSellBatch->reference_no,
                    'transaction_date' => now(),
                    'description' => 'Full item return from sell (Batch ID: ' . $itemSellBatch->id . ')',
                ]);
            }

            // Update sell batch record to reflect full return
            $itemSellBatch->update([
                'total_quantity' => 0,
                'total_sell_price' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'status' => 'R',
            ]);

            // Delete all associated item sell items
            $itemSellBatch->items()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item batch returned successfully! Recorded as '
                    . ($refundMethod === 'Bank Transfer' ? 'bank out' : 'cash out') . ' in the collection report.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to return item batch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function itemSellBill($id)
    {
        // Load batch with related items
        $itemSellBatch = ItemSellBatch::with([
            'student',
            'role',
            'createdBy',
            'itemSellItems.item', // Load items for the batch
        ])->findOrFail($id);

        return view('pages.account.inventory.item-sell-invoice', compact('itemSellBatch'));
    }

    // =====================================================================
    // Return helpers (shared by returnItemSell() and directReturnItemSell())
    // =====================================================================

    /**
     * How an item sell return is paid back (never blocks the return):
     *  - Bank Transfer sale AND bank deposit month == current month
     *      -> "Bank Transfer" (bank out)
     *  - anything else (other month, no deposit date, cash, invoice, ...)
     *      -> "Cash" (cash out)
     */
    private function returnPaymentMethod(ItemSellBatch $itemSellBatch): string
    {
        if (
            $itemSellBatch->payment_method === 'Bank Transfer'
            && $itemSellBatch->bank_payment_date
            && Carbon::parse($itemSellBatch->bank_payment_date)->isSameMonth(Carbon::now())
        ) {
            return 'Bank Transfer';
        }

        return 'Cash';
    }

    /**
     * "if do any return in item sell that want do recorded as a cash
     * negative transaction" — every item sell return, however it's
     * triggered, writes a negative InvoicesPayments row against the sale's
     * own fee invoice: the same ledger saveItemSellData() already writes
     * the original payment to (payment_type 'item_sell'), and the one
     * everywhere else in this app reads back for payment history/reports.
     * The negative row's payment_method follows returnPaymentMethod():
     * "Bank Transfer" (bank out) when the sale was a bank transfer deposited
     * in the current month, otherwise "Cash" (cash out). Either way it shows
     * as a "Return" entry in the Collection Report.
     *
     * NOTE: no separate "cash book"/cash-transactions table was shared with
     * me for this module. If one already exists elsewhere in this app that
     * item sell returns are meant to post to as well, point me at its model
     * and columns and I'll wire this up to write there too.
     */
    private function recordCashNegativeReturn(ItemSellBatch $itemSellBatch, string $note): void
    {
        $feeInvoice = $itemSellBatch->invoice_number
            ? (FeeInvoices::where('main_invoice_number', $itemSellBatch->invoice_number)
                ->where('invoice_type', 'item_sell')
                ->where('branch_id', $itemSellBatch->branch_id)
                ->first()
                ?? FeeInvoices::where('main_invoice_number', $itemSellBatch->invoice_number)->first())
            : null;

        // Older sales were saved without a fee invoice, so their return never
        // reached the collection report. Create the missing invoice from the
        // batch (before it is zeroed out) so the return is still recorded.
        if (!$feeInvoice) {
            $paid = (float) $itemSellBatch->total_paid;
            if ($paid <= 0) {
                return; // nothing was paid — nothing to hand back
            }

            try {
                $feeInvoice = FeeInvoices::create([
                    'branch_id' => $itemSellBatch->branch_id,
                    'student_id' => $itemSellBatch->student_id,
                    'create_date' => $itemSellBatch->sell_date ?? now()->toDateString(),
                    'due_date' => $itemSellBatch->sell_date ?? now()->toDateString(),
                    'payment_status' => 'full',
                    'payment_method' => $itemSellBatch->payment_method,
                    'total_amount' => (float) $itemSellBatch->total_sell_price,
                    'paid_amount' => $paid,
                    'due_amount' => (float) $itemSellBatch->total_due,
                    'main_invoice_number' => $itemSellBatch->invoice_number ?: ('INV-S-RET-' . $itemSellBatch->id),
                    'create_user_id' => Auth::id(),
                    'notes' => 'Created on return for item sell batch #' . $itemSellBatch->id,
                    'invoice_type' => 'item_sell',
                ]);
            } catch (\Throwable $e) {
                // Never block the return itself (e.g. a non-student sale with no student_id).
                \Illuminate\Support\Facades\Log::warning('Item sell return #' . $itemSellBatch->id . ' not recorded in collection: ' . $e->getMessage());
                return;
            }
        }

        $originalPayment = InvoicesPayments::where('invoice_id', $feeInvoice->id)
            ->where('payment_type', 'item_sell')
            ->orderByDesc('id')
            ->first();

        $returnAmount = (float) ($originalPayment->amount ?? $feeInvoice->paid_amount ?? 0);
        if ($returnAmount <= 0) {
            $returnAmount = (float) $itemSellBatch->total_paid;
        }

        if ($returnAmount <= 0) {
            return; // nothing was actually paid — nothing to hand back as cash
        }

        $feeInvoice->paid_amount = max(0, (float) $feeInvoice->paid_amount - $returnAmount);
        $feeInvoice->due_amount = (float) $feeInvoice->due_amount + $returnAmount;
        $feeInvoice->payment_status = 'returned';
        $feeInvoice->save();

        // Bank out (same deposit month) or cash out (everything else).
        $method = $this->returnPaymentMethod($itemSellBatch);
        $isBankOut = $method === 'Bank Transfer';

        InvoicesPayments::create([
            'invoice_id' => $feeInvoice->id,
            'amount' => -$returnAmount,
            'payment_method' => $method,
            'bank_payment_amount' => $isBankOut ? -$returnAmount : null,
            'bank_payment_date' => $isBankOut ? now()->toDateString() : null,
            'payment_type' => 'item_sell_return',
            'receipt_number' => 'RET-' . time() . '-' . uniqid(),
            'create_user_id' => Auth::id(),
            'branch_id' => $itemSellBatch->branch_id,
            'notes' => $note . ($isBankOut ? ' [Bank out]' : ' [Cash out]'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
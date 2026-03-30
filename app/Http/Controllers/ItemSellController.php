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
                $availableQuantity = ItemInventory::getCurrentBalance($itemId);
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
                $currentBalance = ItemInventory::getCurrentBalance($itemId);
                $newBalance = $currentBalance - $request->quantity[$index];

                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'sell',
                    'transaction_id' => $itemSellBatch->id, // Link to ItemSellBatch
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


    public function itemSellList()
    {
        $allItemSellLists = ItemSellBatch::with(['role', 'student', 'user', 'student.studentCategory', 'createdBy'])->orderBy('created_at', 'desc')->get();
        return view('pages.account.inventory.item-sell-list', compact('allItemSellLists'));
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

   

    public function returnItemSell($id)
    {


        try {
            $itemSellItem = ItemSellItem::where('item_sell_batch_id', $id)->get();
            $itemSellBatch = ItemSellBatch::findOrFail($id);
            $returnDate = Carbon::today()->format('Y-m-d');


            foreach ($itemSellItem as $item) {


                $availableQuantity = $item->quantity;

                $currentBalance = ItemInventory::getCurrentBalance($item->item_id);
                $newBalance = $currentBalance + $availableQuantity;
                ItemInventory::create([
                    'item_id' => $item->item_id,
                    'transaction_type' => 'sell_return',
                    'transaction_id' => $itemSellBatch->id, // Link to ItemSellBatch
                    'quantity' => $item->quantity,
                    'current_balance' => $newBalance,
                    'reference_no' => $itemSellBatch->reference_no,
                    'transaction_date' => $returnDate,
                    'description' => $request->return_reason ?? 'Item returned from sell',
                ]);


                $newQuantity = $item->quantity - $item->quantity;
                $item->update([
                    'quantity' => $newQuantity,
                    'sub_total' => $newQuantity * $item->unit_price,
                ]);


                // Update the ItemSellBatch totals
                $itemSellBatch->total_quantity -= $item->quantityy;
                $itemSellBatch->total_sell_price -= ($item->quantity * $item->unit_price);
                $itemSellBatch->total_due += ($item->quantity * $item->unit_price);
                $itemSellBatch->status = 'R';
                $itemSellBatch->save();
            }

            return redirect()
                ->back()
                ->with('success', 'Item return processed successfully!',);
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

            foreach ($itemSellBatch->items as $itemSellItem) {
                // Update inventory for each item in the batch
                $currentBalance = ItemInventory::getCurrentBalance($itemSellItem->item_id);
                $newBalance = $currentBalance + $itemSellItem->quantity;

                ItemInventory::create([
                    'item_id' => $itemSellItem->item_id,
                    'transaction_type' => 'sell_return',
                    'transaction_id' => $itemSellBatch->id,
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
                'message' => 'Item batch returned successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to return item batch.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getItemPrice($id)
    {
        try {
            $item = InventoryItem::findOrFail($id);
            $currentBalance = ItemInventory::getCurrentBalance($id);
            return response()->json([
                'success' => true,
                'price' => $item->item_price,
                'available_quantity' => $currentBalance
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], 404);
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
}

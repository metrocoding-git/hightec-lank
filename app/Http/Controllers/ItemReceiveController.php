<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\ItemInventory;
use App\Models\ItemReceive;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemReceiveController extends Controller
{
    public function itemReceive()
    {
        $items = InventoryItem::with('category')->get();
        $suppliers = Supplier::all();
        $branches = Auth::user()->branches;

        return view('pages.account.inventory.item-receive', compact(
            'items',
            'suppliers',
            'branches'
        ));
    }

    public function itemReceivePost(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'supplier_id' => 'required|integer',
            'store_id' => 'required|integer',
            'receive_date' => 'required|string',
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'invoice_number' => 'nullable|string|max:255',
            'bank_payment_amount' => 'nullable|numeric',
            'bank_payment_date' => 'nullable|string',
            'bank_payment_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->payment_method === 'Bank Transfer') {
            $request->validate([
                'bank_payment_amount' => 'required|numeric|min:0.01',
                'bank_payment_date' => 'required|date',
            ]);

            $subTotal = array_sum(array_map(function ($price, $qty) {
                return (float)$price * (float)$qty;
            }, $request->unit_price, $request->quantity));

            if (($request->bank_payment_amount ?? 0) < $subTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank Payment Amount must be greater than the Item Receive Total!',
                ], 422);
            }
        }

        try {
            $receiveDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->receive_date)->format('Y-m-d');
            $transactionId = 'TRX-' . time() . '-' . rand(1000, 9999);

            $bankPaymentDocumentPath = null;
            if ($request->hasFile('bank_payment_document')) {
                $bankPaymentDocumentPath = $request->file('bank_payment_document')->store('bank_documents', 'public');
            }

            foreach ($request->item_id as $index => $itemId) {
                $lineTotal = (float) $request->unit_price[$index] * (float) $request->quantity[$index];

                $totalPaidAmount = 0;
                if ($request->payment_method === 'Bank Transfer') {
                    $totalPaidAmount = $request->bank_payment_amount ?? 0;
                } else {
                    $totalPaidAmount = $request->totalPaid ?? 0;
                }

                $bankPaymentDate = null;
                if ($request->payment_method === 'Bank Transfer' && $request->bank_payment_date) {
                    $bankPaymentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->bank_payment_date)->format('Y-m-d');
                }

                $itemReceive = ItemReceive::create([
                    'transaction_id' => $transactionId,
                    'payment_method' => $request->payment_method,
                    'bank_payment_amount' => $request->payment_method === 'Bank Transfer' ? $request->bank_payment_amount : null,
                    'bank_payment_date' => $bankPaymentDate,
                    'bank_payment_document' => $request->payment_method === 'Bank Transfer' ? $bankPaymentDocumentPath : null,
                    'supplier_id' => $request->supplier_id,
                    'store_id' => $request->store_id,
                    'reference_no' => $request->reference_no,
                    'receive_date' => $receiveDate,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'unit_price' => $request->unit_price[$index],
                    'quantity' => $request->quantity[$index],
                    'sub_total' => $lineTotal,
                    'total_paid' => $totalPaidAmount,
                    'created_by' => Auth::id(),
                ]);

                $currentBalance = ItemInventory::getCurrentBalance($itemId);
                $newBalance = $currentBalance + $request->quantity[$index];

                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'receive',
                    'transaction_id' => $itemReceive->id,
                    'quantity' => $request->quantity[$index],
                    'current_balance' => $newBalance,
                    'reference_no' => $request->reference_no,
                    'transaction_date' => $receiveDate,
                    'description' => $request->description ?? 'Item received from supplier',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item received successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save item receive.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function itemReceivesaveInvoiceNumber(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_receive,id',
            'invoice_number' => 'required|string|max:50',
        ]);

        $itemReceive = ItemReceive::findOrFail($request->id);
        $itemReceive->invoice_number = $request->invoice_number;
        $itemReceive->save();
    }

    public function itemReceiveList()
    {
        $itemReceives = ItemReceive::with(['supplier', 'branch', 'item', 'item.category'])
            ->distinct()
            ->get();

        $items = InventoryItem::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        return view('pages.account.inventory.item-receive-list', compact('itemReceives', 'items', 'suppliers', 'branches'));
    }

    public function viewItemReceive($transactionId)
    {
        try {
            $transaction = ItemReceive::where('transaction_id', $transactionId)->with(['supplier', 'branch', 'item'])->first();
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $items = ItemReceive::where('transaction_id', $transactionId)->with(['item', 'supplier', 'branch'])->get();

            $totalQuantity = $items->sum('quantity');
            $totalSubTotal = $items->sum('sub_total');
            $totalPaid = $transaction->total_paid;
            $totalBalance = $totalSubTotal - $totalPaid;

            return response()->json([
                'transaction' => $transaction,
                'items' => $items,
                'totals' => [
                    'total_quantity' => $totalQuantity,
                    'total_sub_total' => $totalSubTotal,
                    'total_paid' => $totalPaid,
                    'total_balance' => $totalBalance,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch transaction details: ' . $e->getMessage()], 500);
        }
    }

    public function deleteItemReceive($transactionId)
    {
        try {
            $itemReceives = ItemReceive::where('transaction_id', $transactionId)->get();
            foreach ($itemReceives as $itemReceive) {
                $itemReceive->delete();
            }
            return response()->json([
                'status' => true,
                'message' => 'Item receive deleted successfully!',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete item receive: ' . $e->getMessage());
        }
    }
}

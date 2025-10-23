<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Intake;
use App\Models\Inventory_item;
use App\Models\ItemSell;
use App\Models\ItemInventory;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ItemSellController extends Controller
{
    public function itemSell()
    {
        $items = Inventory_item::all();
        $classes = Course::all();
        $sections = Intake::all();
        $roles = Role::all();
        $students = Student::all();
        $users = User::all();
        return view('pages.account.inventory.item-sell', compact('items', 'classes', 'sections', 'roles', 'students', 'users'));
    }

    public function saveItemSellData(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'payment_method' => 'required|string',
            // 'role_id' => 'required|integer|exists:roles,id',
            'sell_date' => 'required|string', // will parse m/d/Y -> Y-m-d
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer|exists:inventory_items,id',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'totalPaid' => 'nullable|numeric',
            'subTotal' => 'nullable|numeric',
            'totalDue' => 'nullable|numeric',
        ]);

        try {
            // Parse date from the UI (m/d/Y) to database format (Y-m-d)
            $sellDate = Carbon::createFromFormat('m/d/Y', $request->sell_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            // Fallback: try to parse a standard Y-m-d or Y/m/d if user typed differently
            try {
                $sellDate = Carbon::parse($request->sell_date)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid sell date format.',
                ], 422);
            }
        }

        try {
            // Create one row per item, duplicating header fields as per existing schema
            $createdSells = [];
            foreach ($request->item_id as $index => $itemId) {
                // Check available quantity
                $availableQuantity = ItemInventory::getCurrentBalance($itemId);
                if ($request->quantity[$index] > $availableQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity for item cannot exceed available quantity (' . $availableQuantity . ')',
                    ], 422);
                }

                $itemSell = ItemSell::create([
                    'payment_method' => $request->payment_method,
                    'role_id' => $request->role_id,
                    'course_id' => $request->class ?? null, // Map 'class' to 'course_id'
                    'intake_id' => $request->section ?? null, // Map 'section' to 'intake_id'
                    'student_id' => $request->student ?? null, // Map 'student' to 'student_id'
                    'user_id' => $request->staff_id ?? null, // Map 'staff_id' to 'user_id'
                    'reference_no' => $request->reference_no,
                    'sell_date' => $sellDate,
                    'others_name' => $request->customer_name ?? null,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'total_quantity' => $request->quantity[$index],
                    'sell_price' => $request->unit_price[$index],
                    'total_paid' => $request->totalPaid ?? 0,
                    'total_due' => $request->totalDue ?? 0,
                ]);

                $createdSells[] = $itemSell;

                // Track inventory changes for this item sell (decrease inventory)
                $currentBalance = ItemInventory::getCurrentBalance($itemId);
                $newBalance = $currentBalance - $request->quantity[$index];

                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'sell',
                    'transaction_id' => $itemSell->id,
                    'quantity' => $request->quantity[$index],
                    'current_balance' => $newBalance,
                    'reference_no' => $request->reference_no,
                    'transaction_date' => $sellDate,
                    'description' => $request->description ?? 'Item sold to customer',
                ]);
            }

            return response()->json([
                'success' => 'success',
                'message' => 'Item sold successfully!'
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
        $allItemSellLists = ItemSell::with(['role', 'student', 'user'])->get();
        return view('pages.account.inventory.item-sell-list', compact('allItemSellLists'));
    }

    public function viewItemSell($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-view-modal', compact('itemSell'));
    }

    public function editItemSell($id)
    {
        $itemSell = ItemSell::with(['role', 'student', 'user', 'item'])->findOrFail($id);
        $items = Inventory_item::all();
        $classes = Course::all();
        $sections = Intake::all();
        $roles = Role::all();
        $students = Student::all();
        $users = User::all();
        return view('pages.account.inventory.item-sell-modal.item-sell-edit-modal', compact('itemSell', 'items', 'classes', 'sections', 'roles', 'students', 'users'));
    }

    public function deleteItemSellView($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-delete-modal', compact('itemSell'));
    }

    public function updateItemSell(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'role_id' => 'required|integer|exists:roles,id',
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
        ]);

        try {
            $sellDate = Carbon::createFromFormat('m/d/Y', $request->sell_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            try {
                $sellDate = Carbon::parse($request->sell_date)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid sell date format.',
                ], 422);
            }
        }

        try {
            $itemSell = ItemSell::findOrFail($id);
            $itemSell->update([
                'payment_method' => $request->payment_method,
                'role_id' => $request->role_id,
                'course_id' => $request->class ?? null,
                'intake_id' => $request->section ?? null,
                'student_id' => $request->student ?? null,
                'user_id' => $request->staff_id ?? null,
                'reference_no' => $request->reference_no,
                'sell_date' => $sellDate,
                'description' => $request->description,
                'item_id' => $request->item_id[0], // Assuming single item update for simplicity
                'total_quantity' => $request->quantity[0],
                'sell_price' => $request->unit_price[0],
                'total_paid' => $request->totalPaid ?? 0,
                'total_due' => $request->totalDue ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item sell updated successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item sell.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addPaymentSell($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-add-payment-modal', compact('itemSell'));
    }

    public function viewSellPayments($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-view-payments-modal', compact('itemSell'));
    }

    public function deleteItemSell($id)
    {
        try {
            $itemSell = ItemSell::findOrFail($id);
            $itemSell->delete();
            return redirect()
                ->back()
                ->with('success', 'Item sell deleted successfully!');
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item sell.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelItemSell($id)
    {
        try {
            $itemSell = ItemSell::findOrFail($id);
            // Logic to cancel item sell (e.g., update status, refund)
            // For now, just update total_due to grand_total and total_paid to 0
            $itemSell->update([
                'total_paid' => 0,
                'total_due' => $itemSell->sell_price * $itemSell->total_quantity, // Assuming this is the grand total
            ]);
            return redirect()
                ->back()
                ->with('success', 'Item sell cancelled successfully!');

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel item sell.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelItemSellView($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-cancel-modal', compact('itemSell'));
    }

    public function returnItemSellView($id)
    {
        $itemSell = ItemSell::findOrFail($id);
        return view('pages.account.inventory.item-sell-modal.item-sell-return-modal', compact('itemSell'));
    }

    public function returnItemSell(Request $request, $id)
    {
        $request->validate([
            'return_quantity' => 'required|integer|min:1',
            'return_date' => 'required|string',
            'return_reason' => 'nullable|string',
        ]);

        try {
            $itemSell = ItemSell::findOrFail($id);

            // Validate return quantity doesn't exceed sold quantity
            if ($request->return_quantity > $itemSell->total_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Return quantity cannot exceed sold quantity (' . $itemSell->total_quantity . ')',
                ], 422);
            }

            $returnDate = Carbon::createFromFormat('m/d/Y', $request->return_date)->format('Y-m-d');

            // Track inventory changes for this item return (increase inventory)
            $currentBalance = ItemInventory::getCurrentBalance($itemSell->item_id);
            $newBalance = $currentBalance + $request->return_quantity;

            ItemInventory::create([
                'item_id' => $itemSell->item_id,
                'transaction_type' => 'sell_return',
                'transaction_id' => $itemSell->id,
                'quantity' => $request->return_quantity,
                'current_balance' => $newBalance,
                'reference_no' => $itemSell->reference_no,
                'transaction_date' => $returnDate,
                'description' => $request->return_reason ?? 'Item returned from sell',
            ]);

            // Update the sold item record to reflect the return
            $newQuantity = $itemSell->total_quantity - $request->return_quantity;
            $newTotalDue = $itemSell->total_due + ($itemSell->sell_price * $request->return_quantity);

            $itemSell->update([
                'total_quantity' => $newQuantity,
                'total_due' => $newTotalDue,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item return processed successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process item return.',
                'error' => $e->getMessage(),
            ], 500);
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
            $itemSell = ItemSell::findOrFail($id);

            $paymentDate = Carbon::createFromFormat('m/d/Y', $request->payment_date)->format('Y-m-d');

            // Update total_paid and total_due
            $newTotalPaid = $itemSell->total_paid + $request->amount;
            $newTotalDue = $itemSell->total_due - $request->amount;

            if ($newTotalDue < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds remaining due.',
                ], 422);
            }

            $itemSell->update([
                'total_paid' => $newTotalPaid,
                'total_due' => $newTotalDue,
            ]);

            // Optionally, save payment details to a separate payments table if one exists
            // For now, we're just updating the ItemSell record.
            // If a dedicated payments table is needed, it would look something like:
            // $itemSell->payments()->create([
            //     'amount' => $request->amount,
            //     'payment_method' => $request->payment_method,
            //     'payment_date' => $paymentDate,
            // ]);

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
            $itemSell = ItemSell::findOrFail($id);

            $itemSell->update([
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
            $itemSell = ItemSell::findOrFail($id);

            // Update inventory for full return
            $currentBalance = ItemInventory::getCurrentBalance($itemSell->item_id);
            $newBalance = $currentBalance + $itemSell->total_quantity;

            ItemInventory::create([
                'item_id' => $itemSell->item_id,
                'transaction_type' => 'sell_return',
                'transaction_id' => $itemSell->id,
                'quantity' => $itemSell->total_quantity,
                'current_balance' => $newBalance,
                'reference_no' => $itemSell->reference_no,
                'transaction_date' => now(),
                'description' => 'Full item return from sell',
            ]);

            // Update sell record to reflect return
            $itemSell->update([
                'total_quantity' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'status' => 'R',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item returned successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to return item.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getItemPrice($id)
    {
        try {
            $item = Inventory_item::findOrFail($id);
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
}

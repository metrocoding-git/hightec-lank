<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\ItemInventory;
use App\Http\Controllers\Controller;
use App\Models\Branch;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itemCategories = Category::all();
        $items = InventoryItem::with('category')->get();
        $branches = Branch::all();
        foreach ($items as $item) {
            $balances = [];
            foreach ($branches as $branch) {
                $balances[$branch->name] = ItemInventory::getCurrentBalance(
                    $item->id,
                    $branch->id
                );
            }
            $item->current_balance = $balances;
        }
        return view('pages.account.inventory.item-list', compact('itemCategories', 'items'));
    }

    public function view($id)
    {
        $item = InventoryItem::with('category')->findOrFail($id);
        $inventoryHistory = ItemInventory::getInventoryHistory($id);
        return view('pages.account.inventory.item-view', compact('item', 'inventoryHistory'));
    }

    public function getItemHistory($id)
    {
        $inventoryHistory = ItemInventory::getInventoryHistory($id);
        // Ensure item relationships are loaded
        $inventoryHistory->load('item.category');
        return response()->json($inventoryHistory);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_name' => 'required|exists:categories,id',
            'item_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        InventoryItem::create([
            'item_name' => $request->item_name,
            'item_category_id' => $request->category_name,
            'item_price' => $request->item_price,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Item added successfully!');
    }


    public function edit(InventoryItem $inventory_item, $id)
    {
        //
        $editData = InventoryItem::findOrFail($id);
        $items = InventoryItem::with('category')->get();
        $branches = Branch::all();
        foreach ($items as $item) {
            $balances = [];
            foreach ($branches as $branch) {
                $balances[$branch->name] = ItemInventory::getCurrentBalance(
                    $item->id,
                    $branch->id
                );
            }
            $item->current_balance = $balances;
        }
        $itemCategories = Category::all();
        return view('pages.account.inventory.item-list', compact('editData', 'items', 'itemCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventory_item, $id)
    {
        //
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_name' => 'required|exists:categories,id',
            'item_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($id);
        $item->update([
            'item_name' => $request->item_name,
            'item_category_id' => $request->category_name,
            'item_price' => $request->item_price,
            'description' => $request->description,
        ]);

        return redirect()->route('item-list')->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventory_item, $id)
    {
        //
        $item = InventoryItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item deleted successfully!');
    }

    public function getItemPrice($id, $branchId)
    {
        try {
            $item = InventoryItem::findOrFail($id);
            
            $currentBalance = ItemInventory::getCurrentBalance($id, $branchId);
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

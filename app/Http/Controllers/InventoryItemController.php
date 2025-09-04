<?php

namespace App\Http\Controllers;

use App\Models\Inventory_item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\category;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $itemCategories=category::all();
         $items=Inventory_item::all();
        return view('pages.account.inventory.item-list' , compact('itemCategories','items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'description' => 'nullable|string',
        ]);

        Inventory_item::create([
            'item_name' => $request->item_name,
            'item_category_id' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Item added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory_item $inventory_item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory_item $inventory_item, $id)
    {
        //
        $editData = Inventory_item::findOrFail($id);
        $items = Inventory_item::with('category')->get();
        $itemCategories = category::all();
        return view('pages.account.inventory.item-list', compact('editData', 'items', 'itemCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory_item $inventory_item, $id)
    {
        //
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_name' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $item = Inventory_item::findOrFail($id);
        $item->update([
            'item_name' => $request->item_name,
            'item_category_id' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory_item $inventory_item, $id)
    {
        //
        $item = Inventory_item::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item deleted successfully!');
    }
}

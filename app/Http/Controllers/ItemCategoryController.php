<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */ public function index()
    {
        $categories = Category::all(); // fetch all categories
        return view('pages.account.inventory.item-category', compact('categories'));
    }

    // Store a new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

       return redirect()->back()->with('success', 'Category created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $editData = Category::findOrFail($id);
        $categories = Category::all(); // still pass all categories for the table
        return view('pages.account.inventory.item-category', compact('categories', 'editData'));
    }

    // Update existing category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}

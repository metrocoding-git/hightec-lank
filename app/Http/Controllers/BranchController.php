<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\InventoryItem;

class BranchController extends Controller
{
    //
    // post course
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        $branch = Branch::create($validated);
    
        return response()->json([
            "message" => "Course created successfully.",
            "course" => $branch
        ], 201);
    }

    // get course
    public function index()
    {
        $branch = Branch::all();

        return response()->json([
            "courses" => $branch
        ], 200);
    }

    // update course
    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);
    
        $branch->update($validated);
    
        return response()->json([
            'message' => 'Course updated successfully.',
            'course' => $branch
        ]);
    }


    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $branch->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }

    public function getItemsByCategory($categoryId)
    {
        try {
            $items = InventoryItem::where('item_category_id', $categoryId)->get();

            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intake;

class IntakeController extends Controller
{
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|integer|exists:branch_id',
            'student_count' => 'required|numeric',
            
        ]);
    
        $intake = Intake::create($validated);
    
        return response()->json([
            "message" => "Intake created successfully.",
            "intake" => $intake
        ], 201);
    }

    // get course
    public function index()
    {
        $intakes = Intake::with('branch')->get();

        return response()->json([
            "intakes" => $intakes
        ], 200);
    }

    // update course
    public function update(Request $request, $id)
    {
        $intake = Intake::find($id);

        if (!$intake) {
            return response()->json(['message' => 'intakes not found'], 404);
        }
    
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'branch_id' => 'sometimes|required|integer',
            'student_count' => 'sometimes|required|numeric',
        ]);
    
        $intake->update($validated);
    
        return response()->json([
            'message' => 'Intake updated successfully.',
            'intake' => $intake
        ]);
    }


    public function destroy($id)
    {
        $intake  = Intake::find($id);

        if (!$intake) {
            return response()->json(['message' => 'Intake not found'], 404);
        }

        $intake->delete();

        return response()->json(['message' => 'Intake deleted successfully.']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseIntake;

class CourseIntakeController extends Controller
{
    //
    public function store(Request $request) {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'intake_id' => 'required|exists:intakes,id',
        ]);
    
        $courseIntake  = CourseIntake::create($validated);
    
        return response()->json([
            "message" => "Intake created successfully.",
            "intake" => $courseIntake
        ], 201);
    }


    public function index() {

        $$courseIntakes = CourseIntake::with(['course', intake])->get();
    }
}

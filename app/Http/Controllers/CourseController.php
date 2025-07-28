<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    // post course
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
    
        $course = Course::create($validated);
    
        return response()->json([
            "message" => "Course created successfully.",
            "course" => $course
        ], 201);
    }

    // get course
    public function index()
    {
        $courses = Course::all();

        return response()->json([
            "courses" => $courses
        ], 200);
    }

    // update course
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|unique:courses,code,' . $id,
            'price' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
        ]);
    
        $course->update($validated);
    
        return response()->json([
            'message' => 'Course updated successfully.',
            'course' => $course
        ]);
    }


    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully.']);
    }
    
}

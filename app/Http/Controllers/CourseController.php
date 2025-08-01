<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::get();
        $title = 'All Active Courses';

        return view('pages.administration.academic.course', [
            'courses' => $courses
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Course::create($request->all());

        return redirect()->back()->with('message-success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code,' . $id,
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);
        $course->update($request->all());

        return redirect()->route('course.index')->with('message-success', 'Course updated successfully!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $courses = Course::get();
        return view('pages.administration.academic.course', ['course' => $course, 'courses' => $courses, 'courseId' => $id,]);
    }


    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('course.index')->with('message-success-delete', 'Course deleted successfully.');
    }
}

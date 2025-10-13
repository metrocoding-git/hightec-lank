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
            'price' => 'required|numeric',
            'expiration_date' => 'required|numeric',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        $data = $request->all();
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = 'co-' . strtoupper(uniqid()); // Generate a unique code
        }

        Course::create($data);

        return redirect()->back()->with('message-success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
             'expiration_date' => 'required|numeric',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);
        $data = $request->all();
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = 'co-' . strtoupper(uniqid()); // Generate a unique code
        }
        $course->update($data);

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Lesson;
use App\Models\Subject;

class TopicController extends Controller
{
    public function index()
    {
        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();
        $lessons = Lesson::get();

        return view('pages.administration.lesson-plan.topic', [
            'courses' => $courses,
            'subjects' => $subjects,
            'intakes' => $intakes,
            'lessons' => $lessons
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'intake_id' => 'required|exists:intakes,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson' => 'required|array|min:1',
            'lesson.*' => 'required|string|max:255',
        ]);

        $exists = Lesson::where('intake_id', $request->intake_id)
            ->where('course_id', $request->course_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if (!$exists) {
            Lesson::create([
                'course_id' => $request->course_id,
                'intake_id' => $request->intake_id,
                'subject_id' => $request->subject_id,
                'name' => json_encode($request->lesson),
            ]);
            return redirect()->route('lesson.index')->with('message-success', 'Lessons added successfully!');
        } else {
            return redirect()->back()->with('message-danger', 'Subject, Course and Intake already assigned.');
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'intake_id' => 'required|exists:intakes,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson' => 'required|array|min:1',
            'lesson.*' => 'required|string|max:255',
        ]);

        $lesson = Lesson::findOrFail($id);

        $duplicate = Lesson::where('intake_id', $request->intake_id)
            ->where('course_id', $request->course_id)
            ->where('subject_id', $request->subject_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return redirect()->back()->with('message-danger', 'Another record with same Course, Intake, and Subject already exists.');
        }

        $lesson->update([
            'course_id' => $request->course_id,
            'intake_id' => $request->intake_id,
            'subject_id' => $request->subject_id,
            'name' => json_encode($request->lesson),
        ]);

        return redirect()->route('lesson.index')->with('message-success', 'Lesson updated successfully!');
    }


    public function edit($id)
    {

        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();
        $lesson = Lesson::findOrFail($id);
        $lesson->titles = json_decode($lesson->name, true);
        $lessons = Lesson::get();

        return view('pages.administration.lesson-plan.topic', [
            'courses' => $courses,
            'subjects' => $subjects,
            'intakes' => $intakes,
            'lessons' => $lessons,
            'lesson' => $lesson
        ]);
    }


    public function destroy($id)
    {
        Lesson::findOrFail($id)->delete();
        return redirect()->route('lesson.index')->with('message-success-delete', 'lesson deleted successfully.');
    }
}

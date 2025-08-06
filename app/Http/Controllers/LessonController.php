<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Lesson;
use App\Models\Subject;

class LessonController extends Controller
{
    public function index()
    {
        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();

        $lessons = Lesson::get();
        $lessons = $lessons->groupBy(function (Lesson $item) {
            return $item->intake_id . '-' . $item->course_id . '-' . $item->subject_id;
        })->values();

        return view('pages.administration.lesson-plan.lesson', [
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
            foreach ($request->lesson as $lessonTitle) {
                Lesson::create([
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'subject_id' => $request->subject_id,
                    'name' => $lessonTitle,
                ]);
            }

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

        // Fetch the original lesson just to validate it exists
        $lesson = Lesson::findOrFail($id);

        // Get all existing lessons for the same course + intake + subject
        $existingLessons = Lesson::where('course_id', $lesson->course_id)
            ->where('intake_id', $lesson->intake_id)
            ->where('subject_id', $lesson->subject_id)
            ->get();

        $newTitles = $request->lesson;
        $existingCount = $existingLessons->count();
        $newCount = count($newTitles);

        // Update existing lessons
        foreach ($existingLessons as $index => $existingLesson) {
            if (isset($newTitles[$index])) {
                $existingLesson->update([
                    'name' => $newTitles[$index],
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'subject_id' => $request->subject_id,
                ]);
            } else {
                // Remove extra old lessons not in new list
                $existingLesson->delete();
            }
        }

        // Add any new lesson titles if more than existing
        if ($newCount > $existingCount) {
            for ($i = $existingCount; $i < $newCount; $i++) {
                Lesson::create([
                    'name' => $newTitles[$i],
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'subject_id' => $request->subject_id,
                ]);
            }
        }

        return redirect()->route('lesson.index')->with('message-success', 'Lessons updated successfully!');
    }


    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);

        $courses = Course::get();
        $subjects = Subject::get();
        $intakes = Intake::get();

        $lessons = Lesson::get();
        $lessons = $lessons->groupBy(function (Lesson $item) {
            return $item->intake_id . '-' . $item->course_id . '-' . $item->subject_id;
        })->values();
        $groupLessons = Lesson::where('course_id', $lesson->course_id)
            ->where('intake_id', $lesson->intake_id)
            ->where('subject_id', $lesson->subject_id)
            ->get();

        return view('pages.administration.lesson-plan.lesson', [
            'courses' => $courses,
            'subjects' => $subjects,
            'intakes' => $intakes,
            'lessons' => $lessons,
            'lesson' => $lesson,
            'groupLessons' => $groupLessons
        ]);
    }


    public function destroy($id)
    {
        // First find the lesson to get intake, course, subject IDs
        $lesson = Lesson::findOrFail($id);

        $selectedIntake = $lesson->intake_id;
        $selectedCourse = $lesson->course_id;
        $selectedSubject = $lesson->subject_id;

        // Delete all lessons with the same intake, course, and subject
        Lesson::where('intake_id', $selectedIntake)
            ->where('course_id', $selectedCourse)
            ->where('subject_id', $selectedSubject)
            ->delete();

        return redirect()->route('lesson.index')->with('message-success-delete', 'Lessons deleted successfully.');
    }
}

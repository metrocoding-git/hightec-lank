<?php

namespace App\Http\Controllers;

use App\Models\AssignSubject;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Intake;
use App\Models\Subject;

class AssignSubjectController extends Controller
{
    public function index()
    {
        $assign_subjects = AssignSubject::with('subject', 'intake', 'course')->get();
        $assign_subjects = $assign_subjects->groupBy(function ($item) {
            return $item->intake_id . '-' . $item->course_id;
        })->values();

        $subjects = Subject::get();
        $courses = Course::all();
        $intakes = Intake::all();

        return view('pages.administration.academic.assign-subject', [
            'assign_subjects' => $assign_subjects,
            'intakes' => $intakes,
            'courses' => $courses,
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'course_id' => 'required|exists:courses,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',

        ]);

        foreach ($request->subjects as $subjectId) {
            $exists = AssignSubject::where('intake_id', $request->intake_id)
                ->where('course_id', $request->course_id)
                ->where('subject_id', $subjectId)
                ->exists();

            if (!$exists) {
                AssignSubject::create([
                    'intake_id' => $request->intake_id,
                    'course_id' => $request->course_id,
                    'subject_id' => $subjectId,
                ]);
            } else {
                return redirect()->back()->with('message-danger', 'Subject already assigned.');
            }
        }


        return redirect()->route('assign-subject.index')->with('message-success', 'Subject assigned successfully.');
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'intake_id' => 'required',
            'course_id' => 'required',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        // Fetch the original lesson just to validate it exists
        $assignSubject = AssignSubject::findOrFail($id);

        // Get all existing lessons for the same course + intake + subject
        $existingSubjects = AssignSubject::where('course_id', $assignSubject->course_id)
            ->where('intake_id', $assignSubject->intake_id)
            ->get();


        $newTitles = $request->subjects;
        $existingCount = $existingSubjects->count();
        $newCount = count($newTitles);


        // Update existing lessons
        foreach ($existingSubjects as $index => $existingSubject) {
            if (isset($newTitles[$index])) {
                $existingSubject->update([
                    'name' => $newTitles[$index],
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id
                ]);
            } else {
                // Remove extra old lessons not in new list
                $existingSubject->delete();
            }
        }

        // Add any new lesson titles if more than existing
        if ($newCount > $existingCount) {
            for ($i = $existingCount; $i < $newCount; $i++) {
                AssignSubject::create([
                    'course_id' => $request->course_id,
                    'intake_id' => $request->intake_id,
                    'subject_id' => $newTitles[$i],
                ]);
            }
        }

        return redirect()->route('assign-subject.index')->with('message-success', 'Assignment updated successfully!');
    }

    public function edit($id)
    {
        $assign_subjects = AssignSubject::with('subject', 'intake', 'course')->get();
        $assign_subjects = $assign_subjects->groupBy(function ($item) {
            return $item->intake_id . '-' . $item->course_id;
        })->values();

        $subjects = Subject::get();
        $courses = Course::all();
        $intakes = Intake::all();

        $assignSubject = AssignSubject::findOrFail($id);
        $selectedIntake = $assignSubject->intake_id;
        $selectedCourse = $assignSubject->course_id;

        // Get all subject ids assigned to this intake + course
        $selectedSubjects = AssignSubject::where('intake_id', $selectedIntake)
            ->where('course_id', $selectedCourse)
            ->pluck('subject_id')
            ->toArray();


        return view('pages.administration.academic.assign-subject', ['selectedIntake' => $selectedIntake, 'selectedCourse' => $selectedCourse, 'selectedSubjects' => $selectedSubjects, 'assign_subjects' => $assign_subjects, 'subjects' => $subjects, 'courses' => $courses, 'intakes' => $intakes, 'assignSubjectId' => $id,]);
    }


    public function destroy($id)
    {
        $assignSubject = AssignSubject::findOrFail($id);
        $selectedIntake = $assignSubject->intake_id;
        $selectedCourse = $assignSubject->course_id;

        AssignSubject::where('intake_id', $selectedIntake)->where('course_id', $selectedCourse)->delete();

        return redirect()->route('assign-subject.index')->with('message-success-delete', 'Subject deleted successfully.');
    }

    public function getCoursesByIntake($intakeId)
    {
        try {
            $courses = Course::whereHas('intakes', function ($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })->get();

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'intake_id' => $intakeId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'intake_id' => $intakeId
            ], 500);
        }
    }
}

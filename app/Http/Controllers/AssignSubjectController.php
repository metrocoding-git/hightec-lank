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
            'intake_id' => 'required|exists:intakes,id',
            'course_id' => 'required|exists:courses,id',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $intakeId = $request->intake_id;
        $courseId = $request->course_id;
        $newSubjectIds = $request->subjects;

        // Get current subject_ids for this intake_id + course_id
        $currentSubjectIds = AssignSubject::where('intake_id', $intakeId)
            ->where('course_id', $courseId)
            ->pluck('subject_id')
            ->toArray();

        // Find subjects to add and delete
        $toAdd = array_diff($newSubjectIds, $currentSubjectIds);
        $toDelete = array_diff($currentSubjectIds, $newSubjectIds);

        // Delete removed subjects
        if (!empty($toDelete)) {
            AssignSubject::where('intake_id', $intakeId)
                ->where('course_id', $courseId)
                ->whereIn('subject_id', $toDelete)
                ->delete();
        }

        // Add new subjects
        foreach ($toAdd as $subjectId) {
            AssignSubject::create([
                'intake_id' => $intakeId,
                'course_id' => $courseId,
                'subject_id' => $subjectId,
            ]);
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
        Subject::findOrFail($id)->delete();
        return redirect()->route('assign-subject.index')->with('message-success-delete', 'Subject deleted successfully.');
    }
}

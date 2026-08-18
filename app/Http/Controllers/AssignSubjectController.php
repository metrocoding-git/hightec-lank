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
            'times' => 'nullable|array',
        ]);

        foreach ($request->subjects as $subjectId) {
            $exists = AssignSubject::where('intake_id', $request->intake_id)
                ->where('course_id', $request->course_id)
                ->where('subject_id', $subjectId)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('message-danger', 'Subject already assigned.');
            }

            $subject = Subject::find($subjectId);

            AssignSubject::create([
                'intake_id' => $request->intake_id,
                'course_id' => $request->course_id,
                'subject_id' => $subjectId,
                'demonstrator_time' => $request->input("times.$subjectId.demonstrator_time", $subject->demonstrator_time),
                'practical_time' => $request->input("times.$subjectId.practical_time", $subject->practical_time),
            ]);
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
            'times' => 'nullable|array',
        ]);

        $assignSubject = AssignSubject::findOrFail($id);
        $intakeId = $assignSubject->intake_id;
        $courseId = $assignSubject->course_id;

        $existingRows = AssignSubject::where('intake_id', $intakeId)
            ->where('course_id', $courseId)
            ->get()
            ->keyBy('subject_id');

        $existingSubjectIds = $existingRows->keys()->toArray();
        $newSubjectIds = $request->subjects;

        // Unchecked -> remove
        $toRemove = array_diff($existingSubjectIds, $newSubjectIds);
        if (!empty($toRemove)) {
            AssignSubject::where('intake_id', $intakeId)
                ->where('course_id', $courseId)
                ->whereIn('subject_id', $toRemove)
                ->delete();
        }

        foreach ($newSubjectIds as $subjectId) {
            $demonstratorTime = $request->input("times.$subjectId.demonstrator_time");
            $practicalTime = $request->input("times.$subjectId.practical_time");

            if ($existingRows->has($subjectId)) {
                // Still checked -> just update this row's own times
                $existingRows[$subjectId]->update([
                    'demonstrator_time' => $demonstratorTime,
                    'practical_time' => $practicalTime,
                ]);
            } else {
                // Newly checked -> create, falling back to Subject's current values if nothing typed
                $subject = Subject::find($subjectId);
                AssignSubject::create([
                    'intake_id' => $intakeId,
                    'course_id' => $courseId,
                    'subject_id' => $subjectId,
                    'demonstrator_time' => $demonstratorTime ?? $subject->demonstrator_time,
                    'practical_time' => $practicalTime ?? $subject->practical_time,
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

        $existingRows = AssignSubject::where('intake_id', $selectedIntake)
            ->where('course_id', $selectedCourse)
            ->get();

        $selectedSubjects = $existingRows->pluck('subject_id')->toArray();

        $selectedTimes = $existingRows->keyBy('subject_id')->map(function ($row) {
            return [
                'demonstrator_time' => $row->demonstrator_time,
                'practical_time' => $row->practical_time,
            ];
        })->toArray();

        return view('pages.administration.academic.assign-subject', [
            'selectedIntake' => $selectedIntake,
            'selectedCourse' => $selectedCourse,
            'selectedSubjects' => $selectedSubjects,
            'selectedTimes' => $selectedTimes,
            'assign_subjects' => $assign_subjects,
            'subjects' => $subjects,
            'courses' => $courses,
            'intakes' => $intakes,
            'assignSubjectId' => $id,
        ]);
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

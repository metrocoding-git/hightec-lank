<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Intake;

class IntakeController extends Controller
{
    public function index()
    {
        $intakes = Intake::with('courses', 'branch')->get();
        $branches = Branch::all();
        $courses = Course::all();

        return view('pages.administration.academic.intake', compact('intakes', 'branches', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $intake = Intake::create([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        // Sync courses with their respective dates
        $courseData = [];
        if ($request->courses) {
            foreach ($request->courses as $courseId) {
                $courseData[$courseId] = [
                    'start_date' => $request->course_start_dates[$courseId] ?? null,
                    'end_date' => $request->course_end_dates[$courseId] ?? null,
                ];
            }
        }

        // Sync courses with dates using CourseIntake model
        foreach ($courseData as $courseId => $dates) {
            \App\Models\CourseIntake::updateOrCreate(
                [
                    'course_id' => $courseId,
                    'intake_id' => $intake->id,
                ],
                $dates
            );
        }

        return redirect()->route('intake.index')->with('message-success', 'Intake added successfully!');
    }

    public function edit($id)
    {
        $intake = Intake::with('courses')->findOrFail($id);
        $intakes = Intake::with('courses', 'branch')->get();
        $branches = Branch::all();
        $courses = Course::all();

        // Get course intake data with dates for editing
        $courseIntakes = \App\Models\CourseIntake::where('intake_id', $id)->get()->keyBy('course_id');

        return view('pages.administration.academic.intake', [
            'intake' => $intake,
            'intakes' => $intakes,
            'branches' => $branches,
            'courses' => $courses,
            'intakeId' => $id,
            'courseIntakes' => $courseIntakes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'courses' => 'required|array',
            'courses.*' => 'exists:courses,id',
        ]);

        $intake = Intake::findOrFail($id);

        $intake->update([
            'name' => $request->name,
            'branch_id' => $request->branch_id,
        ]);

        // Remove existing course associations
        \App\Models\CourseIntake::where('intake_id', $id)->delete();

        // Add new course associations with dates
        $courseData = [];
        if ($request->courses) {
            foreach ($request->courses as $courseId) {
                $courseData[$courseId] = [
                    'start_date' => $request->course_start_dates[$courseId] ?? null,
                    'end_date' => $request->course_end_dates[$courseId] ?? null,
                ];
            }
        }

        // Create new course associations with dates using CourseIntake model
        foreach ($courseData as $courseId => $dates) {
            \App\Models\CourseIntake::create([
                'course_id' => $courseId,
                'intake_id' => $intake->id,
                'start_date' => $dates['start_date'],
                'end_date' => $dates['end_date'],
            ]);
        }

        return redirect()->route('intake.index')->with('message-success', 'Intake updated successfully!');
    }


    public function destroy($id)
    {
        $intake = Intake::findOrFail($id);

        $intake->courses()->detach();
        $intake->delete();

        return redirect()->route('intake.index')->with('message-success-delete', 'Intake deleted successfully!');
    }
}

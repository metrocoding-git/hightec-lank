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

        $intake->courses()->sync($request->courses);

        $intake->courses()->sync($request->courses); // assuming many-to-many

        return redirect()->route('intake.index')->with('message-success', 'Intake added successfully!');
    }

    public function edit($id)
    {
        $intake = Intake::with('courses')->findOrFail($id);
        $intakes = Intake::with('courses', 'branch')->get();
        $branches = Branch::all();
        $courses = Course::all();

        return view('pages.administration.academic.intake', [
            'intake' => $intake,
            'intakes' => $intakes,
            'branches' => $branches,
            'courses' => $courses,
            'intakeId' => $id,
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

        $intake->courses()->sync($request->courses);

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

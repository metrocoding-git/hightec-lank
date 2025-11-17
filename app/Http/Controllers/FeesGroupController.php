<?php

namespace App\Http\Controllers;

use App\Models\FeesGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeesGroupController extends Controller
{
    // List all fee groups
    public function index()
    {
        $feesGroups = FeesGroup::all();
        return view('pages.student.fees.fee-group', compact('feesGroups'));
    }

    // Store new fee group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        FeesGroup::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Fees group created successfully!');
    }

    // Get fee group data for edit
    public function edit($id)
    {
        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroups = FeesGroup::all(); // to show the list along with edit form
        return view('pages.student.fees.fee-group', compact('feesGroup', 'feesGroups'));
    }

    // Update fee group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroup->update($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Fees group updated successfully!');
    }

    // Delete fee group
    public function delete($id)
    {
        $feesGroup = FeesGroup::findOrFail($id);
        $feesGroup->delete();

        return redirect()->back()->with('success', 'Fees group deleted successfully!');
    }

    public function coursePaidView()
    {
        $branches = Auth::user()->branches;
        $students = \App\Models\Student::all();
        $courses = []; // Empty for dynamic loading
        return view('pages.student.fees.course-paid', compact('branches', 'students', 'courses'));
    }
}

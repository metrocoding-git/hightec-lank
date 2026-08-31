<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeeInvoices;
use App\Models\FeesGroup;
use App\Models\FoodPayment;
use App\Models\InvoicesPayments;
use App\Models\LunchboxSequence;
use App\Models\PaidCourse;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

}

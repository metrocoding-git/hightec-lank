<?php

namespace App\Http\Controllers;

use App\Models\ItemIssue;
use App\Models\Role;
use App\Models\Category;
use App\Models\Inventory_item;
use App\Models\Student;
use App\Models\User;
use App\Models\Course; // Assuming Course model for classes
use App\Models\Intake; // Assuming Intake model for sections
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ItemIssueController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::where('status', 1)->get();
            // $itemCat = Category::get();
            $issuedItems = ItemIssue::where('active_status', 1)->get();
            $classes = Course::get(); // Fetch  classes
            $staffs = User::get(); // Fetch users
            $inventoryItems = Inventory_item::get();//item
            $intakes = Intake::get();//section
            $students = Student::get();//student
            $categories = Category::get();

            return view('pages.account.inventory.item-issue', compact('roles',  'issuedItems', 'classes', 'staffs','intakes','students','inventoryItems' ,'categories'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function saveItemIssueData(Request $request)
    {
        $request->validate([
            'role_id' => 'required',
            'item_category_id' => 'required',
            'item_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'issue_to' => 'nullable|exists:users,id', // For staff
            'student_id' => 'nullable|exists:students,id', // For students
            'section_id' => 'nullable|exists:intakes,id',
            'class_id' => 'nullable|exists:courses,id',
        ]);

        try {
            DB::beginTransaction();

            $itemIssue = new ItemIssue();
            $itemIssue->role_id = $request->role_id;
            $itemIssue->item_category_id = $request->item_category_id;
            $itemIssue->item_id = $request->item_id;
            $itemIssue->issue_date = date('Y-m-d', strtotime($request->issue_date));
            $itemIssue->due_date = date('Y-m-d', strtotime($request->due_date));
            $itemIssue->quantity = $request->quantity;
            $itemIssue->description = $request->description;
            $itemIssue->issue_status = 'I'; // Issued
            $itemIssue->active_status = 1;

            if ($request->role_id == 2) { // Assuming role_id 2 is for students
                $itemIssue->student_id = $request->student_id;
                $itemIssue->section_id = $request->section_id;
                $itemIssue->class_id = $request->class_id;
                $itemIssue->issue_to = null; // Staff ID should be null for students
            } else {
                $itemIssue->issue_to = $request->staff_id;
                $itemIssue->student_id = null;
                $itemIssue->section_id = null;
                $itemIssue->class_id = null;
            }

            $itemIssue->save();

            DB::commit();

            return redirect()->back()->with('success', 'Item issued successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $roles = Role::where('status', 1)->get();
            $issuedItems = ItemIssue::where('active_status', 1)->get();
            $classes = Course::get();
            $staffs = User::get();
            $inventoryItems = Inventory_item::get();
            $intakes = Intake::get();
            $students = Student::get();
            $categories = Category::get();

            $editData = ItemIssue::findOrFail($id);

            return view('pages.account.inventory.item-issue', compact('roles', 'issuedItems', 'classes', 'staffs', 'intakes', 'students', 'inventoryItems', 'categories', 'editData'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required',
            'item_category_id' => 'required',
            'item_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'issue_to' => 'nullable|exists:users,id', // For staff
            'student_id' => 'nullable|exists:students,id', // For students
            'section_id' => 'nullable|exists:intakes,id',
            'class_id' => 'nullable|exists:courses,id',
        ]);

        try {
            DB::beginTransaction();

            $itemIssue = ItemIssue::findOrFail($id);
            $itemIssue->role_id = $request->role_id;
            $itemIssue->item_category_id = $request->item_category_id;
            $itemIssue->item_id = $request->item_id;
            $itemIssue->issue_date = date('Y-m-d', strtotime($request->issue_date));
            $itemIssue->due_date = date('Y-m-d', strtotime($request->due_date));
            $itemIssue->quantity = $request->quantity;
            $itemIssue->description = $request->description;
            $itemIssue->issue_status = 'I'; // Issued
            $itemIssue->active_status = 1;

            if ($request->role_id == 2) { // Assuming role_id 2 is for students
                $itemIssue->student_id = $request->student_id;
                $itemIssue->section_id = $request->section_id;
                $itemIssue->class_id = $request->class_id;
                $itemIssue->issue_to = null; // Staff ID should be null for students
            } else {
                $itemIssue->issue_to = $request->staff_id;
                $itemIssue->student_id = null;
                $itemIssue->section_id = null;
                $itemIssue->class_id = null;
            }

            $itemIssue->save();

            DB::commit();

            return redirect()->route('item-issue')->with('success', 'Item issue updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function returnItemView($id)
    {
        try {
            $itemIssue = ItemIssue::findOrFail($id);
            return view('pages.account.inventory.return-item-modal', compact('itemIssue'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function IssueItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issues,id',
        ]);

        try {
            DB::beginTransaction();

            $itemIssue = ItemIssue::findOrFail($request->id);
            $itemIssue->issue_status = 'I'; // Returned
            // $itemIssue->return_date = date('Y-m-d'); // Set return date to today
            $itemIssue->save();

            DB::commit();

            return redirect()->route('item-issue')->with('success', 'Item issued successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
    public function returnItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issues,id',
        ]);

        try {
            DB::beginTransaction();

            $itemIssue = ItemIssue::findOrFail($request->id);
            $itemIssue->issue_status = 'R'; // Returned
            // $itemIssue->return_date = date('Y-m-d'); // Set return date to today
            $itemIssue->save();

            DB::commit();

            return redirect()->route('item-issue')->with('success', 'Item returned successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}

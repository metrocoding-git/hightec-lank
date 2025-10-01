<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ItemIssue;
use App\Models\Role;
use App\Models\Category;
use App\Models\Inventory_item;
use App\Models\Student;
use App\Models\User;
use App\Models\Course; // Assuming Course model for classes
use App\Models\Intake; // Assuming Intake model for sections
use App\Models\FeeInvoice; // Fee invoice model
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemIssueController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::where('status', 1)->get();
            // $itemCat = Category::get();
            $issuedItems = ItemIssue::where('active_status', 1)->get();
            $courses = Course::get(); // Fetch  classes
            $staffs = User::get(); // Fetch users
            $inventoryItems = Inventory_item::get();//item
            $intakes = Intake::get();//section
            $students = Student::get();//student
            $categories = Category::get();
            $branches = Branch::get();

            // Temporarily dump data to inspect
            // dd($categories, $inventoryItems);

            return view('pages.account.inventory.item-issue', compact('roles',  'issuedItems', 'courses', 'staffs','intakes','students','inventoryItems' ,'categories','branches'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }
    public function issueListView(){
        try {
            $issuedItems = ItemIssue::with(['item', 'category', 'student', 'staff', 'branch'])
                ->where('active_status', 1)
                ->get();

            return view('pages.account.inventory.item-issue-list', compact('issuedItems'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function saveItemIssueData(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'issue_to' => 'nullable|exists:users,id', // For staff
            'student_id' => 'nullable|exists:students,id', // For students
            'intake_id' => 'nullable|exists:intakes,id',
            'course_id' => 'nullable|exists:courses,id',
            'items' => 'required|array|min:1', // Ensure at least one item is present
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.item_category_id' => 'required|exists:categories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $itemIssue = new ItemIssue();
                $itemIssue->branch_id = $request->branch_id;
                $itemIssue->item_category_id = $itemData['item_category_id'];
                $itemIssue->item_id = $itemData['item_id'];
                $itemIssue->issue_date = date('Y-m-d', strtotime($request->issue_date));
                $itemIssue->due_date = date('Y-m-d', strtotime($request->due_date));
                $itemIssue->quantity = $itemData['quantity'];
                $itemIssue->description = $request->description; // Overall description
                $itemIssue->issue_status = 'I'; // Issued
                $itemIssue->active_status = 1;

                // Check if student is selected or staff
                if ($request->student_id) {
                    $itemIssue->student_id = $request->student_id;
                    $itemIssue->course_id = $request->course_id;
                    $itemIssue->intake_id = $request->intake_id;
                    $itemIssue->issue_to = null; // Staff ID should be null for students
                } else {
                    $itemIssue->issue_to = $request->staff_id;
                    $itemIssue->student_id = null;
                    $itemIssue->course_id = null;
                    $itemIssue->intake_id = null;
                }

                $itemIssue->save();
            }


            DB::commit();

            return redirect()->back()->with('success', 'Items issued successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $issuedItems = ItemIssue::where('active_status', 1)->get();
            $courses = Course::get();
            $staffs = User::get();
            $inventoryItems = Inventory_item::get();
            $intakes = Intake::get();
            $students = Student::get();
            $categories = Category::get();
            $branches = Branch::get();

            $editData = ItemIssue::findOrFail($id);

            return view('pages.account.inventory.item-issue', compact('issuedItems', 'courses', 'staffs', 'intakes', 'students', 'inventoryItems', 'categories', 'branches', 'editData'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'issue_to' => 'nullable|exists:users,id', // For staff
            'student_id' => 'nullable|exists:students,id', // For students
            'intake_id' => 'nullable|exists:intakes,id',
            'course_id' => 'nullable|exists:courses,id',
            'items' => 'required|array|min:1', // Ensure at least one item is present
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.item_category_id' => 'required|exists:categories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Find and delete the original item issue record
            // This assumes $id refers to a single ItemIssue record that is being replaced
            // by potentially multiple new records.
            $originalItemIssue = ItemIssue::findOrFail($id);
            $originalItemIssue->delete();

            foreach ($request->items as $itemData) {
                $itemIssue = new ItemIssue();
                $itemIssue->branch_id = $request->branch_id;
                $itemIssue->item_category_id = $itemData['item_category_id'];
                $itemIssue->item_id = $itemData['item_id'];
                $itemIssue->issue_date = date('Y-m-d', strtotime($request->issue_date));
                $itemIssue->due_date = date('Y-m-d', strtotime($request->due_date));
                $itemIssue->quantity = $itemData['quantity'];
                $itemIssue->description = $request->description; // Overall description
                $itemIssue->issue_status = 'I'; // Issued
                $itemIssue->active_status = 1;

                // Check if student is selected or staff
                if ($request->student_id) {
                    $itemIssue->student_id = $request->student_id;
                    $itemIssue->course_id = $request->course_id;
                    $itemIssue->intake_id = $request->intake_id;
                    $itemIssue->issue_to = null; // Staff ID should be null for students
                } else {
                    $itemIssue->issue_to = $request->staff_id;
                    $itemIssue->student_id = null;
                    $itemIssue->course_id = null;
                    $itemIssue->intake_id = null;
                }

                $itemIssue->save();
            }

            DB::commit();

            return redirect()->route('item-issue')->with('success', 'Items updated successfully.');
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

    public function getIntakesByBranch(Request $request, $branchId)
    {
        try {
            $branch_id = $branchId;
            Log::info('=== getIntakesByBranch CALLED ===');
            Log::info('Branch ID from route: ' . $branch_id);
            Log::info('Request method: ' . $request->method());
            Log::info('Request URL: ' . $request->fullUrl());

            $intakes = Intake::where('branch_id', $branch_id)->get();
            Log::info('Found intakes count: ' . $intakes->count());
            Log::info('Intakes data: ' . json_encode($intakes->toArray()));

            return response()->json($intakes);
        } catch (Exception $e) {
            Log::error('Error in getIntakesByBranch: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getCoursesByIntake(Request $request, $intakeId)
    {
        try {
            $intake_id = $intakeId; // Use the route parameter
            $courses = Course::whereHas('intakes', function($query) use ($intake_id) {
                $query->where('intake_id', $intake_id);
            })->get();

            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getStudentsByCourse(Request $request, $courseId)
    {
        try {
            $course_id = $courseId; // Use the route parameter
            $students = Student::whereHas('courses', function($query) use ($course_id) {
                $query->where('course_id', $course_id);
            })->get();

            return response()->json($students);
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getItemsByCategory(Request $request, $categoryId)
    {
        try {
            $category_id = $categoryId; // Use the route parameter
            $items = Inventory_item::where('item_category_id', $category_id)->get();

            return response()->json($items);
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getStudentFeeData(Request $request, $studentId)
    {
        try {
            $student_id = $studentId; // Use the route parameter
            Log::info('=== getStudentFeeData CALLED ===');
            Log::info('Student ID: ' . $student_id);

            // First check if student exists
            $student = Student::find($student_id);
            if (!$student) {
                Log::warning('Student not found: ' . $student_id);
                return response()->json([
                    'student_name' => 'Student not found',
                    'total_invoices' => 0,
                    'total_fees' => 0,
                    'total_paid' => 0,
                    'total_due' => 0,
                    'fee_invoices' => []
                ]);
            }

            // Get student fee invoices using Eloquent relationships
            $feeInvoices = FeeInvoice::where('student_id', $student_id)
                ->with(['invoice_item.feeType'])
                ->get();

            Log::info('Found fee invoices count: ' . $feeInvoices->count());

            // Calculate total fees and payments
            $totalFees = $feeInvoices->sum('total_amount');
            $totalPaid = $feeInvoices->sum('paid_amount');
            $totalDue = $totalFees - $totalPaid;

            // Format fee invoices for display
            $formattedInvoices = [];
            foreach ($feeInvoices as $invoice) {
                $invoiceItems = $invoice->invoice_item ?? collect();
                foreach ($invoiceItems as $item) {
                    $formattedInvoices[] = [
                        'fee_type_name' => $item->feeType->name ?? 'N/A',
                        'amount' => $item->amount ?? 0,
                        'paid_amount' => $item->paid_amount ?? 0,
                    ];
                }
            }

            $studentData = [
                'student_name' => $student->student_name ?? 'N/A',
                'total_invoices' => $feeInvoices->count(),
                'total_fees' => $totalFees,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'fee_invoices' => $formattedInvoices
            ];

            Log::info('Student data prepared: ' . json_encode($studentData));

            return response()->json($studentData);
        } catch (Exception $e) {
            Log::error('Error in getStudentFeeData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Operation Failed: ' . $e->getMessage(),
                'student_name' => 'Error loading data',
                'total_invoices' => 0,
                'total_fees' => 0,
                'total_paid' => 0,
                'total_due' => 0,
                'fee_invoices' => []
            ], 500);
        }
    }
}

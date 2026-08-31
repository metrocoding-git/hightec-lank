<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\ItemIssue;
use App\Models\ItemIssueBatch;
use App\Models\ItemIssueItem;
use App\Models\ItemInventory;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Student;
use App\Models\Course;
use App\Models\CourseIntake;
use App\Models\Intake;
use App\Models\StudentCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ItemIssueController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::where('status', 1)->get();
            // $itemCat = Category::get();
            $issuedItems = ItemIssueBatch::where('active_status', 1)->get();
            $courses = Course::get(); // Fetch  classes
            $staffs = User::get(); // Fetch users
            $inventoryItems = InventoryItem::get(); //item
            $intakes = Intake::get(); //section
            $students = Student::get(); //student
            $categories = Category::get();
            $branches = Auth::user()->branches;

            // Temporarily dump data to inspect
            // dd($categories, $inventoryItems);

            return view('pages.account.inventory.item-issue', compact('roles',  'issuedItems', 'courses', 'staffs', 'intakes', 'students', 'inventoryItems', 'categories', 'branches'));
        } catch (Exception $e) {
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }
    public function issueListView(Request $request)
    {
        try {
            // Performance fix (Section 38): this list previously loaded EVERY
            // active batch (with several eager-loaded relations) on every page
            // view, which got slow once the table had a lot of history. Added
            // a date-range filter on issue_date, defaulting to the last month,
            // so a normal page load only ever pulls a bounded window of rows.
            // Older data is still reachable by picking a wider range in the
            // filter form above the table.
            $request->validate([
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date',
            ]);

            $fromDateInput = $request->filled('from_date') ? $request->from_date : now()->subMonth()->format('Y-m-d');
            $toDateInput = $request->filled('to_date') ? $request->to_date : now()->format('Y-m-d');

            $fromDate = Carbon::parse($fromDateInput)->startOfDay();
            $toDate = Carbon::parse($toDateInput)->endOfDay();

            $issuedBatches = ItemIssueBatch::with(['items.item', 'items.category', 'student', 'staff', 'branch', 'course', 'createdBy'])
                ->where('active_status', 1)
                ->whereBetween('issue_date', [$fromDate, $toDate])
                ->orderBy('issue_date', 'desc')
                ->get();

            // Add expiration status to each batch
            $batchesWithCourseStatus = $issuedBatches->map(function ($batch) {
                $course = $batch->course;
                $intake = $batch->intake;
                if ($course) {
                    $startDate = Carbon::parse($intake->start_date);
                    $expirationDate = $course->expiration_date;
                    $today = now();

                    if ($expirationDate && is_numeric($expirationDate)) {
                        $expireDate = $startDate->copy()->addDays((int)$expirationDate);
                        $isExpired = $expireDate->lessThan($today);
                        $expireStatus = $isExpired ? 'Expired' : 'Not Expired';
                        $expireClass = $isExpired ? 'bg-danger' : 'bg-success';
                    } else {
                        $expireStatus = 'No Expiration';
                        $expireClass = 'bg-info';
                    }
                } else {
                    $expireStatus = 'No Course';
                    $expireClass = 'bg-secondary';
                }

                return [
                    'batch' => $batch,
                    'course_name' => $course ? $course->name : 'N/A',
                    'intake_name' => $intake ? $intake->name : 'N/A',
                    'expire_status' => $expireStatus,
                    'expire_class' => $expireClass,
                ];
            });

            return view('pages.account.inventory.item-issue-list', compact('batchesWithCourseStatus', 'fromDateInput', 'toDateInput'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function saveItemIssueData(Request $request)
    {
        // Debug: Check what categories exist
        $existingCategories = Category::pluck('id', 'name')->toArray();

        // Debug: Check what items exist
        $existingItems = InventoryItem::pluck('id', 'item_name')->toArray();

        $request->validate([
            'branch_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'student_id' => 'nullable|exists:students,id',
            'intake_id' => 'nullable|exists:intakes,id',
            'course_id' => 'nullable|exists:courses,id',
            'staff_id' => 'nullable|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.item_category_id' => 'required|exists:categories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.*.item_category_id.exists' => 'The selected category ID is invalid. Available categories: ' . implode(', ', array_keys($existingCategories)),
            'items.*.item_id.exists' => 'The selected item ID is invalid. Available items: ' . implode(', ', array_keys($existingItems)),
        ]);

        try {
            DB::beginTransaction();

            $student_category_id = Student::where('id', $request->student_id)->value('student_category_id');

            // Create the main batch record
            $batch = new ItemIssueBatch();
            $batch->branch_id = $request->branch_id;
            $batch->issue_date = date('Y-m-d', strtotime($request->issue_date));
            $batch->due_date = date('Y-m-d', strtotime($request->due_date));
            $batch->description = $request->description;
            $batch->issue_status = 'I'; // Issued
            $batch->active_status = 1;
            $batch->student_category_id = $student_category_id;
            $batch->created_by = Auth::id();

            // Check if student is selected or staff
            if ($request->student_id) {
                $batch->student_id = $request->student_id;
                $batch->course_id = $request->course_id;
                $batch->intake_id = $request->intake_id;
                $batch->issue_to = null; // Staff ID should be null for students
            } else {
                $batch->issue_to = $request->staff_id;
                $batch->student_id = null;
                $batch->course_id = null;
                $batch->intake_id = null;
            }

            $batch->save();

            // Update student_courses status if student, intake, and course are provided
            if ($request->student_id && $request->intake_id && $request->course_id) {
                $studentCourse = StudentCourse::where('student_id', $request->student_id)
                    ->where('intake_id', $request->intake_id)
                    ->where('course_id', $request->course_id)
                    ->first();

                if ($studentCourse) {
                    $studentCourse->status = 'start';
                    $studentCourse->save();
                }
            }

            $savedItems = [];
            foreach ($request->items as $index => $itemData) {

                $category = Category::find($itemData['item_category_id']);
                $item = InventoryItem::find($itemData['item_id']);

                if (!$category) {
                    throw new Exception('Category not found: ' . $itemData['item_category_id']);
                }

                if (!$item) {
                    throw new Exception('Item not found: ' . $itemData['item_id']);
                }

                // Verify item belongs to the selected category
                if ($item->item_category_id != $itemData['item_category_id']) {
                    throw new Exception('Item does not belong to the selected category');
                }

                // Create item record within the batch
                $batchItem = new ItemIssueItem();
                $batchItem->batch_id = $batch->id;
                $batchItem->item_category_id = $itemData['item_category_id'];
                $batchItem->item_id = $itemData['item_id'];
                $batchItem->quantity = $itemData['quantity'];
                $batchItem->save();

                $savedItems[] = $batchItem->id;

                $currentBalance = ItemInventory::getCurrentBalance($itemData['item_id'], $request->branch_id);

                $newBalance = $currentBalance - $itemData['quantity'];

                ItemInventory::create([
                    'item_id' => $itemData['item_id'],
                    'branch_id' => $request->branch_id,
                    'transaction_type' => 'issue',
                    'transaction_id' => $batch->id,
                    'quantity' => $itemData['quantity'],
                    'current_balance' => $newBalance,
                    'reference_no' => 'BATCH-' . $batch->id,
                    'transaction_date' => $batch->issue_date,
                    'description' => $request->description ?? 'Item issued from batch',
                ]);
            }
            return redirect()->back()->with('success', 'Items issued successfully. Total items: ' . count($savedItems) . ', Batch ID: ' . $batch->id);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }


    public function ReturnBack(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issue_batches,id',
        ]);

        try {
            DB::beginTransaction();

            $batch = ItemIssueBatch::findOrFail($request->id);

            if ($batch->issue_status === 'I') {
                // If already issued, reverse the transaction (return items to inventory)
                $batchItems = ItemIssueItem::where('batch_id', $batch->id)->get();

                foreach ($batchItems as $batchItem) {
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id, $batch->branch_id);
                    $newBalance = $currentBalance + $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue_return',
                        'branch_id' => $batch->branch_id,
                        'transaction_id' => $batch->id,
                        'quantity' => $batchItem->quantity,
                        'current_balance' => $newBalance,
                        'reference_no' => 'BATCH-' . $batch->id,
                        'transaction_date' => now()->toDateString(),
                        'description' => 'Items returned to inventory (reversed issue)',
                    ]);
                }

                $batch->issue_status = 'P'; // Pending (reversed)
                $message = 'Batch issue reversed successfully. Items returned to inventory.';
            } else {
                // Issue items (normal issue process)
                $batchItems = ItemIssueItem::where('batch_id', $batch->id)->get();

                foreach ($batchItems as $batchItem) {
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id, $batch->branch_id);
                    $newBalance = $currentBalance - $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue',
                        'branch_id' => $batch->branch_id,
                        'transaction_id' => $batch->id,
                        'quantity' => $batchItem->quantity,
                        'current_balance' => $newBalance,
                        'reference_no' => 'BATCH-' . $batch->id,
                        'transaction_date' => now()->toDateString(),
                        'description' => 'Items issued from inventory',
                    ]);
                }

                $batch->issue_status = 'I'; // Issued
                $message = 'Batch issued successfully.';
            }

            $batch->save();

            DB::commit();

            return redirect()->route('item-issue-list')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
    public function getBatchDetails(Request $request, $batchId)
    {
        try {
            Log::info('=== getBatchDetails CALLED ===');
            Log::info('Batch ID: ' . $batchId);

            // Get batch with all related items and their relationships
            $batch = ItemIssueBatch::with([
                'items' => function ($query) {
                    $query->with(['item', 'category']);
                },
                'student',
                'staff',
                'branch'
            ])->find($batchId);

            if (!$batch) {
                Log::warning('Batch not found: ' . $batchId);
                return response()->json(['error' => 'Batch not found'], 404);
            }

            Log::info('Batch found with ' . $batch->items->count() . ' items');

            // Format items for display
            $formattedItems = [];
            foreach ($batch->items as $item) {
                $formattedItems[] = [
                    'item_name' => $item->item ? $item->item->item_name : 'N/A',
                    'category_name' => $item->category ? $item->category->name : 'N/A',
                    'quantity' => $item->quantity,
                    'item_id' => $item->item_id,
                    'category_id' => $item->item_category_id,
                ];
            }

            $batchData = [
                'id' => $batch->id,
                'issue_date' => $batch->issue_date,
                'due_date' => $batch->due_date,
                'description' => $batch->description,
                'issue_status' => $batch->issue_status,
                'status_text' => $batch->issue_status === 'I' ? 'Issued' : 'Returned',
                'student_name' => $batch->student ? $batch->student->student_name : 'N/A',
                'staff_name' => $batch->staff ? $batch->staff->name : 'N/A',
                'branch_name' => $batch->branch ? $batch->branch->name : 'N/A',
                'total_items' => $batch->items->sum('quantity'),
                'items' => $formattedItems
            ];

            Log::info('Batch data prepared: ' . json_encode($batchData));

            return response()->json($batchData);
        } catch (Exception $e) {
            Log::error('Error in getBatchDetails: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function returnItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issue_batches,id',
        ]);

        try {
            DB::beginTransaction();

            $batch = ItemIssueBatch::findOrFail($request->id);

            $intake = Intake::find($batch->intake_id);
            $course = Course::find($batch->course_id);
            $expireDate = Carbon::parse($intake->start_date)->addDays((int)$course->expiration_date);

            $canAdd = now()->lte($expireDate);
            if ($canAdd) {
                // Get all items in this batch before updating status.
                $batchItems = ItemIssueItem::where('batch_id', $batch->id)->get();

                // Track inventory changes for each item return (increase inventory).
                foreach ($batchItems as $batchItem) {
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id, $batch->branch_id);
                    $newBalance = $currentBalance + $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue_return',
                        'branch_id' => $batch->branch_id,
                        'transaction_id' => $batch->id,
                        'quantity' => $batchItem->quantity,
                        'current_balance' => $newBalance,
                        'reference_no' => 'BATCH-' . $batch->id,
                        'transaction_date' => now()->toDateString(),
                        'description' => 'Items returned from batch',
                    ]);
                }

                $batch->issue_status = 'R';
                $batch->save();

                $batchFind = ItemIssueBatch::where('branch_id', $batch->branch_id)
                    ->where('student_id', $batch->student_id)
                    ->where('intake_id', $batch->intake_id)
                    ->where('course_id', $batch->course_id)
                    ->where('issue_status', 'I')
                    ->first();

                if (!$batchFind) {
                    $student_course = StudentCourse::where('student_id', $batch->student_id)
                        ->where('course_id', $batch->course_id)
                        ->where('intake_id', $batch->intake_id)
                        ->where('branch_id', $batch->branch_id)
                        ->first();

                    if ($student_course) {
                        $student_course->status = 'return';
                        $student_course->save();
                    }
                }

                DB::commit();

                return redirect()->route('item-issue-list')->with('success', 'Batch returned successfully.');
            }

            return redirect()->back()->with('error', 'Course Period has expired');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function checkStudentCourseStatus(Request $request)
    {
        try {
            $student_id = $request->student_id;
            $course_id = $request->course_id;
            $intake_id = $request->intake_id;

            $studentCourse = StudentCourse::where('student_id', $student_id)
                ->where('course_id', $course_id)
                ->where('intake_id', $intake_id)
                ->first();

            if ($studentCourse && $studentCourse->status === 'start') {
                return response()->json([
                    'can_issue' => true,
                    'message' => 'Items can be issued.'
                ]);
            }

            $intake = Intake::find($intake_id);
            $course = Course::find($course_id);
            $expireDate = Carbon::parse($intake->start_date)->addDays((int)$course->expiration_date);

            $canAdd = now()->lte($expireDate);
            $message = $canAdd
                ? 'Items can be issued.'
                : 'Items Cannot be issued. expired';

            return response()->json([
                'can_issue' => $canAdd,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'can_issue' => false,
                'message' => 'Error checking course status.'
            ], 500);
        }
    }
}
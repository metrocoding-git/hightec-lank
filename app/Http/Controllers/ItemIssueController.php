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
use App\Models\Inventory_item;
use App\Models\Student;
use App\Models\Course;
use App\Models\Intake;
use App\Models\FeeInvoice;

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
            $issuedBatches = ItemIssueBatch::with(['items.item', 'items.category', 'student', 'staff', 'branch', 'course'])
                ->where('active_status', 1)
                ->get();

            // Add expiration status to each batch
            $batchesWithCourseStatus = $issuedBatches->map(function($batch) {
                $course = $batch->course;
                if ($course) {
                    $startDate = $course->created_at;
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
                    'expire_status' => $expireStatus,
                    'expire_class' => $expireClass,
                ];
            });

            return view('pages.account.inventory.item-issue-list', compact('batchesWithCourseStatus'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    public function saveItemIssueData(Request $request)
    {
        // Debug: Log the request data
        Log::info('=== saveItemIssueData CALLED ===');
        Log::info('Request data: ' . json_encode($request->all()));

        // Debug: Check what categories exist
        $existingCategories = Category::pluck('id', 'name')->toArray();
        Log::info('Existing categories: ' . json_encode($existingCategories));

        // Debug: Check what items exist
        $existingItems = Inventory_item::pluck('id', 'item_name')->toArray();
        Log::info('Existing items: ' . json_encode($existingItems));

        $request->validate([
            'branch_id' => 'required',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'student_id' => 'nullable|exists:students,id', // For students
            'intake_id' => 'nullable|exists:intakes,id',
            'course_id' => 'nullable|exists:courses,id',
            'staff_id' => 'nullable|exists:users,id', // For staff
            'items' => 'required|array|min:1', // Ensure at least one item is present
            'items.*.item_id' => 'required|exists:inventory_items,id',
            'items.*.item_category_id' => 'required|exists:categories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.*.item_category_id.exists' => 'The selected category ID is invalid. Available categories: ' . implode(', ', array_keys($existingCategories)),
            'items.*.item_id.exists' => 'The selected item ID is invalid. Available items: ' . implode(', ', array_keys($existingItems)),
        ]);

        try {
            DB::beginTransaction();

            Log::info('Processing ' . count($request->items) . ' items');

            // Create the main batch record
            $batch = new ItemIssueBatch();
            $batch->branch_id = $request->branch_id;
            $batch->issue_date = date('Y-m-d', strtotime($request->issue_date));
            $batch->due_date = date('Y-m-d', strtotime($request->due_date));
            $batch->description = $request->description;
            $batch->issue_status = 'I'; // Issued
            $batch->active_status = 1;

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
            Log::info('Batch created with ID: ' . $batch->id);

            $savedItems = [];
            foreach ($request->items as $index => $itemData) {
                Log::info('Processing item ' . ($index + 1) . ': ' . json_encode($itemData));

                // Validate that category_id and item_id exist and match
                $category = Category::find($itemData['item_category_id']);
                $item = Inventory_item::find($itemData['item_id']);

                if (!$category) {
                    Log::error('Category not found: ' . $itemData['item_category_id']);
                    throw new Exception('Category not found: ' . $itemData['item_category_id']);
                }

                if (!$item) {
                    Log::error('Item not found: ' . $itemData['item_id']);
                    throw new Exception('Item not found: ' . $itemData['item_id']);
                }

                // Verify item belongs to the selected category
                if ($item->item_category_id != $itemData['item_category_id']) {
                    Log::error('Item does not belong to selected category. Item: ' . $item->id . ', Category: ' . $item->item_category_id . ', Selected Category: ' . $itemData['item_category_id']);
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
                Log::info('Batch item saved with ID: ' . $batchItem->id);

                // Track inventory changes for this item issue (decrease inventory)
                $currentBalance = ItemInventory::getCurrentBalance($itemData['item_id']);
                $newBalance = $currentBalance - $itemData['quantity'];

                ItemInventory::create([
                    'item_id' => $itemData['item_id'],
                    'transaction_type' => 'issue',
                    'transaction_id' => $batch->id,
                    'quantity' => $itemData['quantity'],
                    'current_balance' => $newBalance,
                    'reference_no' => 'BATCH-' . $batch->id,
                    'transaction_date' => $batch->issue_date,
                    'description' => $request->description ?? 'Item issued from batch',
                ]);
            }

            DB::commit();
            Log::info('All items saved successfully. Batch ID: ' . $batch->id . ', Item IDs: ' . implode(', ', $savedItems));

            return redirect()->back()->with('success', 'Items issued successfully. Total items: ' . count($savedItems) . ', Batch ID: ' . $batch->id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saving item issue: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }

    // public function edit($id)
    // {
    //     try {
    //         $issuedItems = ItemIssue::where('active_status', 1)->get();
    //         $courses = Course::get();
    //         $staffs = User::get();
    //         $inventoryItems = Inventory_item::get();
    //         $intakes = Intake::get();
    //         $students = Student::get();
    //         $categories = Category::get();
    //         $branches = Branch::get();

    //         $editData = ItemIssue::findOrFail($id);

    //         return view('pages.account.inventory.item-issue', compact('issuedItems', 'courses', 'staffs', 'intakes', 'students', 'inventoryItems', 'categories', 'branches', 'editData'));
    //     } catch (Exception $e) {
    //         return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'branch_id' => 'required',
    //         'issue_date' => 'required|date',
    //         'due_date' => 'required|date',
    //         'issue_to' => 'nullable|exists:users,id', // For staff
    //         'student_id' => 'nullable|exists:students,id', // For students
    //         'intake_id' => 'nullable|exists:intakes,id',
    //         'course_id' => 'nullable|exists:courses,id',
    //         'items' => 'required|array|min:1', // Ensure at least one item is present
    //         'items.*.item_id' => 'required|exists:inventory_items,id',
    //         'items.*.item_category_id' => 'required|exists:categories,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         // Find and delete the original item issue record
    //         // This assumes $id refers to a single ItemIssue record that is being replaced
    //         // by potentially multiple new records.
    //         $originalItemIssue = ItemIssue::findOrFail($id);
    //         $originalItemIssue->delete();

    //         foreach ($request->items as $itemData) {
    //             $itemIssue = new ItemIssue();
    //             $itemIssue->branch_id = $request->branch_id;
    //             $itemIssue->item_category_id = $itemData['item_category_id'];
    //             $itemIssue->item_id = $itemData['item_id'];
    //             $itemIssue->issue_date = date('Y-m-d', strtotime($request->issue_date));
    //             $itemIssue->due_date = date('Y-m-d', strtotime($request->due_date));
    //             $itemIssue->quantity = $itemData['quantity'];
    //             $itemIssue->description = $request->description; // Overall description
    //             $itemIssue->issue_status = 'I'; // Issued
    //             $itemIssue->active_status = 1;

    //             // Check if student is selected or staff
    //             if ($request->student_id) {
    //                 $itemIssue->student_id = $request->student_id;
    //                 $itemIssue->course_id = $request->course_id;
    //                 $itemIssue->intake_id = $request->intake_id;
    //                 $itemIssue->issue_to = null; // Staff ID should be null for students
    //             } else {
    //                 $itemIssue->issue_to = $request->staff_id;
    //                 $itemIssue->student_id = null;
    //                 $itemIssue->course_id = null;
    //                 $itemIssue->intake_id = null;
    //             }

    //             $itemIssue->save();
    //         }

    //         DB::commit();

    //         return redirect()->route('item-issue')->with('success', 'Items updated successfully.');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
    //     }
    // }

    // public function returnItemView($id)
    // {
    //     try {
    //         $itemIssue = ItemIssue::findOrFail($id);
    //         return view('pages.account.inventory.return-item-modal', compact('itemIssue'));
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
    //     }
    // }

    public function IssueItem(Request $request)
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
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id);
                    $newBalance = $currentBalance + $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue_return',
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
                    $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id);
                    $newBalance = $currentBalance - $batchItem->quantity;

                    ItemInventory::create([
                        'item_id' => $batchItem->item_id,
                        'transaction_type' => 'issue',
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
    public function returnItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_issue_batches,id',
        ]);

        try {
            DB::beginTransaction();

            $batch = ItemIssueBatch::findOrFail($request->id);

            // Get all items in this batch before updating status
            $batchItems = ItemIssueItem::where('batch_id', $batch->id)->get();

            // Track inventory changes for each item return (increase inventory)
            foreach ($batchItems as $batchItem) {
                $currentBalance = ItemInventory::getCurrentBalance($batchItem->item_id);
                $newBalance = $currentBalance + $batchItem->quantity;

                ItemInventory::create([
                    'item_id' => $batchItem->item_id,
                    'transaction_type' => 'issue_return',
                    'transaction_id' => $batch->id,
                    'quantity' => $batchItem->quantity,
                    'current_balance' => $newBalance,
                    'reference_no' => 'BATCH-' . $batch->id,
                    'transaction_date' => now()->toDateString(),
                    'description' => 'Items returned from batch',
                ]);
            }

            $batch->issue_status = 'R'; // Returned
            // $batch->return_date = date('Y-m-d'); // Set return date to today
            $batch->save();

            DB::commit();

            return redirect()->route('item-issue-list')->with('success', 'Batch returned successfully.');
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

    // public function getStudentsByCourse(Request $request, $courseId)
    // {
    //     try {
    //         $course_id = $courseId; // Use the route parameter
    //         $students = Student::whereHas('courses', function($query) use ($course_id) {
    //             $query->where('course_id', $course_id);
    //         })->get();

    //         return response()->json($students);
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
    //     }
    // }

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

    public function getStudentFeeData(Request $request)
    {
        try {
            $student_id = $request->student_id;
            $branch_id = $request->branch_id;
            $intake_id = $request->intake_id;
            $course_id = $request->course_id;
    
            Log::info('=== getStudentFeeData CALLED ===', [
                'student_id' => $student_id,
                'branch_id' => $branch_id,
                'intake_id' => $intake_id,
                'course_id' => $course_id
            ]);
    
            // Basic student validation
            $student = Student::find($student_id);
            if (!$student) {
                return response()->json([
                    'student_name' => 'Student not found',
                    'total_invoices' => 0,
                    'total_fees' => 0,
                    'total_paid' => 0,
                    'total_due' => 0,
                    'fee_invoices' => []
                ]);
            }
    
            // Optional: Filter fee invoices based on branch/course/intake if your DB supports those relations
            $feeInvoices = FeeInvoice::where('student_id', $student_id)
                // ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
                // ->when($intake_id, fn($q) => $q->where('intake_id', $intake_id))
                // ->when($course_id, fn($q) => $q->where('course_id', $course_id))
                ->with(['invoice_item.feeType'])
                ->get();
    
            $totalFees = $feeInvoices->sum('total_amount');
            $totalPaid = $feeInvoices->sum('paid_amount');
            $totalDue = $totalFees - $totalPaid;
    
            $formattedInvoices = [];
            foreach ($feeInvoices as $invoice) {
                foreach ($invoice->invoice_item as $item) {
                    $formattedInvoices[] = [
                        'fee_type_name' => $item->feeType->name ?? 'N/A',
                        'amount' => $item->amount ?? 0,
                        'paid_amount' => $item->paid_amount ?? 0,
                    ];
                }
            }
    
            return response()->json([
                'student_name' => $student->student_name,
                'total_invoices' => $feeInvoices->count(),
                'total_fees' => $totalFees,
                'total_paid' => $totalPaid,
                'total_due' => $totalDue,
                'fee_invoices' => $formattedInvoices
            ]);
    
        } catch (Exception $e) {
            Log::error('Error in getStudentFeeData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch fee data',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getStudentDetails(Request $request, $studentId)
    {
        try {
            Log::info('=== getStudentDetails CALLED ===');
            Log::info('Student ID received: ' . $studentId);

            // Eager load branch (belongsTo), and the first associated intake and course (belongsToMany)
            $student = Student::with(['branch', 'intakes', 'courses'])->find($studentId);

            if (!$student) {
                Log::warning('Student not found for ID: ' . $studentId);
                return response()->json(['error' => 'Student not found'], 404);
            }

            Log::info('Student found: ' . $student->student_name);
            Log::info('Student Branch: ' . ($student->branch ? $student->branch->name : 'N/A'));
            Log::info('Student Intakes: ' . ($student->intakes->isNotEmpty() ? $student->intakes->pluck('name')->implode(', ') : 'N/A'));
            Log::info('Student Courses: ' . ($student->courses->isNotEmpty() ? $student->courses->pluck('name')->implode(', ') : 'N/A'));

            return response()->json([
                'branch' => $student->branch,
                'branch_id' => $student->branch ? $student->branch->id : null, // Add branch_id
                'intakes' => $student->intakes, // Return all intakes
                'courses' => $student->courses, // Return all courses
            ]);
        } catch (Exception $e) {
            Log::error('Error in getStudentDetails: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getCoursesByIntakeAndStudent(Request $request, $intakeId, $studentId)
    {
        try {
            Log::info('=== getCoursesByIntakeAndStudent CALLED ===');
            Log::info('Intake ID: ' . $intakeId . ', Student ID: ' . $studentId);

            $courses = Course::whereHas('intakes', function($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })
            ->whereHas('students', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->get();

            Log::info('Found courses count: ' . $courses->count());

            // Add course dates for expiration validation
            $coursesWithDates = $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'created_at' => $course->created_at ? $course->created_at->toISOString() : null,
                    'expiration_date' => $course->expiration_date,
                ];
            });

            Log::info('Courses with dates data: ' . json_encode($coursesWithDates->toArray()));

            return response()->json($coursesWithDates);
        } catch (Exception $e) {
            Log::error('Error in getCoursesByIntakeAndStudent: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Operation Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getBatchDetails(Request $request, $batchId)
    {
        try {
            Log::info('=== getBatchDetails CALLED ===');
            Log::info('Batch ID: ' . $batchId);

            // Get batch with all related items and their relationships
            $batch = ItemIssueBatch::with([
                'items' => function($query) {
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
}

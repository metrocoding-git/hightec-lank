<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Content;
use App\CheckSection;
use App\Models\Course;
use App\Models\Intake;
use App\Models\SmToDo;
use App\Models\SmClass;
use App\Models\SmEvent;
use App\Models\Student;
use App\Models\SmSchool;
use App\Models\Supplier;
use App\Models\ReturnFee;
use App\Models\FeeInvoice;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\VideoList;
use App\Models\YearCheck;
use App\InfixModuleManager;
use App\Models\BankAccount;
use App\Models\ItemReceive;
use App\Models\ItemInventory;
use App\Models\SmAddIncome;
use Illuminate\Support\Str;
use App\Models\SmAddExpense;
use Illuminate\Http\Request;
use App\Models\SmNoticeBoard;
use App\Models\GlobalVariable;
use App\Models\Inventory_item;
use App\Models\SmAcademicYear;
use App\Models\SmClassSection;
use App\Models\ShareContentList;
use App\Models\SmCalendarSetting;
use App\Models\SmGeneralSettings;
use Illuminate\Support\Facades\DB;
use App\Models\DueFeesLoginPrevent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Modules\Saas\Entities\SmPackagePlan;
use Modules\RolePermission\Entities\InfixRole;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Saas\Entities\SmSubscriptionPayment;
use App\Models\Branch; // Keep this single import for Branch

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function login(){
        return view('auth.login');
    }

    // for display dashboard
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = User::whereHas('role', function($q) { $q->where('role_name', 'Teacher'); })->count();
        $labourCount = User::whereHas('role', function($q) { $q->where('role_name', 'Labour'); })->count();
        $adminCount = User::whereHas('role', function($q) { $q->where('role_name', 'Admin'); })->count();

        // Fetch financial data using existing models
        $totalIncome = FeeInvoice::sum('paid_amount');
        $totalExpenses = 0; // No expense model, set to 0
        $totalProfit = $totalIncome - $totalExpenses;
        $totalRevenue = $totalIncome;

        // Fetch notices using Content model
        $notices = Content::latest()->take(5)->get();

        // Fetch events using Course model
        $events = Course::all();

        // Fetch branches with intakes and courses
        $branches = Branch::with(['intakes.courses'])->get();

        // Fetch todos - using a simple array for now
        $todos = []; // Or create a model later

        return view('pages.dashboard.dashboard', compact(
            'user',
            'studentsCount',
            'teachersCount',
            'labourCount',
            'adminCount',
            'totalIncome',
            'totalExpenses',
            'totalProfit',
            'totalRevenue',
            'notices',
            'events',
            'branches',
            'todos'
        ));
    }




    // public function subjects()
    // {
    //     return view('pages.administration.academic.subject');
    // }




    public function uploadContent()
    {
        return view('pages.administration.study-material.upload-content');
    }

    public function studyMaterialDownload()
    {
        return view('pages.administration.study-material.study-material-download');
    }

    public function otherDownload()
    {
        return view('pages.administration.study-material.other-download');
    }

    public function lesson()
    {
        return view('pages.administration.lesson-plan.lesson');
    }

    public function topic()
    {
        return view('pages.administration.lesson-plan.topic');
    }

    public function topicOverview()
    {
        return view('pages.administration.lesson-plan.topic-overview');
    }


    public function contentType()
    {
        return view('pages.administration.download-center.content-type');
    }
    public function contentList()
    {
        return view('pages.administration.download-center.content-list');
    }
    public function sharedContentList()
    {
        $sharedContents = ShareContentList::with(['role', 'users'])
            ->orderBy('id')
            // ->paginate(10)
            ->get();

        return view('pages.administration.download-center.shared-content-list', compact('sharedContents'));
    }

    public function videoList(Request $request)
    {
        $intakes = Intake::all();
        $videosQuery = VideoList::with(['intake:id,name', 'courses:id,name'])->orderByDesc('id');

        if ($request->filled('title')) {
            $title = $request->input('title');
            $videosQuery->where('title', 'like', "%{$title}%");
        }

        $videos = $videosQuery->get();
        return view('pages.administration.download-center.video-list', compact('intakes', 'videos'));
    }

    public function videoListStore(Request $request)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            // Create VideoList entry
            $video = VideoList::create([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            // Attach related courses
            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video saved successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function videoListDestroy($id)
    {
        $video = VideoList::findOrFail($id);
        $video->delete();
        return redirect()->route('video-list')->with('success', 'Video deleted successfully');
    }

    /**
     * Update an existing video and sync its related courses.
     * Handles AJAX PUT from the dedicated Edit Video modal.
     */
    public function videoListUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'intake_id' => 'required|exists:intakes,id',
            'title' => 'required|string|max:255',
            'video_link' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'integer|exists:courses,id',
        ]);

        try {
            $video = VideoList::findOrFail($id);
            $video->update([
                'intake_id' => $validated['intake_id'],
                'title' => $validated['title'],
                'video_link' => $validated['video_link'],
                'description' => $validated['description'] ?? null,
            ]);

            $video->courses()->sync($validated['course_ids']);

            return response()->json([
                'status' => true,
                'message' => 'Video updated successfully',
                'id' => $video->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function studentCategory()
    {
        return view('pages.student.student-info.student-category');
    }

    public function addStudent()
    {
        return view('pages.student.student-info.add-student');
    }

    public function studentList()
    {
        $students = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
                    'id',
                    'admission_number',
                    'branch_id',
                    'student_category_id',
                    'gender_id',
                    'student_name',
                    'date_of_birth',
                    'phone_number',
                ])->active()->latest()->get();
        $branches = Branch::all();

        return view('pages.student.student-info.student-list', compact('students', 'branches'));
    }
    // App\Http\Controllers\StudentController.php

    public function getIntakes($branchId)
    {
        $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);
        return response()->json($intakes);
    }



    // Get courses for an intake
    public function getCourses($intakeId)
    {
        $intake = Intake::find($intakeId);

        if (!$intake) {
            return response()->json([]);
        }

        $courses = $intake->courses()->get(['courses.id', 'courses.name']); // related courses
        return response()->json($courses);
    }

    public function getCoursesByIntake($intakeId)
    {
        try {
            $intake = Intake::find($intakeId);

            if (!$intake) {
                return response()->json([
                    'success' => false,
                    'error' => 'Intake not found',
                    'intake_id' => $intakeId
                ], 404);
            }

            $courses = $intake->courses()->get(['courses.id', 'courses.name']);

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

    public function getIntakesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'intakes' => $intakes,
                'branch_id' => $branchId,
                'count' => $intakes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getCoursesByBranch($branchId)
    {
        try {
            $intakes = Intake::where('branch_id', $branchId)->pluck('id');
            $courses = Course::whereHas('intakes', function($q) use ($intakes) {
                $q->whereIn('intake_id', $intakes);
            })->get(['id', 'name', 'price']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'branch_id' => $branchId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'branch_id' => $branchId
            ], 500);
        }
    }

    public function getCoursesByStudent($studentId)
    {
        try {
            $student = Student::find($studentId);
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'error' => 'Student not found',
                    'student_id' => $studentId
                ], 404);
            }

            $courses = $student->courses()->get(['courses.id', 'courses.name', 'courses.price']);

            return response()->json([
                'success' => true,
                'courses' => $courses,
                'student_id' => $studentId,
                'count' => $courses->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'student_id' => $studentId
            ], 500);
        }
    }
    public function filterStudentList(Request $request)
    {

        $branchId = $request->input('branch_id');
        $intakeId = $request->input('intake_id');
        $courseId = $request->input('course_id');


        $query = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
                    'id',
                    'admission_number',
                    'branch_id',
                    'student_category_id',
                    'gender_id',
                    'student_name',
                    'date_of_birth',
                    'phone_number',
                ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($intakeId) {

            $query->whereHas('intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($courseId) {
            // join with courses via course_intake
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }





        $branches = Branch::all();

        $students = $query->active()->orderBy('id', 'desc')->get();



        return view('pages.student.student-info.student-list', compact('students', 'branches'));
    }



    public function filter_disabled_StudentList(Request $request)
    {

        $branchId = $request->input('branch_id');
        $intakeId = $request->input('intake_id');
        $courseId = $request->input('course_id');


        $query = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
                    'id',
                    'admission_number',
                    'branch_id',
                    'student_category_id',
                    'gender_id',
                    'student_name',
                    'date_of_birth',
                    'phone_number',
                ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($intakeId) {

            $query->whereHas('intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($courseId) {
            // join with courses via course_intake
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }





        $branches = Branch::all();

        $students = $query->inactive()->orderBy('id', 'desc')->get();



        return view('pages.student.student-info.disabled-students', compact('students', 'branches'));
    }



    public function updateStudetStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        $student->update([
            'is_active' => !$student->is_active
        ]);

        return redirect()->back()->with('message-success', 'Student status update successfully!');
    }

    public function studentView($id)
    {
        return view('pages.student.student-info.student-view', compact('id'));
    }

    public function disabledStudents()
    {
        $students = $students = Student::with([
            'branch',
            'gender',
            'studentCategory',
        ])->select([
                    'id',
                    'admission_number',
                    'branch_id',
                    'student_category_id',
                    'gender_id',
                    'student_name',
                    'date_of_birth',
                    'phone_number',
                ])->inactive()->orderBy('id', 'desc')->get();

        $branches = Branch::all();

        return view('pages.student.student-info.disabled-students', compact('students', 'branches'));
    }
    public function studentAttendance()
    {
        $courses = Course::all();
        $intakes = Intake::all();
        return view('pages.student.student-info.student-attendance', compact('courses', 'intakes'));
    }
    public function studentExport()
    {
        return view('pages.student.student-info.student-export');
    }



    public function feeGroup()
    {
    }




    public function feeType()
    {
        return view('pages.student.fees.fee-type');
    }

    // public function feeInvoice()
    // {
    //     return view('pages.student.fees.fee-invoice');
    // }

    // public function addInvoice()
    // {
    //     return view('pages.student.fees.add-invoice');
    // }

    public function feeInvoiceSetting()
    {
        return view('pages.student.fees.fee-invoice-setting');
    }

    public function feeReturn(Request $request)
    {
        $branches = Branch::all();
        $coursess = Course::all();
        $intakes = Intake::all();
        
        $query = ReturnFee::with(['student', 'role']);

        if ($request->filled('branch')) {
            $branchId = $request->input('branch');
            $query->whereHas('student', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        if ($request->filled('intake')) {
            $intakeId = $request->input('intake');
            $query->whereHas('student.intakes', function ($q) use ($intakeId) {
                $q->where('intakes.id', $intakeId);
            });
        }

        if ($request->filled('course')) {
            $courseId = $request->input('course');
            $query->whereHas('student.courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        $returnDetails = $query->orderBy('created_at', 'desc')->get();

        $branch_id = $request->input('branch');
        $intake_id = $request->input('intake');
        $course_id = $request->input('course');

        return view('pages.student.fees.fee-return', compact('returnDetails', 'coursess', 'intakes', 'branches', 'branch_id', 'intake_id', 'course_id'));
    }

    public function feeInvoice()
    {
        $roles = Role::all();
        $fee_invoices = FeeInvoice::with(['student', 'branch', 'invoice_item.fee_type'])
            ->orderBy('created_at', 'desc')
            ->paginate(perPage: 15);

        return view('pages.student.fees.fee-invoice', compact('fee_invoices', 'roles'));
    }



    public function itemCategory()
    {
        return view('pages.account.inventory.item-category');
    }


    public function itemList()
    {
        return view('pages.account.inventory.item-list');
    }

    public function supplier()
    {
        return view('pages.account.inventory.supplier');
    }

    public function itemReceive()
    {
        $items = Inventory_item::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        // $account_id = BankAccount::all();

        return view('pages.account.inventory.item-receive', compact(
            'items',
            'suppliers',
            'branches',
            // 'account_id'
        ));
    }

    public function itemReceivePost(Request $request)
    {
        // Validate incoming data; arrays for item lines
        $request->validate([
            'payment_method' => 'required|string',
            'supplier_id' => 'required|integer',
            'store_id' => 'required|integer',
            'receive_date' => 'required|string', // will parse m/d/Y -> Y-m-d
            'item_id' => 'required|array|min:1',
            'item_id.*' => 'required|integer',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'invoice_number' => 'nullable|string|max:255',
            'bank_payment_amount' => 'nullable|numeric',
            'bank_payment_date' => 'nullable|string',
            'bank_payment_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        // Conditional validation based on payment method
        if ($request->payment_method === 'Bank Transfer') {
            $request->validate([
                'bank_payment_amount' => 'required|numeric|min:0.01',
                'bank_payment_date' => 'required|date',
            ]);

            $subTotal = array_sum(array_map(function($price, $qty) {
                return (float)$price * (float)$qty;
            }, $request->unit_price, $request->quantity));

            if (($request->bank_payment_amount ?? 0) <= $subTotal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank Payment Amount must be greater than the Item Receive Total!',
                ], 422);
            }
        }

        try {
            // Parse receive_date from the UI (YYYY-MM-DD) to database format (Y-m-d)
            $receiveDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->receive_date)->format('Y-m-d');

            // Generate a unique transaction ID
            $transactionId = 'TRX-' . time() . '-' . rand(1000, 9999);

            // Handle file upload for bank_payment_document
            $bankPaymentDocumentPath = null;
            if ($request->hasFile('bank_payment_document')) {
                $bankPaymentDocumentPath = $request->file('bank_payment_document')->store('bank_documents', 'public');
            }

            // Create one row per item, duplicating header fields as per existing schema
            $createdReceives = [];
            foreach ($request->item_id as $index => $itemId) {
                $lineTotal = (float) $request->unit_price[$index] * (float) $request->quantity[$index];

                $totalPaidAmount = 0;
                if ($request->payment_method === 'Bank Transfer') {
                    $totalPaidAmount = $request->bank_payment_amount ?? 0;
                } else {
                    $totalPaidAmount = $request->totalPaid ?? 0;
                }

                $bankPaymentDate = null;
                if ($request->payment_method === 'Bank Transfer' && $request->bank_payment_date) {
                    $bankPaymentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->bank_payment_date)->format('Y-m-d');
                }

                $itemReceive = ItemReceive::create([
                    'transaction_id' => $transactionId,
                    'payment_method' => $request->payment_method,
                    'bank_payment_amount' => $request->payment_method === 'Bank Transfer' ? $request->bank_payment_amount : null,
                    'bank_payment_date' => $bankPaymentDate,
                    'bank_payment_document' => $request->payment_method === 'Bank Transfer' ? $bankPaymentDocumentPath : null,
                    'supplier_id' => $request->supplier_id,
                    'store_id' => $request->store_id,
                    'reference_no' => $request->reference_no,
                    'receive_date' => $receiveDate,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'unit_price' => $request->unit_price[$index],
                    'quantity' => $request->quantity[$index],
                    'sub_total' => $lineTotal,
                    'total_paid' => $totalPaidAmount,
                ]);

                $createdReceives[] = $itemReceive;

                // Track inventory changes for this item receive
                $currentBalance = ItemInventory::getCurrentBalance($itemId);
                $newBalance = $currentBalance + $request->quantity[$index];

                ItemInventory::create([
                    'item_id' => $itemId,
                    'transaction_type' => 'receive',
                    'transaction_id' => $itemReceive->id,
                    'quantity' => $request->quantity[$index],
                    'current_balance' => $newBalance,
                    'reference_no' => $request->reference_no,
                    'transaction_date' => $receiveDate,
                    'description' => $request->description ?? 'Item received from supplier',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item received successfully!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save item receive.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function itemReceivesaveInvoiceNumber(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:item_receive,id',
            'invoice_number' => 'required|string|max:50',
        ]);
    
        $ItemReceive = ItemReceive::findOrFail($request->id);
        $ItemReceive->invoice_number = $request->invoice_number;
        $ItemReceive->save();
    
    }
    public function itemReceiveList()
    {
        // Group by transaction_id and get the first record for each transaction
        $transactions = ItemReceive::with(['supplier', 'branch', 'item'])
            ->select('transaction_id', 'supplier_id', 'store_id', 'reference_no', 'receive_date', 'payment_method', 'total_paid', 'description')
            ->distinct()
            ->get()
            ->groupBy('transaction_id');

        // For each transaction, get all items
        $itemReceives = [];
        foreach ($transactions as $transactionId => $records) {
            $record = $records->first();
            $items = ItemReceive::where('transaction_id', $transactionId)->with(['item', 'supplier', 'branch'])->get();
            $record->items = $items;
            $record->total_quantity = $items->sum('quantity');
            $record->total_sub_total = $items->sum('sub_total');
            $record->total_balance = $record->total_sub_total - $record->total_paid;
            $itemReceives[] = $record;
        }

        $items = Inventory_item::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        return view('pages.account.inventory.item-receive-list', compact('itemReceives', 'items', 'suppliers', 'branches'));
    }

    public function viewItemReceive($transactionId)
    {
        try {
            $transaction = ItemReceive::where('transaction_id', $transactionId)->with(['supplier', 'branch', 'item'])->first();
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $items = ItemReceive::where('transaction_id', $transactionId)->with(['item', 'supplier', 'branch'])->get();

            // Calculate totals
            $totalQuantity = $items->sum('quantity');
            $totalSubTotal = $items->sum('sub_total');
            $totalPaid = $transaction->total_paid;
            $totalBalance = $totalSubTotal - $totalPaid;

            return response()->json([
                'transaction' => $transaction,
                'items' => $items,
                'totals' => [
                    'total_quantity' => $totalQuantity,
                    'total_sub_total' => $totalSubTotal,
                    'total_paid' => $totalPaid,
                    'total_balance' => $totalBalance,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch transaction details: ' . $e->getMessage()], 500);
        }
    }

    // public function editItemReceive($transactionId)
    // {
    //     try {
    //         $itemReceives = ItemReceive::where('transaction_id', $transactionId)->with('supplier', 'item', 'branch')->get();
    //         if ($itemReceives->isEmpty()) {
    //             return response()->json(['error' => 'Transaction not found'], 404);
    //         }

    //         $items = Inventory_item::all();
    //         $suppliers = Supplier::all();
    //         $branches = Branch::all();

    //         // Get the first record for common transaction details
    //         $transaction = $itemReceives->first();

    //         return response()->json([
    //             'transaction' => $transaction,
    //             'itemReceives' => $itemReceives,
    //             'items' => $items,
    //             'suppliers' => $suppliers,
    //             'branches' => $branches,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to load transaction for editing: ' . $e->getMessage()], 500);
    //     }
    // }

    // ==============================

    // public function updateItemReceive(Request $request, $transactionId)
    // {
    //     $request->validate([
    //         'payment_method' => 'required|string',
    //         'supplier_id' => 'required|integer',
    //         'store_id' => 'required|integer',
    //         'receive_date' => 'required|string',
    //         'item_id' => 'required|integer',
    //         'unit_price' => 'required|numeric',
    //         'quantity' => 'required|numeric',
    //     ]);

    //     try {
    //         $receiveDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->receive_date)->format('Y-m-d');
    //     } catch (\Throwable $e) {
    //         try {
    //             $receiveDate = \Carbon\Carbon::parse($request->receive_date)->format('Y-m-d');
    //         } catch (\Throwable $e2) {
    //             return response()->json(['error' => 'Invalid receive date format.'], 422);
    //         }
    //     }

    //     try {
    //         // Update all items in the transaction with common fields
    //         ItemReceive::where('transaction_id', $transactionId)->update([
    //             'payment_method' => $request->payment_method,
    //             'supplier_id' => $request->supplier_id,
    //             'store_id' => $request->store_id,
    //             'reference_no' => $request->reference_no,
    //             'receive_date' => $receiveDate,
    //             'description' => $request->description,
    //             'total_paid' => $request->totalPaid ?? 0,
    //             'bank_id' => $request->bank_id ?? null,
    //         ]);

    //         // Update the first item with specific item details
    //         $firstItem = ItemReceive::where('transaction_id', $transactionId)->first();
    //         if ($firstItem) {
    //             $firstItem->update([
    //                 'item_id' => $request->item_id,
    //                 'unit_price' => $request->unit_price,
    //                 'quantity' => $request->quantity,
    //                 'sub_total' => (float) $request->unit_price * (float) $request->quantity,
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Transaction updated successfully! Note: Only the first item details were updated. For complex multi-item edits, consider deleting and recreating the transaction.',
    //         ]);
    //     } catch (\Throwable $e) {
    //         return response()->json(['error' => 'Failed to update transaction: ' . $e->getMessage()], 500);
    //     }
    // }

    public function deleteItemReceive($transactionId)
    {
        try {
            $itemReceives = ItemReceive::where('transaction_id', $transactionId)->get();
            foreach ($itemReceives as $itemReceive) {
                $itemReceive->delete();
            }
            return response()->json([
                'status' => true,
                'message' => 'Item receive deleted successfully!',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete item receive: ' . $e->getMessage());
        }
    }

    // public function itemIssue()
    // {
    //     // itemname get using inventerory items table
    //     return view('pages.account.inventory.item-issue');
    // }
    public function itemSell()
    {
        $items = Inventory_item::all();
        $classes = Course::all();
        $sections = Intake::all();
        $roles = Role::all();
        $students = Student::all();
        $users = User::all();
        return view('pages.account.inventory.item-sell', compact('items', 'classes', 'sections', 'roles', 'students', 'users'));
    }

   
}

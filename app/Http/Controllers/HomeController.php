<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\CheckSection;
use App\Models\Course;
use App\Models\Intake;
use App\Models\SmToDo;
use App\Models\SmClass;
use App\Models\SmEvent;
use App\Models\Student;
use App\Models\SmSchool;
use App\Models\Supplier;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\VideoList;
use App\Models\YearCheck;
use App\InfixModuleManager;
use App\Models\BankAccount;
use App\Models\ItemReceive;
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
use Flasher\Toastr\Laravel\Facade\Toastr;
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


    // for display dashboard
    public function dashboard()
    {
        return view('pages.dashboard.dashboard');
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
                ])->active()->orderBy('id', 'desc')->get();
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
        return view('pages.student.student-info.student-attendance');
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

    public function feeReturn()
    {
        return view('pages.student.fees.fee-return');
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
        ]);

        try {
            // Parse date from the UI (m/d/Y) to database format (Y-m-d)
            $receiveDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->receive_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            // Fallback: try to parse a standard Y-m-d or Y/m/d if user typed differently
            try {
                $receiveDate = \Carbon\Carbon::parse($request->receive_date)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid receive date format.',
                ], 422);
            }
        }

        try {
            // Create one row per item, duplicating header fields as per existing schema
            foreach ($request->item_id as $index => $itemId) {
                ItemReceive::create([
                    'payment_method' => $request->payment_method,
                    'supplier_id' => $request->supplier_id,
                    'store_id' => $request->store_id,
                    'reference_no' => $request->reference_no,
                    'receive_date' => $receiveDate,
                    'description' => $request->description,
                    'item_id' => $itemId,
                    'unit_price' => $request->unit_price[$index],
                    'quantity' => $request->quantity[$index],
                    // Map line total to existing column name 'sub_total'
                    'sub_total' => $request->total[$index] ?? ((float) $request->unit_price[$index] * (float) $request->quantity[$index]),
                    // Store overall paid if provided; schema has 'total_paid'
                    'total_paid' => $request->totalPaid ?? 0,
                    // Optional bank id if payment_method requires it
                    'bank_id' => $request->bank_id ?? null,
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

    public function itemReceiveList()
    {
        $itemReceives = ItemReceive::with('supplier')->get(); // Eager load supplier for company_name
        $items = Inventory_item::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        return view('pages.account.inventory.item-receive-list', compact('itemReceives', 'items', 'suppliers', 'branches'));
    }

    public function editItemReceive($id)
    {
        $itemReceive = ItemReceive::with('supplier', 'item', 'branch')->findOrFail($id);
        $items = Inventory_item::all();
        $suppliers = Supplier::all();
        $branches = Branch::all();
        return response()->json([
            'itemReceive' => $itemReceive,
            'items' => $items,
            'suppliers' => $suppliers,
            'branches' => $branches,
        ]);
    }

    public function updateItemReceive(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'supplier_id' => 'required|integer',
            'store_id' => 'required|integer',
            'receive_date' => 'required|string',
            'item_id' => 'required|integer', // Assuming one item per receive record for simplicity based on current schema
            'unit_price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        try {
            $receiveDate = \Carbon\Carbon::createFromFormat('m/d/Y', $request->receive_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            try {
                $receiveDate = \Carbon\Carbon::parse($request->receive_date)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return redirect()->back()->with('error', 'Invalid receive date format.');
            }
        }

        try {
            $itemReceive = ItemReceive::findOrFail($id);
            $itemReceive->update([
                'payment_method' => $request->payment_method,
                'supplier_id' => $request->supplier_id,
                'store_id' => $request->store_id,
                'reference_no' => $request->reference_no,
                'receive_date' => $receiveDate,
                'description' => $request->description,
                'item_id' => $request->item_id,
                'unit_price' => $request->unit_price,
                'quantity' => $request->quantity,
                'sub_total' => (float) $request->unit_price * (float) $request->quantity,
                'total_paid' => $request->totalPaid ?? 0,
                'bank_id' => $request->bank_id ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Item receive updated successfully!',
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to update item receive: ' . $e->getMessage());
        }
    }

    public function deleteItemReceive($id)
    {
        try {
            $itemReceive = ItemReceive::findOrFail($id);
            $itemReceive->delete();
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

    public function itemSellList()
    {
        return view('pages.account.inventory.item-sell-list');
    }
}

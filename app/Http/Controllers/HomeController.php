<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Inventory_item;
use App\Models\ItemReceive;
use App\Models\ItemStore;
use App\Models\Supplier;
use App\Models\User;
use App\Models\SmToDo;
use App\Models\SmClass;
use App\Models\SmEvent;
use App\Models\SmSchool;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\YearCheck;
use App\Models\Branch;
use App\Models\Intake;
use Carbon\Carbon;
use App\Models\SmAddIncome;
use App\CheckSection;
use App\Models\GlobalVariable;
use App\Models\SmAddExpense;
use App\Models\SmNoticeBoard;
use App\Models\SmAcademicYear;
use App\Models\SmClassSection;
use App\Models\SmGeneralSettings;
use App\InfixModuleManager;
use App\Models\Course;
use App\Models\DueFeesLoginPrevent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SmCalendarSetting;
use App\Models\Student;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Modules\Saas\Entities\SmPackagePlan;
use Modules\RolePermission\Entities\InfixRole;
use Modules\Wallet\Entities\WalletTransaction;
use Modules\Saas\Entities\SmSubscriptionPayment;
use App\Models\ShareContentList;

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
            
            ->get();

        return view('pages.administration.download-center.shared-content-list', compact('sharedContents'));
    }
    public function videoList()
    {
        return view('pages.administration.download-center.video-list');
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
         $request->validate([
        'payment_method' => 'required|string',
        'supplier_id' => 'required|integer',
        'store_id' => 'required|integer',
        'receive_date' => 'required|date',
        'item_id.*' => 'required|integer',
        'unit_price.*' => 'required|numeric',
        'quantity.*' => 'required|numeric',
    ]);

    // Create master record
    $receive = ItemReceive::create([
        'payment_method' => $request->payment_method,
        'supplier_id' => $request->supplier_id,
        'store_id' => $request->store_id,
        'reference_no' => $request->reference_no,
        'receive_date' => $request->receive_date,
        'description' => $request->description,
        'total' => $request->subTotal,
        'paid' => $request->totalPaid,
        'due' => $request->totalDueValue,
    ]);

    // Save items
    foreach ($request->item_id as $index => $itemId) {
        ItemReceive::create([
            'item_receive_id' => $receive->id,
            'item_id' => $itemId,
            'unit_price' => $request->unit_price[$index],
            'quantity' => $request->quantity[$index],
            'total' => $request->total[$index],
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Item received successfully!']);

    }


    public function itemReceiveList()
    {
        return view('pages.account.inventory.item-receive-list');
    }

    public function itemIssue()
    {
        return view('pages.account.inventory.item-issue');
    }
    public function itemSell()
    {
        return view('pages.account.inventory.item-sell');
    }

    public function itemSellList()
    {
        return view('pages.account.inventory.item-sell-list');
    }
}

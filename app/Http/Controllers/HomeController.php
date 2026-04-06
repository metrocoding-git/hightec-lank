<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
use App\Models\FeeInvoice;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\VideoList;
use App\Models\YearCheck;
use App\InfixModuleManager;
use App\Models\BankAccount;
use App\Models\SmAddIncome;
use Illuminate\Support\Str;
use App\Models\SmAddExpense;
use Illuminate\Http\Request;
use App\Models\SmNoticeBoard;
use App\Models\GlobalVariable;
use App\Models\InventoryItem;
use App\Models\SmAcademicYear;
use App\Models\SmClassSection;
use App\Models\ShareContentList;
use App\Models\SmCalendarSetting;
use App\Models\SmGeneralSettings;
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
use App\Models\FeeInvoices;
use App\Models\StudentVerification;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function login()
    {
        return view('auth.login');
    }

    // for display dashboard
    public function dashboard()
    {
        $user = Auth::user();

        // Fetch counts
        $studentsCount = Student::count();
        $teachersCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Teacher');
        })->count();
        $labourCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Labour');
        })->count();
        $adminCount = User::whereHas('role', function ($q) {
            $q->where('role_name', 'Admin');
        })->count();

        // Fetch financial data using existing models
        $totalIncome = FeeInvoices::sum('paid_amount');
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
    public function contentList()
    {
        return view('pages.administration.download-center.content-list');
    }

    // App\Http\Controllers\StudentController.php

    public function verificationHistory($id)
    {
        $verifications = StudentVerification::with('user')
            ->where('student_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($verifications);
    }


    // public function feeInvoice()
    // {
    //     return view('pages.student.fees.fee-invoice');
    // }

    // public function addInvoice()
    // {
    //     return view('pages.student.fees.add-invoice');
    // }
// public function invoiceDatatable()
// {
//     $query = DB::table('fee_invoices')
//         ->leftJoin('students', 'students.id', '=', 'fee_invoices.student_id')
//         ->leftJoin('student_categories', 'student_categories.id', '=', 'students.student_category_id')
//         ->leftJoin('branches', 'branches.id', '=', 'fee_invoices.branch_id')
//         ->leftJoin('invoice_items', 'invoice_items.invoice_id', '=', 'fee_invoices.id')
//         ->leftJoin('fee_types', 'fee_types.id', '=', 'invoice_items.fee_type_id')
//         ->leftJoin('paid_courses', 'paid_courses.invoice_id', '=', 'fee_invoices.id')
//         ->leftJoin('courses', 'courses.id', '=', 'paid_courses.course_id')
//         ->where('fee_invoices.payment_status', '!=', 'returned')
//         ->select([
//             'fee_invoices.*',
//             'students.student_name',
//             'students.admission_number',
//             'student_categories.category_name',
//             'branches.name as branch_name',
//             'fee_types.name as fee_type_name',
//             'courses.name as course_name',
//         ]);

//     return DataTables::of($query)
//         ->addIndexColumn()

//         ->editColumn('fee_name', function ($row) {
//             return $row->invoice_type === 'fee'
//                 ? ($row->fee_type_name ?? '-')
//                 : ($row->course_name ?? '-');
//         })

//         ->addColumn('balance', function ($row) {
//             return number_format($row->total_amount - $row->paid_amount, 2);
//         })

//         ->addColumn('payment_date', fn($row) =>
//             \Carbon\Carbon::parse($row->create_date)->format('m/d/Y')
//         )

//         ->addColumn('due_date', fn($row) =>
//             \Carbon\Carbon::parse($row->due_date)->format('m/d/Y')
//         )

//         ->filter(function ($query) {
//             $keyword = request('search.value');

//             if ($keyword) {
//                 $query->where(function ($q) use ($keyword) {
//                     $q->where('fee_invoices.main_invoice_number', 'like', "%{$keyword}%")
//                       ->orWhere('students.student_name', 'like', "%{$keyword}%")
//                       ->orWhere('students.admission_number', 'like', "%{$keyword}%")
//                       ->orWhere('student_categories.category_name', 'like', "%{$keyword}%")
//                       ->orWhere('branches.name', 'like', "%{$keyword}%")
//                       ->orWhere('fee_types.name', 'like', "%{$keyword}%")
//                       ->orWhere('courses.name', 'like', "%{$keyword}%")
//                       ->orWhere('fee_invoices.payment_status', 'like', "%{$keyword}%");
//                 });
//             }
//         })

//         ->rawColumns(['payment_status', 'action'])
//         ->make(true);
// }

}

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



}

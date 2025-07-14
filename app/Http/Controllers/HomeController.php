<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SmToDo;
use App\Models\SmClass;
use App\Models\SmEvent;
use App\Models\SmStaff;
use App\Models\SmSchool;
use App\Models\SmHoliday;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmUserLog;
use App\Models\YearCheck;
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
use App\Models\DueFeesLoginPrevent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SmCalendarSetting;
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

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


    // for display dashboard
    public function index()
    {
        return view('auth.login');
    }
}
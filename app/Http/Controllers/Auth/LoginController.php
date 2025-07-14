<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\InfixModuleManager;
use App\Models\SmAcademicYear;
use App\Models\SmBackgroundSetting;
use App\Models\SmDateFormat;
use App\Models\SmGeneralSettings;
use App\Models\SmLanguage;
use App\Models\SmSchool;
use App\Models\SmStaff;
use App\Models\SmsTemplate;
use App\Models\SmStudent;
use App\Models\SmStyle;
use App\Models\SmUserLog;
use App\Models\User;
use App\Scopes\StatusAcademicSchoolScope;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use phpseclib3\System\SSH\Agent;
use Str;

class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }


    public function  index()
    {
        return view('auth.login');
    }


   
    
}

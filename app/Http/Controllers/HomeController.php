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
use App\Models\Course;
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
    public function dashboard()
    {
        return view('pages.dashboard.dashboard');
    }


    public function intake()
    {
        return view('pages.administration.academic.intake');
    }

    public function subjects()
    {
        return view('pages.administration.academic.subject');
    }

    public function courses()
    {
        $courses = Course::get();
        $title = 'All Active Courses';

        return view('pages.administration.academic.course', [
            'courses' => $courses
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Course::create($request->all());

        return redirect()->back()->with('message-success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:courses,code,' . $id,
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);
        $course->update($request->all());

        return redirect()->route('course.index')->with('message-success', 'Course updated successfully!');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $courses = Course::get();
        return view('pages.administration.academic.course', ['course' => $course, 'courses' => $courses, 'courseId' => $id,]);
    }


    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('course.index')->with('message-success-delete', 'Course deleted successfully.');
    }




    public function assignsubject()
    {
        return view('pages.administration.academic.assign-subject');
    }

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
        return view('pages.administration.download-center.shared-content-list');
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
        return view('pages.student.student-info.student-list');
    }

    public function studentView($id)
    {
        return view('pages.student.student-info.student-view', compact('id'));
    }




    public function disabledStudents()
    {
        return view('pages.student.student-info.disabled-students');
    }
    public function studentAttendance()
    {
        return view('pages.student.student-info.student-attendance');
    }
    public function studentExport()
    {
        return view('pages.student.student-info.student-export');
    }
}

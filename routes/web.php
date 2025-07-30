<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::controller(HomeController::class)->group(callback: function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');

    Route::get('administration/academic/intake', 'intake')->name('intake');
    Route::get('administration/academic/subjects', 'subjects')->name('subjects');
    Route::get('administration/academic/courses', 'courses')->name('courses');
    Route::get('administration/academic/assign-subject', 'assignSubject')->name('"assign-subject');


    Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
    Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download  ');
    Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');



    
    Route::get('administration/lesson-plan/lesson', 'lesson')->name('lesson');
    Route::get('administration/lesson-plan/topic', 'topic')->name('topic');
    Route::get('administration/lesson-plan/topic-overview', 'topicOverview')->name('topic-overview');


    Route::get('student/student-category', 'studentCategory')->name('student-category');
    Route::get('student/add-student', 'addStudent')->name('add-student');
    Route::get('disabled-students', 'disabledStudents')->name('disabled-students');
    Route::get('student-attendance', 'student-Attendance')->name('student-attendance');
    Route::get('student-export', 'studentExport')->name('student-export');


    Route::get('fee-group', 'feeGroup')->name('fee-group');
    Route::get('fee-type', 'feeType')->name('fee-type');
    Route::get('fee-invoice', 'feeInvoice')->name('fee-invoice');
    Route::get('add-invoice', 'addInvoice')->name('add-invoice');
    Route::get('fee-invoice-setting', 'feeInvoiceSetting')->name('fee-invoice-setting');
    Route::get('fee-return', 'feeReturn')->name('fee-return');


    Route::get('item-category', 'itemCategory')->name('item-category');
    Route::get('item-list', 'itemList')->name('item-list');
    Route::get('supplier', 'supplier')->name('supplier');
    Route::get('item-receive', 'itemReceive')->name('item-receive');
    Route::get('item-receive-list', 'itemReceiveList')->name('item-receive-list');
    Route::get('item-issue', 'itemIssue')->name('item-issue');
    Route::get('item-sell', 'itemSell')->name('item-sell');





});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::get('recoveryPassword', 'recoveryPassword')->name('recoveryPassword');
    Route::get('register', 'register')->name('register');
});

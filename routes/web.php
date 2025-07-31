<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::controller(HomeController::class)->group(callback: function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');


    Route::get('administration/academic/courses', 'courses')->name('course.index');
    Route::post('administration/academic/courses/store', 'store')->name('course.store');
    Route::post('administration/academic/courses/update/{id}', 'update')->name('course.update');
    Route::get('administration/academic/courses/edit/{id}', 'edit')->name('course.edit');
    Route::delete('/course/destroy/{id}', 'destroy')->name('course.destroy');



    Route::get('administration/academic/intake', 'intake')->name('intake');
    Route::get('administration/academic/subjects', 'subjects')->name('subjects');

    Route::get('administration/academic/assign-subject', 'assignSubject')->name('"assign-subject');


    Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
    Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download  ');
    Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');




    Route::get('administration/lesson-plan/lesson', 'lesson')->name('lesson');
    Route::get('administration/lesson-plan/topic', 'topic')->name('topic');
    Route::get('administration/lesson-plan/topic-overview', 'topicOverview')->name('topic-overview');



    Route::get('administration/download-center/content-type', 'contentType')->name('content-type');
    Route::get('administration/download-center/content-list', 'contentList')->name('content-list');
    Route::get('administration/download-center/shared-content-list', 'sharedContentList')->name('shared-content-list');
    Route::get('administration/download-center/video-list', 'videoList')->name('video-list');




    Route::get('student/student-info/student-category', 'studentCategory')->name('student-category');
    Route::get('student/student-info/add-student', 'addStudent')->name('add-student');
    Route::get('student/student-info/student-list', 'studentList')->name('student-list');
    Route::get('student/student-info/student-view/{id}', 'studentView')->name('student-details');

    Route::get('student/student-info/disabled-student', 'disabledStudents')->name('disabled-students');
    Route::get('student/student-info/student-attendance', 'studentAttendance')->name('student-attendance');
    Route::get('student/student-info/student-export', 'studentExport')->name('student-export');


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

<?php

use App\Http\Controllers\AddStudentController;
use App\Http\Controllers\AssignSubjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StudentCategoryController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TopicOverviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::controller(CourseController::class)->prefix('administration/academic/courses')->group(callback: function () {
    Route::get('/', 'index')->name('course.index');
    Route::post('store', 'store')->name('course.store');
    Route::post('update/{id}', 'update')->name('course.update');
    Route::get('edit/{id}', 'edit')->name('course.edit');
    Route::delete('destroy/{id}', 'destroy')->name('course.destroy');
});

Route::controller(IntakeController::class)->prefix('administration/academic/intake')->group(callback: function () {
    Route::get('/', 'index')->name('intake.index');
    Route::post('store', 'store')->name('intake.store');
    Route::post('update/{id}', 'update')->name(name: 'intake.update');
    Route::get('edit/{id}', 'edit')->name('intake.edit');
    Route::delete('destroy/{id}', 'destroy')->name('intake.destroy');
});

Route::controller(SubjectController::class)->prefix('administration/academic/subjects')->group(callback: function () {
    Route::get('/', 'index')->name('subject.index');
    Route::post('store', 'store')->name('subject.store');
    Route::post('update/{id}', 'update')->name('subject.update');
    Route::get('edit/{id}', 'edit')->name('subject.edit');
    Route::delete('destroy/{id}', 'destroy')->name('subject.destroy');
});

Route::controller(AssignSubjectController::class)->prefix('administration/academic/assign-subject')->group(callback: function () {
    Route::get('/', 'index')->name('assign-subject.index');
    Route::post('store', 'store')->name('assign-subject.store');
    Route::post('update/{id}', 'update')->name('assign-subject.update');
    Route::get('edit/{id}', 'edit')->name('assign-subject.edit');
    Route::delete('destroy/{id}', 'destroy')->name('assign-subject.destroy');
});

Route::controller(LessonController::class)->prefix('administration/lesson-plan/lesson')->group(callback: function () {
    Route::get('/', 'index')->name('lesson.index');
    Route::post('store', 'store')->name('lesson.store');
    Route::post('update/{id}', 'update')->name('lesson.update');
    Route::get('edit/{id}', 'edit')->name('lesson.edit');
    Route::delete('destroy/{id}', 'destroy')->name('lesson.destroy');
});

Route::controller(TopicController::class)->prefix('administration/lesson-plan/topic')->group(callback: function () {
    Route::get('/', 'index')->name('topic.index');
    Route::post('store', 'store')->name('topic.store');
    Route::put('update/{id}', 'update')->name('topic.update');
    Route::get('edit/{id}', 'edit')->name('topic.edit');
    Route::delete('destroy/{id}', 'destroy')->name('topic.destroy');
});

Route::controller(TopicOverviewController::class)->prefix('administration/lesson-plan/topic-overview')->group(callback: function () {
    Route::get('/', 'index')->name('topic-overview.index');
    Route::post('store', 'store')->name('topic-overview.store');
});

Route::controller(StudentCategoryController::class)->prefix('student/student-info/student-category')->group(callback: function () {
    Route::get('/', 'index')->name('student-category.index');
    Route::post('store', 'store')->name('student-category.store');
    Route::put('update/{id}', 'update')->name('student-category.update');
    Route::get('edit/{id}', 'edit')->name('student-category.edit');
    Route::delete('destroy/{id}', 'destroy')->name('student-category.destroy');
});

Route::controller(AddStudentController::class)->prefix('student/student-info/add-student')->group(callback: function () {
    Route::get('/', 'index')->name('add-student.index');
    Route::post('store', 'store')->name('add-student.store');
    Route::put('update/{id}', 'update')->name('add-student.update');
    Route::get('edit/{id}', 'edit')->name('add-student.edit');
    Route::delete('destroy/{id}', 'destroy')->name('add-student.destroy');
});

Route::controller(HomeController::class)->group(callback: function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');

    Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
    Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download  ');
    Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');






    Route::get('administration/download-center/content-type', 'contentType')->name('content-type');
    Route::get('administration/download-center/content-list', 'contentList')->name('content-list');
    Route::get('administration/download-center/shared-content-list', 'sharedContentList')->name('shared-content-list');
    Route::get('administration/download-center/video-list', 'videoList')->name('video-list');




 
    // Route::get('student/student-info/add-student', 'addStudent')->name('add-student');
    Route::get('student/student-info/student-list', 'studentList')->name('student-list');
    Route::patch('student/update-status', 'updateStudetStatus')->name('student.update-status');
    Route::get('student/student-info/student-view/{id}', 'studentView')->name('student-details');

    Route::get('student/student-info/disabled-student', 'disabledStudents')->name('disabled-students');
    Route::get('student/student-info/student-attendance', 'studentAttendance')->name('student-attendance');
    Route::get('student/student-info/student-export', 'studentExport')->name('student-export');


    Route::get('student/fees/fee-group', 'feeGroup')->name('fee-group');
    Route::get('student/fees/fee-type', 'feeType')->name('fee-type');
    Route::get('student/fees/fee-invoice', 'feeInvoice')->name('fee-invoice');
    Route::get('student/fees/add-invoice', 'addInvoice')->name('add-invoice');
    Route::get('student/fees/fee-invoice-setting', 'feeInvoiceSetting')->name('fee-invoice-setting');
    Route::get('student/fees/fee-return', 'feeReturn')->name('fee-return');

    Route::get('account/inventory/item-category', 'itemCategory')->name('item-category');
    Route::get('account/inventory/item-list', 'itemList')->name('item-list');
    Route::get('account/inventory/supplier', 'supplier')->name('supplier');
    Route::get('account/inventory/item-receive', 'itemReceive')->name('item-receive');
    Route::get('account/inventory/item-receive-list', 'itemReceiveList')->name('item-receive-list');
    Route::get('account/inventory/item-issue', 'itemIssue')->name('item-issue');
    Route::get('account/inventory/item-sell', 'itemSell')->name('item-sell');
    Route::get('account/inventory/item-sell-list', 'itemSellList')->name('item-sell-list');
});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::get('recoveryPassword', 'recoveryPassword')->name('recoveryPassword');
    Route::get('register', 'register')->name('register');
});

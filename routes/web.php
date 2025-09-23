<?php

use App\Models\Supplier;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ItemSellController; // Import the new controller
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FeesGroupController;
use App\Http\Controllers\ItemIssueController;
use App\Http\Controllers\AddStudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\AssignSubjectController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\TopicOverviewController;
use App\Http\Controllers\StudentCategoryController;
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
    Route::put('update/{student}', 'update')->name('add-student.update');
    Route::get('edit/{student}', 'edit')->name('add-student.edit');
    Route::delete('destroy/{id}', 'destroy')->name('add-student.destroy');
});

Route::controller(HomeController::class)->group(callback: function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');

    Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
    Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download');
    Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');






    // Route::get('administration/download-center/content-type', 'contentType')->name('content-type');
    // Route::get('administration/download-center/content-list', 'contentList')->name('content-list');
    Route::get('administration/download-center/shared-content-list', 'sharedContentList')->name('shared-content-list');
    Route::get('administration/download-center/video-list', 'videoList')->name('video-list');
    Route::post('administration/download-center/video-list-save', 'videoListStore')->name('download-center.video-list-save');
    Route::put('administration/download-center/video-list-update/{id}', 'videoListUpdate')->name('download-center.video-list-update');
    Route::delete('administration/download-center/video-list-destroy/{id}', 'videoListDestroy')->name('download-center.video-list-destroy');
    // Optionally, add edit/update later
    // Route::get('administration/download-center/video-list-edit/{id}', 'videoListEdit')->name('download-center.video-list-edit');
    // Route::get('student/student-info/add-student', 'addStudent')->name('add-student');
    Route::get('student/student-info/student-list', 'studentList')->name('student-list');
    Route::patch('student/update-status', 'updateStudetStatus')->name('student.update-status');
    Route::get('student/student-info/student-view/{id}', 'studentView')->name('student-details');

    Route::get('student/student-info/disabled-student', 'disabledStudents')->name('disabled-students');
    Route::get('student/student-info/student-attendance', 'studentAttendance')->name('student-attendance');
    Route::get('student/student-info/student-export', 'studentExport')->name('student-export');


    //  New dependent dropdown AJAX routes 

    Route::get('student/student-info/get-intakes/{branchId}', 'getIntakes')->name('student.get-intakes');
    Route::get('student/student-info/get-courses/{intakeId}', 'getCourses')->name('student.get-courses');

    //filter student list by branch
    Route::post('student/student-info/diabled-filter', 'filter_disabled_StudentList')->name('frontend.disabled-student.search');
    Route::post('student/student-info/filtert', 'filterStudentList')->name('frontend.student.search');








    // Route::get('student/fees/fee-invoice', 'feeInvoice')->name('fee-invoice');
    // Route::get('student/fees/add-invoice', 'addInvoice')->name('add-invoice');
    Route::get('student/fees/fee-invoice-setting', 'feeInvoiceSetting')->name('fee-invoice-setting');
    Route::get('student/fees/fee-return', 'feeReturn')->name('fee-return');


    // Route::get('account/inventory/item-list', 'itemList')->name('item-list');
    // Route::get('account/inventory/supplier', 'supplier')->name('supplier');
    Route::get('account/inventory/item-receive', 'itemReceive')->name('item-receive');
    Route::get('account/inventory/item-receive-list', 'itemReceiveList')->name('item-receive-list');
    Route::get('account/inventory/item-receive/{id}/edit', [HomeController::class, 'editItemReceive'])->name('item-receive.edit');
    Route::put('account/inventory/item-receive/{id}', [HomeController::class, 'updateItemReceive'])->name('item-receive.update');
    Route::delete('account/inventory/item-receive/{id}', [HomeController::class, 'deleteItemReceive'])->name('item-receive.destroy');
    Route::post('/item-receive/save', [HomeController::class, 'itemReceivePost'])
        ->name('save-item-receive-data');
});

Route::controller(ItemSellController::class)->group(function () {
    Route::get('account/inventory/item-sell', 'itemSell')->name('item-sell');
    Route::post('account/inventory/item-sell/save', 'saveItemSellData')->name('save-item-sell-data');
    Route::get('account/inventory/item-sell-list', 'itemSellList')->name('item-sell-list');
    Route::get('account/inventory/item-sell/{id}', 'viewItemSell')->name('view-item-sell');
    Route::get('account/inventory/item-sell/{id}/edit', 'editItemSell')->name('edit-item-sell');
    Route::put('account/inventory/item-sell/{id}', 'updateItemSell')->name('update-item-sell');
    Route::get('account/inventory/item-sell/{id}/delete-view', 'deleteItemSellView')->name('delete-item-sell-view');
    Route::get('account/inventory/item-sell/{id}/add-payment', 'addPaymentSell')->name('add-payment-sell');
    Route::post('account/inventory/item-sell/{id}/payments', 'storeItemSellPayment')->name('item-sell-payments.store');
    Route::get('account/inventory/item-sell/{id}/view-payments', 'viewSellPayments')->name('view-sell-payments');
    Route::post('account/inventory/item-sell/{id}', 'deleteItemSell')->name('delete-item-sell');
    Route::get('account/inventory/item-sell/{id}/cancel-view', 'cancelItemSellView')->name('cancel-item-sell-view');
    Route::post('account/inventory/item-sell/{id}/cancel', 'cancelItemSell')->name('cancel-item-sell');
});

Route::controller(ItemIssueController::class)->group(function () {
    Route::get('account/inventory/item-issue', 'index')->name('item-issue');
    Route::post('account/inventory/save-item-issue-data', 'saveItemIssueData')->name('save-item-issue-data');
    Route::get('account/inventory/item-issue-edit/{id}', 'edit')->name('item-issue-edit');
    Route::put('account/inventory/item-issue-update/{id}', 'update')->name('item-issue-update');
    Route::get('account/inventory/return-item-view/{id}', 'returnItemView')->name('return-item-view');
    Route::post('account/inventory/return-item', 'returnItem')->name('return-item');
    Route::post('account/inventory/issue-item', 'IssueItem')->name('issue-item');

});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::get('recoveryPassword', 'recoveryPassword')->name('recoveryPassword');
    Route::get('register', 'register')->name('register');
});



Route::controller(FeesGroupController::class)->group(function () {
    Route::get('student/fees/fee-group', 'index')->name('fee-group'); // GET list
    Route::post('student/fees/fee-group-create', 'store')->name('fee-group-create'); // POST create
    Route::get('student/fees/fee-group-edit/{id}', 'edit')->name('fee-group-edit'); // GET edit
    Route::put('student/fees/fee-group-update/{id}', 'update')->name('fee-group-update'); // POST update
    Route::delete('student/fees/fee-group-delete/{id}', 'delete')->name('fee-group-delete'); // POST delete
        Route::get('student/fees/fee-group-edit/{id}', 'edit')->name('fee-group-edit'); // GET edit


});



Route::controller(FeeTypeController::class)->group(function () {

    Route::get('student/fees/fee-type', 'index')->name('fees.fees-type');
    Route::post('student/fees/fee-type-store', 'store')->name('fees.fees-type-store');
    Route::get('student/fees/fee-type-edit/{id}', 'edit')->name('fees.fees-type-edit');
    Route::put('student/fees/fee-type-update/{id}', 'update')->name('fees.fees-type-update');
    Route::delete('student/fees/fee-type-delete/{id}', 'destroy')->name('fees.fees-type-delete');
});

Route::controller(InvoiceController::class)->group(function () {

    Route::get('student/fees/invoice/view/{id}', 'edit')->name('fees.fee-invoice-view');
    Route::post('student/fees/invoice/{id}', 'addPayment')->name('fees.fees-invoice-payment');

    Route::get('student/fees/fee-invoice', 'index')->name('fees.fee-invoice');
    Route::get('student/fees/add-invoice', 'create')->name('fees.add-invoice');
    Route::get('student/invoice/get-student/{branchId}', 'show')->name('fees.get-student');
    Route::get('student/invoice/types_get_by_group/{groupId}', 'dropdown_fee_types')->name('fees.get-types-by-group');




    Route::post('student/fees/fee-store', 'store')->name('fees.invoice.store');
    // Route::get('student/fees/fee-edit/{id}', 'edit')->name('fees.fee-edit-invoice');
    Route::get('student/fees/fee-group-edit/{id}', 'edit')->name('fees.fee-invoice-edit'); // GET edit

    Route::put('student/fees/fee-update/{id}', 'update')->name('fees.fee-update-invoice');
    Route::delete('student/fees/fee-delete/{id}', 'destroy')->name('fees.fee-delete');
});




Route::controller(CategoryController::class)->group(function () {



    Route::get('account/inventory/item-category', 'index')->name('item-category');
    Route::post('account/inventory/item-category/store', 'store')->name('item-category-store');
    Route::get('account/inventory/item-category/edit/{id}', 'edit')->name('item-category-edit');
    Route::put('account/inventory/item-category/update/{id}', 'update')->name('item-category-update');
    Route::delete('account/inventory/item-category/delete/{id}', 'destroy')->name('delete-item-category');
});



Route::controller(InventoryItemController::class)->group(function () {
    Route::get('account/inventory/item-list', 'index')->name('item-list');
    Route::post('account/inventory/item-store', 'store')->name('item-store');
    Route::get('account/inventory/item-edit/{id}', 'edit')->name('item-edit');
    Route::put('account/inventory/item-update/{id}', 'update')->name('item-update');
    Route::delete('account/inventory/item-delete/{id}', 'destroy')->name('item-delete');
});

Route::controller(SupplierController::class)->group(function () {
    Route::get('account/inventory/supplier', 'index')->name('supplier');
    Route::post('account/inventory/supplier-store', 'store')->name('supplier-store');
    Route::get('account/inventory/supplier-edit/{id}', 'edit')->name('supplier-edit');
    Route::put('account/inventory/supplier-update/{id}', 'update')->name('supplier-update');
    Route::delete('account/inventory/supplier-delete/{id}', 'destroy')->name('supplier-delete');
});
Route::controller(ContentTypeController::class)->group(function () {
    Route::get('administration/download-center/content-type', 'index')->name('content-type');
    Route::post('account/content-type-store', 'store')->name('content-type-store');
    Route::get('account/content-type-edit/{id}', 'edit')->name('content-type-edit');
    Route::put('account/content-type-update/{id}', 'update')->name('content-type-update');
    Route::delete('account/content-type-delete/{id}', 'destroy')->name('content-type-delete');
});

Route::controller(ContentController::class)->group(function () {

    Route::get('administration/download-center/content-list', 'index')->name('content-list');
    Route::post('account/content-store', 'store')->name('content-store');
    Route::get('account/content-edit/{id}', 'edit')->name('content-edit');
    Route::put('account/content-update/{id}', 'update')->name('content-update');
    Route::delete('account/content-delete/{id}', 'destroy')->name('content-delete');
    // Save shared content list (Share modal)
    Route::post('administration/download-center/content-share-list-save', 'shareListSave')->name('content-share-list-save');
    // Generate URL for selected contents (Generate URL modal)
    Route::post('administration/download-center/content-generate-url-save', 'generateUrlSave')->name('download-center.content-generate-url-save');
    // Dependent dropdown: get courses for a given intake (used in Share/Generate URL modals)
    Route::get('administration/download-center/get-courses/{intakeId}', 'getCoursesByIntake')->name('download-center.get-courses-by-intake');
    // Save generated URL data
    Route::post('administration/download-center/save-generated-url-data', 'saveGeneratedUrlData')->name('download-center.save-generated-url-data');
    // View shared content details
    Route::get('administration/download-center/shared-content-view/{id}', 'viewSharedContent')->name('download-center.shared-content-view');
});

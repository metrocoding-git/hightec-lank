<?php

use App\Models\Supplier;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FeesGroupController;
use App\Http\Controllers\ItemIssueController;
use App\Http\Controllers\ReturnFeeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\AssignSubjectController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\TopicOverviewController;
use App\Http\Controllers\StudentCategoryController;
use App\Http\Controllers\ItemSellController; // Import the new controller
use App\Http\Controllers\ReportController; // Import the new controller
use App\Http\Controllers\RoleController; // Import the new controller
use App\Http\Controllers\UserController; // Import the new controller

Route::controller(HomeController::class)->middleware(['permission'])->group(callback: function () {
    Route::get('dashboard', 'dashboard')->name('dashboard');
    Route::get('/', 'login')->name('login');


    // Study Material
    Route::middleware(['permission'])->group(function () {
        Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
        Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download');
        Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');
    });

    // Download Center
    Route::middleware(['permission'])->group(function () {
        Route::get('administration/download-center/shared-content-list', 'sharedContentList')->name('shared-content-list');
        Route::get('administration/download-center/video-list', 'videoList')->name('video-list');
        Route::post('administration/download-center/video-list-save', 'videoListStore')->name('download-center.video-list-save');
        Route::put('administration/download-center/video-list-update/{id}', 'videoListUpdate')->name('download-center.video-list-update');
        Route::delete('administration/download-center/video-list-destroy/{id}', 'videoListDestroy')->name('download-center.video-list-destroy');
    });

    // Student Information Management (handled by HomeController)
    // Route::middleware(['permission'])->group(function () {
        Route::get('student/student-info/student-list', [StudentController::class, 'studentList'])->name('student-list');
        Route::get('/student/student-info/student-list-datatable', [StudentController::class, 'studentDatatable'])->name('student-list.datatable');
        Route::get('student/student-info/verification-history/{id}', 'verificationHistory')->name('student-list.verification-history');
        Route::put('student/update-status', [StudentController::class, 'updateStudetStatus'])->name('student.update-status');
        Route::get('student/student-info/student-view/{id}', [StudentController::class, 'studentView'])->name('student-details');
        Route::get('student/student-info/disabled-student', [StudentController::class, 'disabledStudents'])->name('disabled-students');
        Route::get('student/student-info/student-attendance', [StudentController::class, 'studentAttendance'])->name('student-attendance');
        Route::get('student/student-info/student-attendance-import', [StudentController::class, 'studentAttendanceImport'])->name('student-attendance-import');
        Route::post('student/student-info/student-attendance-import', [StudentController::class, 'studentAttendanceImportStore'])->name('student-attendance-import.store');
        Route::get('student/student-info/student-attendance-import/download/{id}', [StudentController::class, 'studentAttendanceImportDownload'])->name('student-attendance-import.download');
        Route::get('student/student-info/student-export', [StudentController::class, 'studentExport'])->name('student-export');
        Route::match(['get', 'post'], 'student/student-info/diabled-filter', [StudentController::class, 'filter_disabled_StudentList'])->name('frontend.disabled-student.search');
        Route::match(['get', 'post'], 'student/student-info/filtert', [StudentController::class, 'filterStudentList'])->name('frontend.student.search');
    // });

    // Dependent Dropdown AJAX Routes (Student Info, Fees, etc.)
    Route::prefix('student')->group(function () {
        Route::get('student-info/get-intakes-by-branch/{branchId}', 'getIntakesByBranch')->name('get-intakes-by-branch');
        Route::get('student-info/get-courses-by-intake/{intakeId}', 'getCoursesByIntake')->name('get-courses-by-intake');
        Route::get('student-info/get-courses-by-branch/{branchId}', 'getCoursesByBranch')->name('get-courses-by-branch')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-info/get-courses-by-student/{studentId}', 'getCoursesByStudent')->name('get-courses-by-student')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-info/get-intakes/{branchId}', 'getIntakes')->name('student.get-intakes');
        Route::get('student-info/get-courses/{intakeId}', 'getCourses')->name('student.get-courses');
        Route::get('student-attendance/get-courses/{intakeId}', [StudentController::class, 'getCoursesByIntake'])->name('student-attendance.get-courses')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-list/get-intakes/{branchId}', 'getIntakesByBranch')->name('student-list.get-intakes')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-list/get-courses/{intakeId}', 'getCoursesByIntake')->name('student-list.get-courses')->withoutMiddleware(['csrf', 'permission']);
    });

    // Inventory Management
    Route::prefix('account/inventory')->group(function () {
        Route::get('item-receive', 'itemReceive')->name('item-receive');
        Route::get('item-receive-list', 'itemReceiveList')->name('item-receive-list');
        Route::get('item-receive/{transactionId}/view', 'viewItemReceive')->name('item-receive.view');
        Route::delete('item-receive/{transactionId}', 'deleteItemReceive')->name('item-receive.destroy');
        Route::post('/item-receive/save', 'itemReceivePost')->name('save-item-receive-data');
        Route::post('/item-receive/save-invoice-number', 'itemReceivesaveInvoiceNumber')->name('item-receive.invoic-save');
    })->withoutMiddleware(['permission']);

    // Fees & Returns
    // Route::middleware(['permission'])->group(function () {
        Route::get('student/fees/fee-invoice', 'feeInvoice')->name('fees.fee-invoice');
        Route::get('student/fees/fee-invoice-datatable', 'invoiceDatatable')->name('fee.invoice.datatable')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student/fees/fee-invoice-setting', 'feeInvoiceSetting')->name('fee-invoice-setting');
        Route::get('student/fees/fee-return', 'feeReturn')->name('fee-return');
        Route::post('student/fees/fee-return/search', 'feeReturn')->name('fees.fee-return.search');
    // });
});

Route::controller(CourseController::class)->prefix('administration/academic/courses')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('course.index');
    Route::post('store', 'store')->name('course.store');
    Route::post('update/{id}', 'update')->name('course.update');
    Route::get('edit/{id}', 'edit')->name('course.edit');
    Route::delete('destroy/{id}', 'destroy')->name('course.destroy');
});

Route::controller(IntakeController::class)->prefix('administration/academic/intake')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('intake.index');
    Route::post('store', 'store')->name('intake.store');
    Route::post('update/{id}', 'update')->name(name: 'intake.update');
    Route::get('edit/{id}', 'edit')->name('intake.edit');
    Route::delete('destroy/{id}', 'destroy')->name('intake.destroy');
});

Route::controller(SubjectController::class)->prefix('administration/academic/subjects')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('subject.index');
    Route::post('store', 'store')->name('subject.store');
    Route::post('update/{id}', 'update')->name('subject.update');
    Route::get('edit/{id}', 'edit')->name('subject.edit');
    Route::delete('destroy/{id}', 'destroy')->name('subject.destroy');
});

Route::controller(AssignSubjectController::class)->prefix('administration/academic/assign-subject')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('assign-subject.index');
    Route::post('store', 'store')->name('assign-subject.store');
    Route::post('update/{id}', 'update')->name('assign-subject.update');
    Route::get('edit/{id}', 'edit')->name('assign-subject.edit');
    Route::delete('destroy/{id}', 'destroy')->name('assign-subject.destroy');
    Route::get('get-courses/{intakeId}', 'getCoursesByIntake')->name('assign-subject.get-courses')->withoutMiddleware(['csrf', 'permission']);
});

Route::controller(LessonController::class)->prefix('administration/lesson-plan/lesson')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('lesson.index');
    Route::post('store', 'store')->name('lesson.store');
    Route::post('update/{id}', 'update')->name('lesson.update');
    Route::get('edit/{id}', 'edit')->name('lesson.edit');
    Route::delete('destroy/{id}', 'destroy')->name('lesson.destroy');
    Route::get('get-intakes/{courseId}', 'getIntakesByCourse')->name('lesson.get-intakes')->withoutMiddleware(['csrf', 'permission']);
    Route::get('get-subjects/{intakeId}', 'getSubjectsByIntake')->name('lesson.get-subjects')->withoutMiddleware(['csrf', 'permission']);
});

Route::controller(TopicController::class)->prefix('administration/lesson-plan/topic')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('topic.index');
    Route::post('store', 'store')->name('topic.store');
    Route::put('update/{id}', 'update')->name('topic.update');
    Route::get('edit/{id}', 'edit')->name('topic.edit');
    Route::delete('destroy/{id}', 'destroy')->name('topic.destroy');
    Route::get('get-intakes/{courseId}', 'getIntakesByCourse')->name('topic.get-intakes')->withoutMiddleware(['csrf', 'permission']);
    Route::get('get-subjects/{intakeId}', 'getSubjectsByIntake')->name('topic.get-subjects')->withoutMiddleware(['csrf', 'permission']);
    Route::get('get-lessons/{subjectId}', 'getLessonsBySubject')->name('topic.get-lessons')->withoutMiddleware(['csrf', 'permission']);
});

Route::controller(TopicOverviewController::class)->prefix('administration/lesson-plan/topic-overview')->middleware(['permission'])->group(callback: function () {
    Route::get('/', 'index')->name('topic-overview.index');
    Route::post('store', 'store')->name('topic-overview.store');
    Route::get('get-intakes/{courseId}', 'getIntakesByCourse')->name('topic-overview.get-intakes')->withoutMiddleware(['csrf', 'permission']);
    Route::get('get-subjects/{intakeId}', 'getSubjectsByIntake')->name('topic-overview.get-subjects')->withoutMiddleware(['csrf', 'permission']);
});


// ================= STUDENT ROUTES =================

// Student Category Management
Route::controller(StudentCategoryController::class)
    ->prefix('student/student-info/student-category')
    ->middleware(['permission'])
    ->group(function () {
        Route::get('/', 'index')->name('student-category.index');
        Route::post('store', 'store')->name('student-category.store');
        Route::put('update/{id}', 'update')->name('student-category.update');
        Route::get('edit/{id}', 'edit')->name('student-category.edit');
        Route::delete('destroy/{id}', 'destroy')->name('student-category.destroy');
    });

// Add Student Management
Route::controller(StudentController::class)
    ->prefix('student/student-info/add-student')
    ->group(function () {
        Route::get('/', 'index')->name('add-student.index');
        Route::post('/check-admission-number', 'checkAdmission')->name('check-admission-number');
        Route::post('store', 'store')->name('add-student.store');
        Route::put('update/{student}', 'update')->name('add-student.update');
        Route::get('edit/{student}', 'edit')->name('add-student.edit');
        Route::delete('destroy/{id}', 'destroy')->name('add-student.destroy');
        Route::get('verify/{id}', 'verify')->name('add-student.verify');
        Route::post('verify/{id}', 'verifySubmit')->name('add-student.verify.submit');
    });

// Student Course Management
Route::controller(StudentController::class)
    ->prefix('student/student-info')
    ->middleware(['permission'])
    ->group(function () {
        Route::get('student-add-course', 'studentAddCourse')->name('student.add-course.index');
        Route::post('student-add-course', 'storeStudentCourse')->name('student.add-course.store');
        Route::get('student-add-course/{id}/edit', 'editStudentCourse')->name('student.add-course.edit');
        Route::put('student-add-course/{id}', 'updateStudentCourse')->name('student.add-course.update');
        Route::delete('student-add-course/{id}', 'destroyStudentCourse')->name('student.add-course.destroy');
    });

// Student Intake Management
Route::controller(StudentController::class)
    ->prefix('student/student-info')
    ->middleware(['permission'])
    ->group(function () {
        Route::post('/student-import',  'studentImport')->name('students.import')->withoutMiddleware(['csrf', 'permission']);
        Route::post('/students/check-nic', 'checkNic')->name('students.check-nic')->withoutMiddleware(['csrf', 'permission']);
        Route::POST('/change-course/{id}', 'changeCourse')->name('student.add-intake-with-courses.change-course')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-add-intake', 'studentAddIntakeWithCourses')->name('student.add-intake-with-courses.index')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-intake-data', 'studentAddIntakeWithCoursesData')->name('student.add-intake-with-courses.data')->withoutMiddleware(['csrf', 'permission']);
        Route::post('student-add-intake', 'storeStudentIntakeWithCourses')->name('student.add-intake-with-courses.store');
        Route::get('student-add-intake/{id}/edit', 'editStudentIntake')->name('student.add-intake.edit');
        Route::put('student-add-intake/{id}', 'updateStudentIntake')->name('student.add-intake.update');
        Route::delete('student-add-intake/{id}', 'destroyStudentAddIntakeWithCourses')->name('student.add-intake.destroy');
        // Search functionality for Student Add Intake
        Route::post('student-add-intake/search', 'searchStudentIntake')->name('student.add-intake.search');
        Route::get('student-add-intake/get-intakes/{branchId}', 'getSearchIntakesByBranch')->name('student.add-intake.get-intakes')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student-add-intake/get-courses/{intakeId}', 'getSearchCoursesByIntake')->name('student.add-intake.get-courses')->withoutMiddleware(['csrf', 'permission']);
        Route::get('intake-course-search', 'intakeCourseSearch')->name('student.intake-course.search')->withoutMiddleware(['csrf', 'permission']);
        // Status update for Student Intake
        Route::post('student-add-intake/{id}/update-status', 'updateStatus')->name('student.add-intake.update-status');
    });

// Student List, Attendance, Export, and AJAX/Dependent Dropdowns
Route::controller(HomeController::class)
    ->group(function () {
        Route::get('student/student-info/student-list', [StudentController::class, 'studentList'])->name('student-list');
        Route::get('/student/student-info/student-list-datatable',  [StudentController::class, 'studentDatatable'])->name('student-list.datatable');
        Route::get('student/student-info/verification-history/{id}', 'verificationHistory')->name('student-list.verification-history');
        Route::put('student/update-status', [StudentController::class, 'updateStudetStatus'])->name('student.update-status');
        Route::get('student/student-info/student-view/{id}', [StudentController::class, 'studentView'])->name('student-details');
        Route::get('student/student-info/disabled-student', [StudentController::class, 'disabledStudents'])->name('disabled-students');
        Route::get('student/student-info/student-attendance', [StudentController::class, 'studentAttendance'])->name('student-attendance');
        Route::get('student/student-info/student-export', [StudentController::class, 'studentExport'])->name('student-export');
        // Dependent dropdown AJAX routes for student info and fees
        Route::get('student/student-info/get-intakes-by-branch/{branchId}', 'getIntakesByBranch')->name('get-intakes-by-branch');
        Route::get('student/student-info/get-courses-by-intake/{intakeId}', 'getCoursesByIntake')->name('get-courses-by-intake');
        Route::get('student/student-info/get-courses-by-branch/{branchId}', 'getCoursesByBranch')->name('get-courses-by-branch')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student/student-info/get-courses-by-student/{studentId}', 'getCoursesByStudent')->name('get-courses-by-student')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student/student-info/get-intakes/{branchId}', 'getIntakes')->name('student.get-intakes');
        Route::get('student/student-info/get-courses/{intakeId}', 'getCourses')->name('student.get-courses');
        Route::get('student/student-attendance/get-courses/{intakeId}', 'getCoursesByIntake')->name('student-attendance.get-courses')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student/student-list/get-intakes/{branchId}', 'getIntakesByBranch')->name('student-list.get-intakes')->withoutMiddleware(['csrf', 'permission']);
        Route::get('student/student-list/get-courses/{intakeId}', 'getCoursesByIntake')->name('student-list.get-courses')->withoutMiddleware(['csrf', 'permission']);
        // Filter student list
        Route::post('student/student-info/diabled-filter', [StudentController::class, 'filter_disabled_StudentList'])->name('frontend.disabled-student.search');
        Route::post('student/student-info/filtert', [StudentController::class, 'filterStudentList'])->name('frontend.student.search');
    });
// ================= END STUDENT ROUTES =================

// Route::controller(HomeController::class)->middleware(['permission'])->group(callback: function () {
Route::controller(HomeController::class)->group(callback: function () {

    // Route::get('dashboard', 'dashboard')->name('dashboard');

    Route::get('administration/study-material/upload-content', 'uploadContent')->name('upload-content');
    Route::get('administration/study-material/study-material-download', 'studyMaterialDownload')->name('study-material-download');
    Route::get('administration/study-material/other-download', 'otherDownload')->name('other-download');

    Route::get('administration/download-center/shared-content-list', 'sharedContentList')->name('shared-content-list');
    Route::get('administration/download-center/video-list', 'videoList')->name('video-list');
    Route::post('administration/download-center/video-list-save', 'videoListStore')->name('download-center.video-list-save');
    Route::put('administration/download-center/video-list-update/{id}', 'videoListUpdate')->name('download-center.video-list-update');
    Route::delete('administration/download-center/video-list-destroy/{id}', 'videoListDestroy')->name('download-center.video-list-destroy');

    Route::get('student/student-info/student-list', [StudentController::class, 'studentList'])->name(name: 'student-list');
    Route::get('/student/student-info/student-list-datatable',  [StudentController::class, 'studentDatatable'])->name('student-list.datatable');

    Route::get('student/student-info/verification-history/{id}', 'verificationHistory')->name('student-list.verification-history');
    Route::put('student/update-status', [StudentController::class, 'updateStudetStatus'])->name('student.update-status');
    Route::get('student/student-info/student-view/{id}', [StudentController::class, 'studentView'])->name('student-details');

    Route::get('student/student-info/disabled-student', [StudentController::class, 'disabledStudents'])->name('disabled-students');
    Route::get('student/student-info/student-attendance', [StudentController::class, 'studentAttendance'])->name('student-attendance');
    Route::get('student/student-info/student-export', [StudentController::class, 'studentExport'])->name('student-export');

    // Dependent dropdown AJAX routes for student info and fees
    Route::get('student/student-info/get-intakes-by-branch/{branchId}', 'getIntakesByBranch')->name('get-intakes-by-branch');
    Route::get('student/student-info/get-courses-by-intake/{intakeId}', 'getCoursesByIntake')->name('get-courses-by-intake');
    Route::get('student/student-info/get-courses-by-branch/{branchId}', 'getCoursesByBranch')->name('get-courses-by-branch')->withoutMiddleware(['csrf', 'permission']);
    Route::get('student/student-info/get-courses-by-student/{studentId}', 'getCoursesByStudent')->name('get-courses-by-student')->withoutMiddleware(['csrf', 'permission']);

    // Other existing dependent dropdown AJAX routes
    Route::get('student/student-info/get-intakes/{branchId}', 'getIntakes')->name('student.get-intakes');
    Route::get('student/student-info/get-courses/{intakeId}', 'getCourses')->name('student.get-courses');
    Route::get('student/student-attendance/get-courses/{intakeId}', 'getCoursesByIntake')->name('student-attendance.get-courses')->withoutMiddleware(['csrf', 'permission']);
    Route::get('student/student-list/get-intakes/{branchId}', 'getIntakesByBranch')->name('student-list.get-intakes')->withoutMiddleware(['csrf', 'permission']);
    Route::get('student/student-list/get-courses/{intakeId}', 'getCoursesByIntake')->name('student-list.get-courses')->withoutMiddleware(['csrf', 'permission']);

    //filter student list by branch
    Route::post('student/student-info/diabled-filter', [StudentController::class, 'filter_disabled_StudentList'])->name('frontend.disabled-student.search');
    Route::post('student/student-info/filtert', [StudentController::class, 'filterStudentList'])->name('frontend.student.search');
    // Route::get('student/fees/fee-invoice', 'feeInvoice')->name('fee-invoice');
    // Route::get('student/fees/add-invoice', 'addInvoice')->name('add-invoice');
    Route::get('student/fees/fee-invoice-setting', 'feeInvoiceSetting')->name('fee-invoice-setting');
    Route::get('student/fees/fee-return', 'feeReturn')->name('fee-return');
    // Route::get('account/inventory/item-list', 'itemList')->name('item-list');
    // Route::get('account/inventory/supplier', 'supplier')->name('supplier');
    Route::get('account/inventory/item-receive', 'itemReceive')->name('item-receive')->withoutMiddleware(['permission']);
    Route::get('account/inventory/item-receive-list', 'itemReceiveList')->name('item-receive-list')->withoutMiddleware(['permission']);
    Route::get('account/inventory/item-receive/{transactionId}/view', [HomeController::class, 'viewItemReceive'])->name('item-receive.view')->withoutMiddleware(['permission']);
    // Route::get('account/inventory/item-receive/{transactionId}/edit', [HomeController::class, 'editItemReceive'])->name('item-receive.edit')->withoutMiddleware(['permission']);
    // Route::put('account/inventory/item-receive/{id}', [HomeController::class, 'updateItemReceive'])->name('item-receive.update')->withoutMiddleware(['permission']);
    Route::delete('account/inventory/item-receive/{transactionId}', [HomeController::class, 'deleteItemReceive'])->name('item-receive.destroy')->withoutMiddleware(['permission']);
    Route::post('/item-receive/save', [HomeController::class, 'itemReceivePost'])
        ->name('save-item-receive-data')->withoutMiddleware(['permission']);
    Route::post('/item-receive/save-invoice-number', [HomeController::class, 'itemReceivesaveInvoiceNumber'])
        ->name('item-receive.invoic-save')->withoutMiddleware(['permission']);
});

Route::prefix('/student/fees/fee-invoice/invoice-payments')->group(function () {
    Route::get('/', [InvoicePaymentController::class, 'index'])->name('invoice-payments.index');
    Route::post('/search', [InvoicePaymentController::class, 'search'])->name('invoice-payments.search');
    Route::get('/datatable', [InvoicePaymentController::class, 'datatable'])->name('invoice-payments.datatable');
    Route::post('/bulk-not-open', [InvoicePaymentController::class, 'bulkNotOpen'])->name('invoice-payments.bulk-not-open');
    Route::post('/bulk-pending', [InvoicePaymentController::class, 'bulkPending'])->name('invoice-payments.bulk-pending');
    Route::get('/print/{invoice_number}', [InvoicePaymentController::class, 'printInvoice'])->name('invoice-payments.print');
    Route::post('/bulk-back', [InvoicePaymentController::class, 'bulkBack'])->name('invoice-payments.bulk-back');
})->withoutMiddleware(['csrf', 'permission']);

Route::controller(ItemSellController::class)
    ->middleware(['permission'])
    ->group(function () {
        Route::get('account/inventory/item-sell', 'itemSell')->name('item-sell');
        Route::post('account/inventory/item-sell/save', 'saveItemSellData')->name('save-item-sell-data');
        Route::get('account/inventory/item-sell-list', 'itemSellList')->name('item-sell-list');
        Route::get('account/inventory/item-sell/{id}', 'viewItemSell')->name('view-item-sell');
        Route::get('account/inventory/item-sell/{id}/return', 'returnItemSell')->name('return-item-sell');
        Route::post('account/inventory/item-sell/{id}/update-status', 'updateItemSellStatus')->name('update-item-sell-status');
        Route::post('account/inventory/item-sell/{id}/direct-return', 'directReturnItemSell')->name('direct-return-item-sell');
        Route::get('account/inventory/item-sell/{id}/return-view', action: 'returnItemSellView')->name('return-item-sell-view');
        Route::get('account/inventory/get-item-price/{id}', 'getItemPrice')->name(name: 'get-item-price')->withoutMiddleware(['csrf', 'permission']);
        Route::post('account/inventory/item-sell/save-invoice-number', 'itemSellSaveInvoiceNumber')
            ->name('item-sell.invoic-save')->withoutMiddleware(['permission']);
        Route::get('account/inventory/item-sell-bill/{id}', 'itemSellBill')->name('inventory.item-sell-bill')->withoutMiddleware(['csrf', 'permission']);
    });

Route::controller(ItemIssueController::class)->middleware(['permission'])->group(function () {
    Route::get('account/inventory/item-issue', 'index')->name('item-issue');
    Route::get('account/inventory/item-issue-list', 'issueListView')->name('item-issue-list');
    Route::post('account/inventory/save-item-issue-data', 'saveItemIssueData')->name('save-item-issue-data');
    // Route::get('account/inventory/item-issue-edit/{id}', 'edit')->name('item-issue-edit');
    // Route::put('account/inventory/item-issue-update/{id}', 'update')->name('item-issue-update');
    Route::get('account/inventory/return-item-view/{id}', 'returnItemView')->name('return-item-view');
    Route::post('account/inventory/return-item', 'returnItem')->name('return-item');
    Route::post('account/inventory/issue-item', 'IssueItem')->name('issue-item');

    // AJAX routes for dynamic filtering
    Route::get('account/inventory/get-students-by-branch/{branchId}', 'getStudentsByBranch')->name('item-issue.get-students-by-branch')->withoutMiddleware(['permission']);
    Route::get('account/inventory/get-intakes-by-branch/{branchId}', 'getIntakesByBranch')->name('item-issue.get-intakes-by-branch')->withoutMiddleware(['permission']);
    Route::get('account/inventory/get-courses-by-intake/{intakeId}', 'getCoursesByIntake')->name('item-issue.get-courses-by-intake')->withoutMiddleware(['permission']);
    Route::get('account/inventory/get-items-by-category/{categoryId}', 'getItemsByCategory')->name('item-issue.get-items-by-category')->withoutMiddleware(['permission']);
    Route::get('account/inventory/get-student-fee-data', 'getStudentFeeData')->name('item-issue.get-student-fee-data')->withoutMiddleware(['permission']);
    Route::get('item-issue/get-student-details/{studentId}', 'getStudentDetails')->name('item-issue.get-student-details')->withoutMiddleware(['permission']);
    Route::get('item-issue/get-courses-by-intake-and-student/{intakeId}/{studentId}', 'getCoursesByIntakeAndStudent')->name('item-issue.get-courses-by-intake-and-student')->withoutMiddleware(['permission']);
    Route::get('item-issue/get-intake-by-branch-and-student/{branchId}/{studentId}', 'getIntakeByBranchAndStudent')->name('item-issue.get-intakes-by-branch-and-student')->withoutMiddleware(['permission']);

    Route::get('item-issue/get-batch-details/{batchId}', 'getBatchDetails')->name('item-issue.get-batch-details')->withoutMiddleware(['permission']);
    Route::get('item-issue/check-student-course-status', 'checkStudentCourseStatus')->name('item-issue.check-student-course-status')->withoutMiddleware(['permission']);
});
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login'); // For displaying the login form
    Route::post('login', 'login')->name('login.post'); // For handling login form submission
    Route::get('recoveryPassword', 'recoveryPassword')->name('recoveryPassword');
    Route::get('register', 'register')->name('register');
    Route::post('logout', 'logout')->name('logout'); // Add logout route
});



Route::controller(FeesGroupController::class)->middleware(['permission'])->group(function () {
    Route::get('student/fees/fee-group', 'index')->name('fee-group');
    Route::post('student/fees/fee-group-create', 'store')->name('fee-group-create');
    Route::get('student/fees/fee-group-edit/{id}', 'edit')->name('fee-group-edit');
    Route::put('student/fees/fee-group-update/{id}', 'update')->name('fee-group-update');
    Route::delete('student/fees/fee-group-delete/{id}', 'delete')->name('fee-group-delete');

    Route::get('student/fees/course-paid', 'coursePaidView')->name('fees.course-paid');


    Route::post('student/fees/course-payment-store', 'storeCoursePayment')->name('fees.course-payment.store');
    Route::get('student/fees/course-payment', 'indexCoursePayment')->name('fees.course-payment');
    Route::get('student/fees/course-payment-history/{studentId}/{courseId}', 'getCoursePaymentHistory')->name('fees.course-payment-history')->withoutMiddleware(['csrf', 'permission']);
});

Route::controller(FeeTypeController::class)->middleware(['permission'])->group(function () {

    Route::get('student/fees/fee-type', 'index')->name('fees.fees-type');
    Route::post('student/fees/fee-type-store', 'store')->name('fees.fees-type-store');
    Route::get('student/fees/fee-type-edit/{id}', 'edit')->name('fees.fees-type-edit');
    Route::put('student/fees/fee-type-update/{id}', 'update')->name('fees.fees-type-update');
    Route::delete('student/fees/fee-type-delete/{id}', 'destroy')->name('fees.fees-type-delete');
});

Route::controller(InvoiceController::class)->middleware(['permission'])->group(function () {

    Route::get('/fees/confirm-invoice-payment/{payment}', 'confirmInvoicePayment')->withoutMiddleware(['csrf', 'permission']);

    Route::get('student/fees/invoice/view/{id}', 'viewFeeInvoice')->name('fees.fee-invoice-view');
    Route::get('student/fees/student-details/{studentId}', 'getStudentDetails')->name('fees.student-details');
    Route::post('fees/add-payment/{invoiceId}', 'addPayment')->name('fees.add-payment');

    Route::get('student/fees/add-invoice', 'create')->name('fees.add-invoice');
    Route::get('student/invoice/get-student/{branchId}', 'show')->name('fees.get-student');
    Route::get('student/invoice/types_get_by_group/{groupId}', 'dropdown_fee_types')->name('fees.get-types-by-group');

    Route::post('student/fees/fee-store', 'store')->name('fees.invoice.store');
    Route::get('fees/payment-history/{invoiceId}', 'getPaymentHistory')->name('fees.payment-history')->withoutMiddleware(['csrf', 'permission']);
    Route::get('fees/return-fee-data/{invoiceId}', 'getReturnFeeData')->name('fees.return-fee-data')->withoutMiddleware(['csrf', 'permission']);

    Route::get('fees/get-intakes-by-course/{courseId}/{studentId}', 'getIntakesByCourse')->name('fees.get-intakes-by-course')->withoutMiddleware(['csrf', 'permission']);
    // Route::get('student/fees/fee-edit/{id}', 'edit')->name('fees.fee-edit-invoice');
    Route::get('student/fees/fee-invoice-edit/{id}', 'edit')->name('fees.fee-invoice-edit'); // GET edit

    Route::put('student/fees/fee-update/{id}', 'update')->name('fees.fee-update-invoice');
    Route::delete('student/fees/fee-delete/{id}', 'destroy')->name('fees.fee-delete');

    // New routes for invoice actions
    Route::post('fees/update-invoice-number', 'updateInvoiceNumber')->name('fees.update-invoice-number');
    Route::post('fees/mark-as-paid', 'markAsPaid')->name('fees.mark-as-paid');
    Route::post('fees/refund-payment/{paymentId}', 'refundPayment')->name('fees.refund-payment')->withoutMiddleware(['csrf', 'permission']);
    Route::post('fees/add-payment-invoice-number', 'addPaymentInvoiceNumber')->name('fees.add-payment-invoice-number')->withoutMiddleware(['csrf', 'permission']);

    // New route for invoice report
    // Route::get('student/fees/invoice-print', 'invoiceReport')->name('fees.invoice-report')->withoutMiddleware(['permission']);
    // Route for invoice report data (AJAX)
    Route::get('student/fees/invoice-print/{invoiceId}', 'invoicePrint')->name('fees.invoice-print')->withoutMiddleware(['csrf', 'permission']);
    Route::get('student/fees/invoice-item-print/{invoiceId}/{paymentId}', 'invoiceItemPrint')->name('fees.invoice-item-print')->withoutMiddleware(['csrf', 'permission']);
});

Route::controller(HomeController::class)->middleware(['permission'])->group(function () {
    Route::get('student/fees/fee-invoice', action: 'feeInvoice')->name('fees.fee-invoice');
    Route::get('student/fees/fee-invoice-datatable', 'invoiceDatatable')->name('fee.invoice.datatable')->withoutMiddleware(['csrf', 'permission']);

    Route::post('student/fees/fee-return/search', 'feeReturn')->name('fees.fee-return.search');
});

Route::controller(CategoryController::class)->middleware(['permission'])->group(function () {
    Route::get('account/inventory/item-category', 'index')->name('item-category');
    Route::post('account/inventory/item-category/store', 'store')->name('item-category-store');
    Route::get('account/inventory/item-category/edit/{id}', 'edit')->name('item-category-edit');
    Route::put('account/inventory/item-category/update/{id}', 'update')->name('item-category-update');
    Route::delete('account/inventory/item-category/delete/{id}', 'destroy')->name('delete-item-category');
});

// Inventory Item Management
Route::controller(InventoryItemController::class)->middleware(['permission'])->group(function () {
    Route::get('account/inventory/item-list', 'index')->name('item-list');
    Route::get('account/inventory/item-view/{id}', 'view')->name('item-view');
    Route::get('account/inventory/item-history/{id}', 'getItemHistory')->name('item-history')->withoutMiddleware(['csrf', 'permission']);
    Route::post('account/inventory/item-store', 'store')->name('item-store');
    Route::get('account/inventory/item-edit/{id}', 'edit')->name('item-edit');
    Route::put('account/inventory/item-update/{id}', 'update')->name('item-update');
    Route::delete('account/inventory/item-delete/{id}', 'destroy')->name('item-delete');
});
// Inventory Supplier Management
Route::controller(SupplierController::class)->middleware(['permission'])->group(function () {
    Route::get('account/inventory/supplier', 'index')->name('supplier');
    Route::post('account/inventory/supplier-store', 'store')->name('supplier-store');
    Route::get('account/inventory/supplier-edit/{id}', 'edit')->name('supplier-edit');
    Route::put('account/inventory/supplier-update/{id}', 'update')->name('supplier-update');
    Route::delete('account/inventory/supplier-delete/{id}', 'destroy')->name('supplier-delete');
});

Route::controller(ContentTypeController::class)->middleware(['permission'])->group(function () {
    Route::get('administration/download-center/content-type', 'index')->name('content-type');
    Route::post('account/content-type-store', 'store')->name('content-type-store');
    Route::get('account/content-type-edit/{id}', 'edit')->name('content-type-edit');
    Route::put('account/content-type-update/{id}', 'update')->name('content-type-update');
    Route::delete('account/content-type-delete/{id}', 'destroy')->name('content-type-delete');
});

Route::controller(ContentController::class)->middleware(['permission'])->group(function () {
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
    Route::get('administration/download-center/get-courses/{intakeId}', 'getCoursesByIntake')->name('download-center.get-courses-by-intake')->withoutMiddleware(['csrf', 'permission']);
    // Save generated URL data
    Route::post('administration/download-center/save-generated-url-data', 'saveGeneratedUrlData')->name('download-center.save-generated-url-data');
    // View shared content details
    Route::get('administration/download-center/shared-content-view/{id}', 'viewSharedContent')->name('download-center.shared-content-view');
    Route::delete('administration/download-center/shared-content-delete/{id}', 'destroySharedContent')->name('download-center.shared-content-delete');
});

// Return Fee Routes

Route::controller(ReturnFeeController::class)->middleware(['permission'])->group(function () {
    Route::post('/student/fees/return-fees', 'store')->name('return-fees.store');
});


// ================= ROLE & USER MANAGEMENT ROUTES =================

// Role Management
Route::controller(RoleController::class)
    ->prefix('role-management')
    ->middleware(['permission'])
    ->group(function () {
        Route::get('roles', 'index')->name('roles.index');
        Route::post('roles', 'store')->name('roles.store');
        Route::put('roles/{id}', 'update')->name('roles.update');
        Route::delete('roles/{id}', 'destroy')->name('roles.destroy');
        Route::get('roles/{id}/edit', 'edit')->name('roles.edit');
        Route::get('roles/{id}/grant-permissions', 'grantPermission')->name('roles.grantPermission');
        Route::post('roles/{id}/save-permissions', 'savePermissions')->name('roles.savePermissions');
    });

// User Management
Route::controller(UserController::class)
    ->prefix('role-management')
    ->middleware(['permission'])
    ->group(function () {
        Route::get('create-user', 'createUser')->name('role-management.create-user');
        Route::post('store-user', 'storeUser')->name('role-management.store-user');
        Route::get('user-list', 'userList')->name('role-management.user-list')->withoutMiddleware(['csrf', 'permission']);
        Route::get('user-details/{id}', 'getUserDetails')->name('role-management.user-details')->withoutMiddleware(['csrf', 'permission']);
        Route::put('user-update/{id}', 'updateUser')->name('role-management.user-update')->withoutMiddleware(['csrf', 'permission']);
        Route::post('user-change-password/{id}', 'changePassword')->name('role-management.user-change-password')->withoutMiddleware(['csrf', 'permission']);
        Route::get('get-student-details/{id}', 'getStudentDetails')->name('role-management.get-student-details');
    });
// ================= END ROLE & USER MANAGEMENT ROUTES =================

// Reports Routes
Route::controller(ReportController::class)->prefix('reports')
    // ->middleware(['permission'])
    ->group(function () {
        Route::get('course-paid', 'coursePaid')->name('reports.course-paid');
        Route::get('sell-items', 'sellItems')->name('reports.sell-items');
        Route::get('issue-items', 'issueItems')->name('reports.issue-items');
        Route::get('students-paid', 'studentsPaid')->name(name: 'reports.students-paid');
        Route::get('course-dates-payments', 'studentsDatesPayments')->name(name: 'reports.course-dates-payments');
        Route::get('course-intake-payments', 'courseIntakePayments')->name('reports.course-intake-payments');
    });

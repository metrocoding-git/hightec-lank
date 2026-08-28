<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ContentListController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FeesGroupController;
use App\Http\Controllers\ItemIssueController;
use App\Http\Controllers\ReturnFeeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContentTypeController;
use App\Http\Controllers\AssignSubjectController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ItemReceiveController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\TopicOverviewController;
use App\Http\Controllers\StudentCategoryController;
use App\Http\Controllers\ItemSellController;
use App\Http\Controllers\VideoListController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CoursePaymentController;
use App\Http\Controllers\OtherPaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentCardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScanAttendanceController;
use App\Http\Controllers\TrainingVehicleController;

Route::middleware(['permission'])->group(function () {

    // ================= ADMINISTRATION ROUTES ================= //
    Route::prefix('administration')->group(callback: function () {

        // ================= ACADEMIC MANAGEMENT ROUTES ================= //
        Route::prefix('academic')->group(callback: function () {

            // ================= ACADEMIC MANAGEMENT COURSE ROUTES ================= //
            Route::prefix('courses')->group(callback: function () {
                Route::get('/', [CourseController::class, 'index'])->name('course.index');
                Route::post('store', [CourseController::class, 'store'])->name('course.store');
                Route::post('update/{id}', [CourseController::class, 'update'])->name('course.update');
                Route::get('edit/{id}', [CourseController::class, 'edit'])->name('course.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
                Route::post('toggle-status/{id}', [CourseController::class, 'toggleStatus'])->name('course.toggle-status')->withoutMiddleware(['permission']);
            });

            // ================= ACADEMIC MANAGEMENT INTAKE ROUTES ================= //
            Route::prefix('intake')->group(callback: function () {
                Route::get('/', [IntakeController::class, 'index'])->name('intake.index');
                Route::post('store', [IntakeController::class, 'store'])->name('intake.store');
                Route::post('update/{id}', [IntakeController::class, 'update'])->name(name: 'intake.update');
                Route::get('edit/{id}', [IntakeController::class, 'edit'])->name('intake.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [IntakeController::class, 'destroy'])->name('intake.destroy');
                Route::post('toggle-status/{id}', [IntakeController::class, 'toggleStatus'])->name('intake.toggle-status')->withoutMiddleware(['permission']);
            });

            // ================= ACADEMIC MANAGEMENT SUBJECT ROUTES ================= //
            Route::prefix('subjects')->group(callback: function () {
                Route::get('/', [SubjectController::class, 'index'])->name('subject.index');
                Route::post('store', [SubjectController::class, 'store'])->name('subject.store');
                Route::post('update/{id}', [SubjectController::class, 'update'])->name('subject.update');
                Route::get('edit/{id}', [SubjectController::class, 'edit'])->name('subject.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy');
                Route::post('toggle-status/{id}', [SubjectController::class, 'toggleStatus'])->name('subject.toggle-status')->withoutMiddleware(['permission']);
            });

            // ================= ACADEMIC MANAGEMENT ASSIGN SUBJECT ROUTES ================= //
            Route::prefix('assign-subject')->group(callback: function () {
                Route::get('/', [AssignSubjectController::class, 'index'])->name('assign-subject.index');
                Route::post('store', [AssignSubjectController::class, 'store'])->name('assign-subject.store');
                Route::post('update/{id}', [AssignSubjectController::class, 'update'])->name('assign-subject.update');
                Route::get('edit/{id}', [AssignSubjectController::class, 'edit'])->name('assign-subject.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [AssignSubjectController::class, 'destroy'])->name('assign-subject.destroy');
                Route::post('toggle-status/{id}', [AssignSubjectController::class, 'toggleStatus'])->name('assign-subject.toggle-status')->withoutMiddleware(['permission']);
                Route::get('get-courses/{intakeId}', [AssignSubjectController::class, 'getCoursesByIntake'])->name('assign-subject.get-courses')->withoutMiddleware(['permission']);
            });
        });


        // ================= ACADEMIC MANAGEMENT LESSON PLAN ROUTES ================= //
        Route::prefix('lesson-plan')->group(callback: function () {

            // ================= LESSON PLAN MANAGEMENT LESSON ROUTES ================= //
            Route::prefix('lesson')->group(callback: function () {
                Route::get('/', [LessonController::class, 'index'])->name('lesson.index');
                Route::post('store', [LessonController::class, 'store'])->name('lesson.store');
                Route::post('update/{id}', [LessonController::class, 'update'])->name('lesson.update');
                Route::get('edit/{id}', [LessonController::class, 'edit'])->name('lesson.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [LessonController::class, 'destroy'])->name('lesson.destroy');
                Route::get('get-intakes/{courseId}', [LessonController::class, 'getIntakesByCourse'])->name('lesson.get-intakes')->withoutMiddleware(['permission']);
                Route::get('get-subjects/{intakeId}', [LessonController::class, 'getSubjectsByIntake'])->name('lesson.get-subjects')->withoutMiddleware(['permission']);
            });

            // ================= LESSON PLAN MANAGEMENT TOPIC ROUTES ================= //
            Route::prefix('topic')->group(callback: function () {
                Route::get('/', [TopicController::class, 'index'])->name('topic.index');
                Route::post('store', [TopicController::class, 'store'])->name('topic.store');
                Route::put('update/{id}', [TopicController::class, 'update'])->name('topic.update');
                Route::get('edit/{id}', [TopicController::class, 'edit'])->name('topic.edit')->withoutMiddleware(['permission']);
                Route::delete('destroy/{id}', [TopicController::class, 'destroy'])->name('topic.destroy');
                Route::get('get-intakes/{courseId}', [TopicController::class, 'getIntakesByCourse'])->name('topic.get-intakes')->withoutMiddleware(['permission']);
                Route::get('get-subjects/{intakeId}', [TopicController::class, 'getSubjectsByIntake'])->name('topic.get-subjects')->withoutMiddleware(['permission']);
                Route::get('get-lessons/{subjectId}', [TopicController::class, 'getLessonsBySubject'])->name('topic.get-lessons')->withoutMiddleware(['permission']);
            });

            // ================= LESSON PLAN MANAGEMENT TOPIC OVERVIEW ROUTES ================= //
            Route::prefix('topic-overview')->group(callback: function () {
                Route::get('/', [TopicOverviewController::class, 'index'])->name('topic-overview.index');
                Route::post('store', [TopicOverviewController::class, 'store'])->name('topic-overview.store');
                Route::get('get-intakes/{courseId}', [TopicOverviewController::class, 'getIntakesByCourse'])->name('topic-overview.get-intakes')->withoutMiddleware(['permission']);
                Route::get('get-subjects/{intakeId}', [TopicOverviewController::class, 'getSubjectsByIntake'])->name('topic-overview.get-subjects')->withoutMiddleware(['permission']);
            });
        });


        // ================= DOWNLOAD CENTER ROUTES =================
        Route::prefix('download-center')->group(callback: function () {

            // ================= DOWNLOAD CENTER CONTENT TYPE ROUTES ================= //
            Route::prefix('content-type')->group(callback: function () {
                Route::get('/', [ContentTypeController::class, 'index'])->name('content-type');
                Route::post('account/content-type-store', [ContentTypeController::class, 'store'])->name('content-type-store');
                Route::get('account/content-type-edit/{id}', [ContentTypeController::class, 'edit'])->name('content-type-edit')->withoutMiddleware(['permission']);
                Route::put('account/content-type-update/{id}', [ContentTypeController::class, 'update'])->name('content-type-update');
                Route::delete('account/content-type-delete/{id}', [ContentTypeController::class, 'destroy'])->name('content-type-delete');
            });

            // ================= DOWNLOAD CENTER CONTENT ROUTES ================= //
            Route::prefix('')->group(function () {
                Route::get('content-list', [ContentListController::class, 'index'])->name('download-center.content-list');
                Route::post('content-store', [ContentListController::class, 'store'])->name('download-center.content-store');
                Route::get('content-edit/{id}', [ContentListController::class, 'edit'])->name('download-center.content-edit')->withoutMiddleware(['permission']);
                Route::put('content-update/{id}', [ContentListController::class, 'update'])->name('download-center.content-update');
                Route::delete('content-delete/{id}', [ContentListController::class, 'destroy'])->name('download-center.content-delete');
                Route::post('content-share-list-save', [ContentListController::class, 'shareListSave'])->name('download-center.content-share-list-save');
                Route::post('content-generate-url-save', [ContentListController::class, 'generateUrlSave'])->name('download-center.content-generate-url-save');
                Route::get('get-courses/{intakeId}', [ContentListController::class, 'getCoursesByIntake'])->name('download-center.get-courses-by-intake')->withoutMiddleware(['permission']);
                Route::post('save-generated-url-data', [ContentListController::class, 'saveGeneratedUrlData'])->name('download-center.save-generated-url-data')->withoutMiddleware(['permission']);
                Route::get('shared-content-view/{id}', [ContentListController::class, 'viewSharedContent'])->name('download-center.shared-content-view');
                Route::delete('shared-content-delete/{id}', [ContentListController::class, 'destroySharedContent'])->name('download-center.shared-content-delete');
                Route::get('shared-content-list', [ContentListController::class, 'sharedContentList'])->name('download-center.shared-content-list');
            });

            // ================= DOWNLOAD CENTER VIDEO LIST ROUTES ================= //
            Route::prefix('video-list')->group(function () {
                Route::get('/', [VideoListController::class, 'videoList'])->name('download-center.video-list');
                Route::post('save', [VideoListController::class, 'videoListStore'])->name('download-center.video-list-save');
                Route::put('update/{id}', [VideoListController::class, 'videoListUpdate'])->name('download-center.video-list-update');
                Route::delete('destroy/{id}', [VideoListController::class, 'videoListDestroy'])->name('download-center.video-list-destroy');
            });
        });
    });


    // ================= STUDENT INFO MANAGEMENT ROUTES ================= //
    Route::prefix('student/student-info')->group(function () {

        // ================= STUDENT CATEGORY ROUTES ================= //
        Route::prefix('student-category')->group(function () {
            Route::get('/', [StudentCategoryController::class, 'index'])->name('student-category.index');
            Route::post('store', [StudentCategoryController::class, 'store'])->name('student-category.store');
            Route::put('update/{id}', [StudentCategoryController::class, 'update'])->name('student-category.update');
            Route::get('edit/{id}', [StudentCategoryController::class, 'edit'])->name('student-category.edit')->withoutMiddleware(['permission']);
            Route::delete('destroy/{id}', [StudentCategoryController::class, 'destroy'])->name('student-category.destroy');
            Route::post('toggle-status/{id}', [StudentCategoryController::class, 'toggleStatus'])->name('student-category.toggle-status')->withoutMiddleware(['permission']);
        });

        // ================= ADD STUDENT ROUTES ================= //
        Route::prefix('add-student')->group(function () {
            Route::get('/', [StudentController::class, 'index'])->name('add-student.index');
            Route::post('check-admission-number', [StudentController::class, 'checkAdmission'])->name('check-admission-number')->withoutMiddleware(['permission']);
            Route::post('store', [StudentController::class, 'store'])->name('add-student.store');
            Route::put('update/{student}', [StudentController::class, 'update'])->name('add-student.update');
            Route::get('edit/{student}', [StudentController::class, 'edit'])->name('add-student.edit')->withoutMiddleware(['permission']);
            Route::delete('destroy/{id}', [StudentController::class, 'destroy'])->name('add-student.destroy');
            Route::get('verify/{id}', [StudentController::class, 'verify'])->name('add-student.verify');
            Route::post('verify/{id}', [StudentController::class, 'verifySubmit'])->name('add-student.verify.submit');
            Route::post('student-import',  [StudentController::class, 'studentImport'])->name('students.import')->withoutMiddleware(['permission']);
            Route::post('students/check-nic', [StudentController::class, 'checkNic'])->name('students.check-nic')->withoutMiddleware(['permission']);
        });

        // ================= STUDENT VIEW ROUTES ================= //
        Route::get('student-view/{id}', [StudentController::class, 'studentView'])->name('student-details');

        // ================= STUDENT LIST ROUTES ================= //
        Route::prefix('student-list')->group(function () {
            Route::get('/', [StudentController::class, 'studentList'])->name('student-list');
            Route::get('datatable', [StudentController::class, 'studentDatatable'])->name('student-list.datatable')->withoutMiddleware(['permission']);
            Route::post('filtert', [StudentController::class, 'filterStudentList'])->name('student-list.search');
            Route::get('export-all', [StudentController::class, 'exportAllStudents'])->name('student-list.export-all')->withoutMiddleware(['permission']);
            Route::get('export-filtered', [StudentController::class, 'exportFilteredStudents'])->name('student-list.export-filtered')->withoutMiddleware(['permission']);
            Route::get('verification-history/{id}', [StudentController::class, 'verificationHistory'])->name('student-list.verification-history');
        });

        // ================= STUDENT CARD ROUTES ================= //
        Route::prefix('student/card')->group(function () {
            Route::get('{id}', [StudentCardController::class, 'show'])->name('student-card.show');
            Route::get('{id}/pdf', [StudentCardController::class, 'downloadPdf'])->name('student-card.pdf')->withoutMiddleware(['permission']);
            Route::get('bulk/preview', [StudentCardController::class, 'bulkPreview'])->name('student-card.bulk-preview')->withoutMiddleware(['permission']);
            Route::get('bulk/pdf', [StudentCardController::class, 'bulkPdf'])->name('student-card.bulk-pdf')->withoutMiddleware(['permission']);
        });

        // ================= STUDENT STATUS UPDATE ROUTES ================= //
        Route::put('update-status', [StudentController::class, 'updateStudetStatus'])->name('student.update-status');

        // ================= DISABLED STUDENTS ROUTES ================= //
        Route::prefix('disabled-student')->group(function () {
            Route::get('/', [StudentController::class, 'disabledStudents'])->name('disabled-students');
            Route::get('diabled-filter', [StudentController::class, 'filter_disabled_StudentList'])->name('disabled-students.search');
        });

        // ================= STUDENT ADD INTAKE ROUTES ================= //
        Route::prefix('student-add-intake')->group(function () {
            Route::POST('change-course/{id}', [StudentController::class, 'changeCourse'])->name('student.add-intake-with-courses.change-course');
            Route::get('/', [StudentController::class, 'studentAddIntakeWithCourses'])->name('student.add-intake-with-courses.index');
            Route::get('datatable', [StudentController::class, 'studentAddIntakeWithCoursesData'])->name('student.add-intake-with-courses.data')->withoutMiddleware(['permission']);
            Route::post('{id}/update-category', [StudentController::class, 'updateStudentCategory'])->name('student.add-intake.update-category');
            Route::post('store', [StudentController::class, 'storeStudentIntakeWithCourses'])->name('student.add-intake-with-courses.store');
            Route::get('{id}/edit', [StudentController::class, 'editStudentIntake'])->name('student.add-intake.edit')->withoutMiddleware(['permission']);
            Route::put('{id}', [StudentController::class, 'updateStudentIntake'])->name('student.add-intake.update');
            Route::delete('{id}', [StudentController::class, 'destroyStudentAddIntakeWithCourses'])->name('student.add-intake.destroy');
            Route::post('search', [StudentController::class, 'searchStudentIntake'])->name('student.add-intake.search');
            Route::get('get-intakes/{branchId}', [StudentController::class, 'getSearchIntakesByBranch'])->name('student.add-intake.get-intakes')->withoutMiddleware(['permission']);
            Route::get('get-courses/{intakeId}', [StudentController::class, 'getSearchCoursesByIntake'])->name('student.add-intake.get-courses')->withoutMiddleware(['permission']);
            Route::get('intake-course-search', [StudentController::class, 'intakeCourseSearch'])->name('student.intake-course.search')->withoutMiddleware(['permission']);
            Route::post('{id}/update-status', [StudentController::class, 'updateStatus'])->name('student.add-intake.update-status');
        });

        // ================= SCAN ATTENDANCE ROUTES ================= //
        Route::prefix('scan-attendance')->group(function () {
            Route::get('/', [ScanAttendanceController::class, 'index'])->name('scan-attendance');
            Route::post('/course/scan', [ScanAttendanceController::class, 'scanCourse'])->name('scan-attendance.course.scan');
            Route::get('/subjects', [ScanAttendanceController::class, 'allSubjects'])->name('scan-attendance.subjects')->withoutMiddleware(['permission']);
            Route::post('/subject/scan', [ScanAttendanceController::class, 'scanSubject'])->name('scan-attendance.subject.scan');
            Route::post('/practical/resolve', [ScanAttendanceController::class, 'resolvePracticalSubjects'])->name('scan-attendance.practical.resolve')->withoutMiddleware(['permission']);
            Route::post('/practical/scan', [ScanAttendanceController::class, 'scanPractical'])->name('scan-attendance.practical.scan');
            Route::post('/undo', [ScanAttendanceController::class, 'undoScan'])->name('scan-attendance.undo');
            Route::post('/confirm-out', [ScanAttendanceController::class, 'confirmOut'])->name('scan-attendance.confirm-out')->withoutMiddleware(['permission']);
            Route::get('/today-log', [ScanAttendanceController::class, 'todayLog'])->name('scan-attendance.today-log')->withoutMiddleware(['permission']);
        });
    });


    // ================= STUDENT FEES MANAGEMENT ROUTES ================= //
    Route::prefix('student/fees')->group(function () {

        // ================= FEE GROUP ROUTES ================= //
        Route::prefix('fee-group')->group(function () {
            Route::get('/', [FeesGroupController::class, 'index'])->name('fee-group');
            Route::post('create', [FeesGroupController::class, 'store'])->name('fee-group-create');
            Route::get('edit/{id}', [FeesGroupController::class, 'edit'])->name('fee-group-edit')->withoutMiddleware(['permission']);
            Route::put('update/{id}', [FeesGroupController::class, 'update'])->name('fee-group-update');
            Route::delete('delete/{id}', [FeesGroupController::class, 'delete'])->name('fee-group-delete');
        });

        // ================= FEE TYPE ROUTES ================= //
        Route::prefix('fee-type')->group(function () {
            Route::get('/', [FeeTypeController::class, 'index'])->name('fees.fees-type');
            Route::post('store', [FeeTypeController::class, 'store'])->name('fees.fees-type-store');
            Route::get('edit/{id}', [FeeTypeController::class, 'edit'])->name('fees.fees-type-edit')->withoutMiddleware(['permission']);
            Route::put('update/{id}', [FeeTypeController::class, 'update'])->name('fees.fees-type-update');
            Route::delete('delete/{id}', [FeeTypeController::class, 'destroy'])->name('fees.fees-type-delete');
        });

        // ================= COURSE. NVQ, FOOD PAYMENT ROUTES ================= //
        Route::prefix('course-payment')->group(function () {
            Route::get('/', [CoursePaymentController::class, 'coursePaidView'])->name('fees.course-payment');
            Route::post('store', [CoursePaymentController::class, 'storeCoursePayment'])->name('fees.course-payment.store');
            Route::get('history/{studentId}/{courseId}', [CoursePaymentController::class, 'getCoursePaymentHistory'])->name('fees.course-payment-history')->withoutMiddleware(['permission']);
        });

        // ================= OTHER PAYMENT ROUTES ================= //
        Route::prefix('other-payment')->group(function () {
            Route::get('/', [OtherPaymentController::class, 'create'])->name('fees.add-invoice');
            Route::post('fee-store', [OtherPaymentController::class, 'store'])->name('fees.invoice.store');
            Route::get('get-student/{branchId}', [OtherPaymentController::class, 'show'])->name('fees.get-student')->withoutMiddleware(['permission']);
        });

        // ================= INVOICE ITEM PRINT ROUTES ================= //
        Route::get('invoice-item-print/{invoiceId}/{paymentId}', [InvoiceController::class, 'invoiceItemPrint'])->name('fees.invoice-item-print');
        Route::get('invoice-print/{invoiceId}', [InvoiceController::class, 'invoicePrint'])->name('fees.invoice-print');

        // ================= FEE INVOICE LIST ROUTES ================= //
        Route::prefix('invoice-list')->group(function () {
            Route::get('/', [InvoiceController::class, 'feeInvoice'])->name('fees.fee-invoice');
            Route::get('datatable', [InvoiceController::class, 'invoiceDatatable'])->name('fee.invoice.datatable')->withoutMiddleware(['permission']);
            Route::post('add-payment-invoice-number', [InvoiceController::class, 'addPaymentInvoiceNumber'])->name('fees.add-payment-invoice-number')->withoutMiddleware(['permission']);
            Route::get('student/fees/student-details/{studentId}', [InvoiceController::class, 'getStudentDetails'])->name('fees.student-details')->withoutMiddleware(['permission']);
            Route::post('mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('fees.mark-as-paid');
            Route::get('payment-history/{invoiceId}', [InvoiceController::class, 'getPaymentHistory'])->name('fees.payment-history');
            Route::get('confirm-invoice-payment/{payment}', [InvoiceController::class, 'confirmInvoicePayment'])->name('fees.confirm-invoice-payment');
            Route::get('return-fee-data/{invoiceId}', [InvoiceController::class, 'getReturnFeeData'])->name('fees.return-fee-data');
            Route::post('add-payment/{invoiceId}', [InvoiceController::class, 'addPayment'])->name('fees.add-payment');
            Route::post('refund-payment/{paymentId}', [InvoiceController::class, 'refundPayment'])->name('fees.refund-payment');
        });

        // ================= FEE RETURN ROUTES ================= //
        Route::prefix('fee-return')->group(function () {
            Route::get('/', [ReturnFeeController::class, 'feeReturn'])->name('/')->withoutMiddleware(['permission']);
            Route::post('search', [ReturnFeeController::class, 'feeReturn'])->name('fees.fee-return.search');
            Route::post('store', [ReturnFeeController::class, 'store'])->name('return-fees.store');
            Route::get('invoice/view/{id}', [ReturnFeeController::class, 'viewFeeInvoice'])->name('fees.fee-invoice-view');
        });

        // ================= FEE INVOICE - INVOICE PAYMENT ROUTES ================= //
        Route::prefix('fee-invoice/invoice-payments')->group(function () {
            Route::get('/pending', [InvoicePaymentController::class, 'index'])->name('invoice-pending-payments.index');
            Route::post('/search', [InvoicePaymentController::class, 'search'])->name('invoice-payments.search');
            Route::get('/not-start-datatable', [InvoicePaymentController::class, 'notStartDatatable'])->name('invoice-payments.not-start-datatable')->withoutMiddleware(['permission']);
            Route::get('/', [InvoicePaymentController::class, 'pendingIndex'])->name('invoice-payments');
            Route::get('/pending-datatable', [InvoicePaymentController::class, 'pendingDatatable'])->name('invoice-payments-datatable')->withoutMiddleware(['permission']);
            Route::post('/bulk-not-open', [InvoicePaymentController::class, 'bulkNotOpen'])->name('invoice-payments.bulk-not-open');
            Route::post('/bulk-pending', [InvoicePaymentController::class, 'bulkPending'])->name('invoice-payments.bulk-pending');
            Route::get('/print/{invoice_number}', [InvoicePaymentController::class, 'printInvoice'])->name('invoice-payments.print');
            Route::post('/bulk-back', [InvoicePaymentController::class, 'bulkBack'])->name('invoice-payments.bulk-back');
        });
    });


    // ================= INVENTORY ROUTES ================= //
    Route::prefix('account/inventory')->group(function () {

        // ================= ITEM CATEGORY ROUTES ================= //
        Route::prefix('item-category')->group(function () {
            Route::get('/',  [ItemCategoryController::class, 'index'])->name('item-category');
            Route::post('store',  [ItemCategoryController::class, 'store'])->name('item-category-store');
            Route::get('edit/{id}',  [ItemCategoryController::class, 'edit'])->name('item-category-edit')->withoutMiddleware(['permission']);
            Route::put('update/{id}',  [ItemCategoryController::class, 'update'])->name('item-category-update');
            Route::delete('delete/{id}',  [ItemCategoryController::class, 'destroy'])->name('delete-item-category');
        });

        // ================= ITEM LIST ROUTES ================= //
        Route::prefix('item-list')->group(function () {
            Route::get('/', [InventoryItemController::class, 'index'])->name('item-list');
            Route::get('item-view/{id}', [InventoryItemController::class, 'view'])->name('item-view');
            Route::get('item-history/{id}', [InventoryItemController::class, 'getItemHistory'])->name('item-history')->withoutMiddleware(['permission']);
            Route::post('item-store', [InventoryItemController::class, 'store'])->name('item-store');
            Route::get('item-edit/{id}', [InventoryItemController::class, 'edit'])->name('item-edit')->withoutMiddleware(['permission']);
            Route::put('item-update/{id}', [InventoryItemController::class, 'update'])->name('item-update');
            Route::delete('item-delete/{id}', [InventoryItemController::class, 'destroy'])->name('item-delete');
        });

        // ================= SUPPLIER ROUTES ================= //
        Route::prefix('supplier')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('supplier');
            Route::post('store', [SupplierController::class, 'store'])->name('supplier-store');
            Route::get('edit/{id}', [SupplierController::class, 'edit'])->name('supplier-edit')->withoutMiddleware(['permission']);
            Route::put('update/{id}', [SupplierController::class, 'update'])->name('supplier-update');
            Route::delete('delete/{id}', [SupplierController::class, 'destroy'])->name('supplier-delete');
        });

        // ================= ITEM RECEIVE ROUTES ================= //
        Route::prefix('item-receive')->group(function () {
            Route::get('/', [ItemReceiveController::class, 'itemReceive'])->name('item-receive');
            Route::get('{transactionId}/view', [ItemReceiveController::class, 'viewItemReceive'])->name('item-receive.view');
            Route::delete('{transactionId}', [ItemReceiveController::class, 'deleteItemReceive'])->name('item-receive.destroy');
            Route::post('/save', [ItemReceiveController::class, 'itemReceivePost'])->name('save-item-receive-data');
            Route::post('/save-invoice-number', [ItemReceiveController::class, 'itemReceivesaveInvoiceNumber'])->name('item-receive.invoic-save');
            Route::get('list', [ItemReceiveController::class, 'itemReceiveList'])->name('item-receive-list');
        });

        // ================= ITEM ISSUE ROUTES ================= //
        Route::prefix('item-issue')->group(function () {
            Route::get('/', [ItemIssueController::class, 'index'])->name('item-issue');
            Route::get('list', [ItemIssueController::class, 'issueListView'])->name('item-issue-list');
            Route::post('save-data', [ItemIssueController::class, 'saveItemIssueData'])->name('save-item-issue-data');
            Route::post('return-item', [ItemIssueController::class, 'returnItem'])->name('return-item');
            Route::post('return-back-item', [ItemIssueController::class, 'ReturnBack'])->name('return-back-item');
            Route::get('get-batch-details/{batchId}', [ItemIssueController::class, 'getBatchDetails'])->name('item-issue.get-batch-details')->withoutMiddleware(['permission']);
            Route::get('check-student-course-status', [ItemIssueController::class, 'checkStudentCourseStatus'])->name('item-issue.check-student-course-status')->withoutMiddleware(['permission']);
        });

        // ================= ITEM SELL ROUTES ================= //
        Route::prefix('item-sell')->group(function () {
            Route::get('/', [ItemSellController::class, 'itemSell'])->name('item-sell');
            Route::post('save', [ItemSellController::class, 'saveItemSellData'])->name('save-item-sell-data');
            Route::get('list', [ItemSellController::class, 'itemSellList'])->name('item-sell-list');
            Route::get('{id}', [ItemSellController::class, 'viewItemSell'])->name('view-item-sell');
            Route::get('{id}/return', [ItemSellController::class, 'returnItemSell'])->name('return-item-sell');
            Route::post('{id}/update-status', [ItemSellController::class, 'updateItemSellStatus'])->name('update-item-sell-status');
            Route::post('{id}/direct-return', [ItemSellController::class, 'directReturnItemSell'])->name('direct-return-item-sell');
            Route::get('{id}/return-view', [ItemSellController::class, 'returnItemSellView'])->name('return-item-sell-view');
            Route::post('save-invoice-number', [ItemSellController::class, 'itemSellSaveInvoiceNumber'])->name('item-sell.invoic-save');
            Route::get('bill/{id}', [ItemSellController::class, 'itemSellBill'])->name('inventory.item-sell-bill');
        });
    });


    // ================= REPORT ROUTES ================= //
    Route::prefix('reports')->group(function () {
        Route::get('course-paid', [ReportController::class, 'coursePaid'])->name('reports.course-paid');
        Route::get('sell-items', [ReportController::class, 'sellItems'])->name('reports.sell-items');
        Route::get('issue-items', [ReportController::class, 'issueItems'])->name('reports.issue-items');
        Route::get('students-paid', [ReportController::class, 'studentsPaid'])->name(name: 'reports.students-paid');
        Route::get('course-dates-payments', [ReportController::class, 'studentsDatesPayments'])->name(name: 'reports.course-dates-payments');
        Route::get('course-intake-payments', [ReportController::class, 'courseIntakePayments'])->name('reports.course-intake-payments');
        Route::get('students-intake-balance', [ReportController::class, 'studentsIntakeBalance'])->name('reports.students-intake-balance');
        Route::get('invoice-payments', [ReportController::class, 'invoicePayments'])->name('reports.invoice-payments');
        Route::get('invoice-payments/data', [ReportController::class, 'getInvoicePaymentsData'])->name('reports.invoice-payments.data')->withoutMiddleware(['permission']);
        Route::get('invoice-payments/export', [ReportController::class, 'exportInvoicePayments'])->name('reports.invoice-payments.export');
        Route::get('invoice-payments/{id}/items', [ReportController::class, 'getReceiptItemsData'])->name('reports.invoice-payments.items')->withoutMiddleware(['permission']);
        Route::get('food-payment-report', [ReportController::class, 'foodPaymentReport'])->name('reports.food-payment');
        Route::get('change-course', [ReportController::class, 'changeCourse'])->name('reports.change-course');
        Route::get('/student-attendance', [ReportController::class, 'studentAttendance'])->name('reports.student-attendance');
        Route::get('/reports/student-attendance/export', [ReportController::class, 'exportCsv'])->name('reports.student-attendance.export');
    });

    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // ================= LOGIN ROUTES =================
    Route::prefix('')->group(function () {
        Route::get('login', [LoginController::class, 'index'])->name('login')->withoutMiddleware(['permission']);
        Route::post('login', [LoginController::class, 'login'])->name('login.post')->withoutMiddleware(['permission']);
        Route::post('logout', [LoginController::class, 'logout'])->name('logout')->withoutMiddleware(['permission']); // Add logout route
    });

    // ================= ROLE MANAGEMENT ROUTES =================
    Route::prefix('role-management')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit')->withoutMiddleware(['permission']);
        Route::get('roles/{id}/grant-permissions', [RoleController::class, 'grantPermission'])->name('roles.grantPermission');
        Route::post('roles/{id}/save-permissions', [RoleController::class, 'savePermissions'])->name('roles.savePermissions');
    });

    // ================ USER MANAGEMENT ROUTES =================
    Route::prefix('role-management')->group(function () {
        Route::get('create-user', [UserController::class, 'createUser'])->name('role-management.create-user');
        Route::post('store-user', [UserController::class, 'storeUser'])->name('role-management.store-user');
        Route::get('user-list', [UserController::class, 'userList'])->name('role-management.user-list');
        Route::get('user-details/{id}', [UserController::class, 'getUserDetails'])->name('role-management.user-details')->withoutMiddleware(['permission']);
        Route::put('user-update/{id}', [UserController::class, 'updateUser'])->name('role-management.user-update');
        Route::post('user-change-password/{id}', [UserController::class, 'changePassword'])->name('role-management.user-change-password');
        Route::get('get-student-details/{id}', [UserController::class, 'getStudentDetails'])->name('role-management.get-student-details')->withoutMiddleware(['permission']);
    });


    // ================= COMMON USED ROUTES ================= //
    Route::prefix('student/student-info')->group(function () {
        Route::get('get-intakes-by-branch/{branchId}', [IntakeController::class, 'getIntakesByBranch'])->name('get-intakes-by-branch')->withoutMiddleware(['permission']);
        Route::get('get-courses-by-intake/{intakeId}', [IntakeController::class, 'getCoursesByIntake'])->name('get-courses-by-intake')->withoutMiddleware(['permission']);
        Route::get('get-courses-by-branch/{branchId}', [CourseController::class, 'getCoursesByBranch'])->name('get-courses-by-branch')->withoutMiddleware(['permission']);
        Route::get('get-courses-by-student/{studentId}', [CourseController::class, 'getCoursesByStudent'])->name('get-courses-by-student')->withoutMiddleware(['permission']);
        Route::get('get-intakes/{branchId}', [IntakeController::class, 'getIntakes'])->name('student.get-intakes')->withoutMiddleware(['permission']);
        Route::get('get-courses/{intakeId}', [IntakeController::class, 'getCourses'])->name('student.get-courses')->withoutMiddleware(['permission']);
    });

    Route::prefix('account/inventory')->group(function () {
        Route::get('get-students-by-branch/{branchId}', [StudentController::class, 'getStudentsByBranch'])->name('item-issue.get-students-by-branch')->withoutMiddleware(['permission']);
        Route::get('get-intakes-by-branch/{branchId}', [IntakeController::class, 'getIntakesByBranch'])->name('item-issue.get-intakes-by-branch')->withoutMiddleware(['permission']);
        Route::get('get-courses-by-intake/{intakeId}', [CourseController::class, 'getCoursesByIntake'])->name('item-issue.get-courses-by-intake')->withoutMiddleware(['permission']);
        Route::get('get-items-by-category/{categoryId}', [BranchController::class, 'getItemsByCategory'])->name('item-issue.get-items-by-category')->withoutMiddleware(['permission']);
        Route::get('get-student-fee-data', [StudentController::class, 'getStudentFeeData'])->name('item-issue.get-student-fee-data')->withoutMiddleware(['permission']);
        Route::get('get-item-price/{id}/{branchId}', [InventoryItemController::class, 'getItemPrice'])->name(name: 'get-item-price')->withoutMiddleware(['permission']);
    });

    Route::prefix('item-issue')->group(function () {
        Route::get('get-student-details/{studentId}', [StudentController::class, 'getStudentDetails'])->name('item-issue.get-student-details')->withoutMiddleware(['permission']);
        Route::get('get-courses-by-intake-and-student/{intakeId}/{studentId}', [CourseController::class, 'getCoursesByIntakeAndStudent'])->name('item-issue.get-courses-by-intake-and-student')->withoutMiddleware(['permission']);
        Route::get('get-intake-by-branch-and-student/{branchId}/{studentId}', [IntakeController::class, 'getIntakeByBranchAndStudent'])->name('item-issue.get-intakes-by-branch-and-student')->withoutMiddleware(['permission']);
    });



    // Training Vehicles - not complete
    Route::prefix('training-vehicles')->group(function () {
        Route::get('/', [TrainingVehicleController::class, 'index'])->name('training-vehicles');
        Route::post('/', [TrainingVehicleController::class, 'store'])->name('training-vehicles.store');
        Route::put('/{vehicle}', [TrainingVehicleController::class, 'update'])->name('training-vehicles.update');
        Route::delete('/{vehicle}', [TrainingVehicleController::class, 'destroy'])->name('training-vehicles.destroy');
    });

});
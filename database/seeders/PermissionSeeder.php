<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'course.index', 'course.store', 'course.update', 'course.edit', 'course.destroy',
            'intake.index', 'intake.store', 'intake.update', 'intake.edit', 'intake.destroy',
            'subject.index', 'subject.store', 'subject.update', 'subject.edit', 'subject.destroy',
            'assign-subject.index', 'assign-subject.store', 'assign-subject.update', 'assign-subject.edit', 'assign-subject.destroy', 'assign-subject.get-courses',
            'lesson.index', 'lesson.store', 'lesson.update', 'lesson.edit', 'lesson.destroy', 'lesson.get-intakes', 'lesson.get-subjects',
            'topic.index', 'topic.store', 'topic.update', 'topic.edit', 'topic.destroy', 'topic.get-intakes', 'topic.get-subjects', 'topic.get-lessons',
            'topic-overview.index', 'topic-overview.store', 'topic-overview.get-intakes', 'topic-overview.get-subjects',
            'student-category.index', 'student-category.store', 'student-category.update', 'student-category.edit', 'student-category.destroy',
            'add-student.index', 'add-student.store', 'add-student.update', 'add-student.edit', 'add-student.destroy',
            'student.add-course.index', 'student.add-course.store', 'student.add-course.edit', 'student.add-course.update', 'student.add-course.destroy',
            'student.add-intake.index', 'student.add-intake-with-courses.store', 'student.add-intake.edit', 'student.add-intake.update', 'student.add-intake.destroy', 'student.add-intake.search', 'student.add-intake.get-intakes', 'student.add-intake.get-courses',
            'dashboard',
            'upload-content', 'study-material-download', 'other-download',
            'shared-content-list', 'video-list', 'download-center.video-list-save', 'download-center.video-list-update', 'download-center.video-list-destroy',
            'student-list', 'student.update-status', 'student-details',
            'disabled-students', 'student-attendance', 'student-export',
            'get-intakes-by-branch', 'get-courses-by-intake',
            'student.get-intakes', 'student.get-courses', 'student-attendance.get-courses', 'student-list.get-intakes', 'student-list.get-courses',
            'frontend.disabled-student.search', 'frontend.student.search',
            'fee-invoice-setting', 'fee-return',
            'item-receive', 'item-receive-list', 'item-receive.edit', 'item-receive.update', 'item-receive.destroy', 'save-item-receive-data',
            'item-sell', 'save-item-sell-data', 'item-sell-list', 'view-item-sell', 'edit-item-sell', 'update-item-sell', 'delete-item-sell-view', 'add-payment-sell', 'item-sell-payments.store', 'view-sell-payments', 'delete-item-sell', 'cancel-item-sell-view', 'cancel-item-sell',
            'item-issue', 'item-issue-list', 'save-item-issue-data', 'item-issue-edit', 'item-issue-update', 'return-item-view', 'return-item', 'issue-item', 'item-issue.get-intakes-by-branch', 'item-issue.get-courses-by-intake', 'item-issue.get-students-by-course', 'item-issue.get-items-by-category', 'item-issue.get-student-fee-data',
            'login', 'login.post', 'recoveryPassword', 'register', 'logout',
            'fee-group', 'fee-group-create', 'fee-group-edit', 'fee-group-update', 'fee-group-delete',
            'fees.course-paid',
            'fees.fees-type', 'fees.fees-type-store', 'fees.fees-type-edit', 'fees.fees-type-update', 'fees.fees-type-delete',
            'fees.fee-invoice-view', 'fees.student-details', 'fees.fees-invoice-payment', 'fees.add-invoice', 'fees.get-student', 'fees.get-types-by-group', 'fees.invoice.store', 'fees.course-payment.store', 'fees.course-payment', 'fees.fee-invoice-edit', 'fees.fee-update-invoice', 'fees.fee-delete', 'fees.update-invoice-number', 'fees.mark-as-paid',
            'fees.fee-invoice', 'fees.fee-return.search',
            'item-category', 'item-category-store', 'item-category-edit', 'item-category-update', 'delete-item-category',
            'item-list', 'item-store', 'item-edit', 'item-update', 'item-delete',
            'supplier', 'supplier-store', 'supplier-edit', 'supplier-update', 'supplier-delete',
            'content-type', 'content-type-store', 'content-type-edit', 'content-type-update', 'content-type-delete',
            'content-list', 'content-store', 'content-edit', 'content-update', 'content-delete', 'content-share-list-save', 'download-center.content-generate-url-save', 'download-center.get-courses-by-intake', 'download-center.save-generated-url-data', 'download-center.shared-content-view', 'download-center.shared-content-delete',
            'return-fees.store',
            'roles.index', 'roles.store', 'roles.update', 'roles.destroy', 'roles.edit', 'roles.grantPermission', 'roles.savePermissions',
            'role-management.create-user', 'role-management.store-user', 'role-management.get-student-details',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['permission_name' => $permission]);
        }
    }
}

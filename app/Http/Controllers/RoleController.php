<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    // Define a mapping for inconsistent permission names
    private $permissionNameMap = [
        // Academic (already consistent, but can be explicitly mapped if needed)
        'course.index' => 'course.index',
        'course.store' => 'course.store',
        'course.update' => 'course.update',
        'course.edit' => 'course.edit',
        'course.destroy' => 'course.destroy',
        'intake.index' => 'intake.index',
        'intake.store' => 'intake.store',
        'intake.update' => 'intake.update',
        'intake.edit' => 'intake.edit',
        'intake.destroy' => 'intake.destroy',
        'subject.index' => 'subject.index',
        'subject.store' => 'subject.store',
        'subject.update' => 'subject.update',
        'subject.edit' => 'subject.edit',
        'subject.destroy' => 'subject.destroy',
        'assign-subject.index' => 'assign-subject.index',
        'assign-subject.store' => 'assign-subject.store',
        'assign-subject.update' => 'assign-subject.update',
        'assign-subject.edit' => 'assign-subject.edit',
        'assign-subject.destroy' => 'assign-subject.destroy',
        'assign-subject.get-courses' => 'assign-subject.get-courses',

        // Lesson Plan (already consistent)
        'lesson.index' => 'lesson.index',
        'lesson.store' => 'lesson.store',
        'lesson.update' => 'lesson.update',
        'lesson.edit' => 'lesson.edit',
        'lesson.destroy' => 'lesson.destroy',
        'lesson.get-intakes' => 'lesson.get-intakes',
        'lesson.get-subjects' => 'lesson.get-subjects',
        'topic.index' => 'topic.index',
        'topic.store' => 'topic.store',
        'topic.update' => 'topic.update',
        'topic.edit' => 'topic.edit',
        'topic.destroy' => 'topic.destroy',
        'topic.get-intakes' => 'topic.get-intakes',
        'topic.get-subjects' => 'topic.get-subjects',
        'topic.get-lessons' => 'topic.get-lessons',
        'topic-overview.index' => 'topic-overview.index',
        'topic-overview.store' => 'topic-overview.store',
        'topic-overview.get-intakes' => 'topic-overview.get-intakes',
        'topic-overview.get-subjects' => 'topic-overview.get-subjects',

        // Download Center
        'upload-content.upload' => 'upload-content', // Special case, action 'upload' maps to 'upload-content'
        'study-material-download.download' => 'study-material-download', // Special case
        'other-download.download' => 'other-download', // Special case
        'shared-content-list.index' => 'shared-content-list',
        'shared-content-list.save' => 'content-share-list-save', // Mapped from route to seeder name
        'shared-content-list.update' => 'download-center.video-list-update', // This is a guess, needs verification against routes/seeder
        'shared-content-list.destroy' => 'download-center.video-list-destroy', // This is a guess, needs verification against routes/seeder
        'shared-content-list.generate-url' => 'download-center.content-generate-url-save',
        'shared-content-list.view' => 'download-center.shared-content-view',
        'shared-content-list.delete-shared' => 'download-center.shared-content-delete',
        'video-list.index' => 'video-list',
        'video-list.save' => 'download-center.video-list-save',
        'video-list.update' => 'download-center.video-list-update',
        'video-list.destroy' => 'download-center.video-list-destroy',

        // Student Management
        'student-category.index' => 'student-category.index',
        'student-category.store' => 'student-category.store',
        'student-category.update' => 'student-category.update',
        'student-category.edit' => 'student-category.edit',
        'student-category.destroy' => 'student-category.destroy',
        'add-student.index' => 'add-student.index',
        'add-student.store' => 'add-student.store',
        'add-student.update' => 'add-student.update',
        'add-student.edit' => 'add-student.edit',
        'add-student.destroy' => 'add-student.destroy',
        'student.add-course.index' => 'student.add-course.index',
        'student.add-course.store' => 'student.add-course.store',
        'student.add-course.edit' => 'student.add-course.edit',
        'student.add-course.update' => 'student.add-course.update',
        'student.add-course.destroy' => 'student.add-course.destroy',
        'student.add-intake.index' => 'student.add-intake.index',
        'student.add-intake.store' => 'student.add-intake-with-courses.store', // Mismatch in seeder/route
        'student.add-intake.edit' => 'student.add-intake.edit',
        'student.add-intake.update' => 'student.add-intake.update',
        'student.add-intake.destroy' => 'student.add-intake.destroy',
        'student.add-intake.search' => 'student.add-intake.search',
        'student.add-intake.get-intakes' => 'student.add-intake.get-intakes',
        'student.add-intake.get-courses' => 'student.add-intake.get-courses',
        'student-list.index' => 'student-list', // Special case
        'student-list.update-status' => 'student.update-status',
        'student-list.details' => 'student-details',
        'student-list.export' => 'student-export',
        'student-list.get-intakes' => 'student-list.get-intakes',
        'student-list.get-courses' => 'student-list.get-courses',
        'disabled-students.index' => 'disabled-students', // Special case
        'disabled-students.search' => 'frontend.disabled-student.search', // Mismatch
        'student-attendance.index' => 'student-attendance', // Special case
        'student-attendance.get-courses' => 'student-attendance.get-courses',

        // Inventory Management
        'item-category.index' => 'item-category',
        'item-category.store' => 'item-category-store',
        'item-category.edit' => 'item-category-edit',
        'item-category.update' => 'item-category-update',
        'item-category.delete' => 'delete-item-category',
        'item-list.index' => 'item-list',
        'item-list.store' => 'item-store',
        'item-list.edit' => 'item-edit',
        'item-list.update' => 'item-update',
        'item-list.delete' => 'item-delete',
        'supplier.index' => 'supplier',
        'supplier.store' => 'supplier-store',
        'supplier.edit' => 'supplier-edit',
        'supplier.update' => 'supplier-update',
        'supplier.delete' => 'supplier-delete',
        'item-receive.index' => 'item-receive',
        'item-receive.store' => 'save-item-receive-data', // Mismatch
        'item-receive.edit' => 'item-receive.edit',
        'item-receive.update' => 'item-receive.update',
        'item-receive.destroy' => 'item-receive.destroy',
        'item-sell.index' => 'item-sell',
        'item-sell.store' => 'save-item-sell-data', // Mismatch
        'item-sell.view' => 'view-item-sell',
        'item-sell.edit' => 'edit-item-sell',
        'item-sell.update' => 'update-item-sell',
        'item-sell.delete' => 'delete-item-sell', // Mismatch
        'item-sell.cancel' => 'cancel-item-sell', // Mismatch
        'item-sell.add-payment' => 'add-payment-sell', // Mismatch
        'item-sell.view-payments' => 'view-sell-payments', // Mismatch
        'item-issue.index' => 'item-issue',
        'item-issue.store' => 'save-item-issue-data', // Mismatch
        'item-issue.edit' => 'item-issue-edit',
        'item-issue.update' => 'item-issue-update',
        'item-issue.return' => 'return-item', // Mismatch
        'item-issue.issue' => 'issue-item',
        'item-issue.get-intakes' => 'item-issue.get-intakes-by-branch', // Mismatch
        'item-issue.get-courses' => 'item-issue.get-courses-by-intake', // Mismatch
        'item-issue.get-students' => 'item-issue.get-students-by-course', // Mismatch
        'item-issue.get-items' => 'item-issue.get-items-by-category', // Mismatch
        'item-issue.get-student-fee-data' => 'item-issue.get-student-fee-data', // Mismatch

        // FEE
        'fee-group.index' => 'fee-group',
        'fee-group.create' => 'fee-group-create',
        'fee-group.edit' => 'fee-group-edit',
        'fee-group.update' => 'fee-group-update',
        'fee-group.delete' => 'fee-group-delete',
        'fees.fees-type.index' => 'fees.fees-type',
        'fees.fees-type.store' => 'fees.fees-type-store',
        'fees.fees-type.edit' => 'fees.fees-type-edit',
        'fees.fees-type.update' => 'fees.fees-type-update',
        'fees.fees-type.delete' => 'fees.fees-type-delete',
        'fees.fee-invoice.index' => 'fees.fee-invoice',
        'fees.fee-invoice.view' => 'fees.fee-invoice-view',
        'fees.fee-invoice.payment' => 'fees.fees-invoice-payment',
        'fees.fee-invoice.add' => 'fees.add-invoice',
        'fees.fee-invoice.get-student' => 'fees.get-student',
        'fees.fee-invoice.get-types' => 'fees.get-types-by-group',
        'fees.fee-invoice.store' => 'fees.invoice.store',
        'fees.fee-invoice.course-payment' => 'fees.course-payment.store',
        'fees.fee-invoice.edit' => 'fees.fee-invoice-edit',
        'fees.fee-invoice.update' => 'fees.fee-update-invoice',
        'fees.fee-invoice.delete' => 'fees.fee-delete',
        'fees.fee-invoice.update-invoice-number' => 'fees.update-invoice-number',
        'fees.fee-invoice.mark-as-paid' => 'fees.mark-as-paid',
        'fee-return.index' => 'fee-return',
        'fee-return.store' => 'return-fees.store',
        'fee-return.search' => 'fees.fee-return.search',
        'fees.course-paid.index' => 'fees.course-paid',
        'fee-invoice-setting.index' => 'fee-invoice-setting',

        // Role Management (already consistent)
        'roles.index' => 'roles.index',
        'roles.store' => 'roles.store',
        'roles.update' => 'roles.update',
        'roles.destroy' => 'roles.destroy',
        'roles.edit' => 'roles.edit',
        'roles.grantPermission' => 'roles.grantPermission',
        'roles.savePermissions' => 'roles.savePermissions',
        'role-management.create-user.create' => 'role-management.create-user', // Mismatch
        'role-management.create-user.store' => 'role-management.store-user', // Mismatch
        'role-management.create-user.get-student-details' => 'role-management.get-student-details', // Mismatch
    ];

    public function index()
    {
        $roles = Role::all();
        return view('pages.role_management.roles', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,role_name|max:255',
        ]);

        try {
            $role = new Role();
            $role->role_name = $request->name;
            $role->save();

            return response()->json(['message' => 'Role added successfully', 'role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Operation Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $editRole = Role::findOrFail($id);
        $roles = Role::all();
        return view('pages.role_management.roles', compact('editRole', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,role_name,' . $id . '|max:255',
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->role_name = $request->name;
            $role->save();

            return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Operation Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Role::destroy($id);
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Operation Failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function grantPermission($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $allPermissions = Permission::all();

        $menuStructure = $this->buildMenuStructure($allPermissions, $rolePermissions);

        return view('pages.role_management.grantpermission', compact(
            'role',
            'menuStructure',
            'rolePermissions'
        ));
    }

    private function buildMenuStructure($allPermissions, $rolePermissions)
    {
        $permissionsData = [
            // Academic
            ['name' => 'Courses', 'route' => 'course', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Intakes', 'route' => 'intake', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Subjects', 'route' => 'subject', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Assign Subjects', 'route' => 'assign-subject', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-courses']],

            // Lesson Plan
            ['name' => 'Lessons', 'route' => 'lesson', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-intakes', 'get-subjects']],
            ['name' => 'Topics', 'route' => 'topic', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-intakes', 'get-subjects', 'get-lessons']],
            ['name' => 'Topic Overview', 'route' => 'topic-overview', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'get-intakes', 'get-subjects']],

            // Download Center
            ['name' => 'Upload Content', 'route' => 'upload-content', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['upload']],
            ['name' => 'Study Material Download', 'route' => 'study-material-download', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['download']],
            ['name' => 'Other Download', 'route' => 'other-download', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['download']],
            ['name' => 'Shared Content List', 'route' => 'shared-content-list', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['index', 'save', 'update', 'destroy', 'generate-url', 'view', 'delete-shared']],
            ['name' => 'Video List', 'route' => 'video-list', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['index', 'save', 'update', 'destroy']],

            // Student Management
            ['name' => 'Student Categories', 'route' => 'student-category', 'category' => 'Student Management', 'sub_category' => 'Student Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Add Student', 'route' => 'add-student', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Student Add Course', 'route' => 'student.add-course', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'edit', 'update', 'destroy']],
            ['name' => 'Student Add Intake', 'route' => 'student.add-intake', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'edit', 'update', 'destroy', 'search', 'get-intakes', 'get-courses']],
            ['name' => 'Student List', 'route' => 'student-list', 'category' => 'Student Management', 'sub_category' => 'Student Information', 'actions' => ['index', 'update-status', 'details', 'export', 'get-intakes', 'get-courses']],
            ['name' => 'Disabled Students', 'route' => 'disabled-students', 'category' => 'Student Management', 'sub_category' => 'Student Information', 'actions' => ['index', 'search']],
            ['name' => 'Student Attendance', 'route' => 'student-attendance', 'category' => 'Student Management', 'sub_category' => 'Student Information', 'actions' => ['index', 'get-courses']],

            // Inventory Management
            ['name' => 'Item Categories', 'route' => 'item-category', 'category' => 'Inventory Management', 'sub_category' => 'Item Settings', 'actions' => ['index', 'store', 'edit', 'update', 'delete']],
            ['name' => 'Item List', 'route' => 'item-list', 'category' => 'Inventory Management', 'sub_category' => 'Item Settings', 'actions' => ['index', 'store', 'edit', 'update', 'delete']],
            ['name' => 'Suppliers', 'route' => 'supplier', 'category' => 'Inventory Management', 'sub_category' => 'Item Settings', 'actions' => ['index', 'store', 'edit', 'update', 'delete']],
            ['name' => 'Item Receive', 'route' => 'item-receive', 'category' => 'Inventory Management', 'sub_category' => 'Inventory Transactions', 'actions' => ['index', 'store', 'edit', 'update', 'destroy']],
            ['name' => 'Item Sell', 'route' => 'item-sell', 'category' => 'Inventory Management', 'sub_category' => 'Inventory Transactions', 'actions' => ['index', 'store', 'view', 'edit', 'update', 'delete', 'cancel', 'add-payment', 'view-payments']],
            ['name' => 'Item Issue', 'route' => 'item-issue', 'category' => 'Inventory Management', 'sub_category' => 'Inventory Transactions', 'actions' => ['index', 'store', 'edit', 'update', 'return', 'issue', 'get-intakes', 'get-courses', 'get-students', 'get-items', 'get-student-fee-data']],

            // FEE
            ['name' => 'Fee Group', 'route' => 'fee-group', 'category' => 'FEE', 'sub_category' => 'Fee Settings', 'actions' => ['index', 'create', 'edit', 'update', 'delete']],
            ['name' => 'Fee Type', 'route' => 'fees.fees-type', 'category' => 'FEE', 'sub_category' => 'Fee Settings', 'actions' => ['index', 'store', 'edit', 'update', 'delete']],
            ['name' => 'Fee Invoice', 'route' => 'fees.fee-invoice', 'category' => 'FEE', 'sub_category' => 'Fee Transactions', 'actions' => ['index', 'view', 'payment', 'add', 'get-student', 'get-types', 'store', 'course-payment', 'edit', 'update', 'delete', 'update-invoice-number', 'mark-as-paid']],
            ['name' => 'Fee Return', 'route' => 'fee-return', 'category' => 'FEE', 'sub_category' => 'Fee Transactions', 'actions' => ['index', 'store', 'search']],
            ['name' => 'Course Paid', 'route' => 'fees.course-paid', 'category' => 'FEE', 'sub_category' => 'Fee Transactions', 'actions' => ['index']],
            ['name' => 'Fee Invoice Setting', 'route' => 'fee-invoice-setting', 'category' => 'FEE', 'sub_category' => 'Fee Settings', 'actions' => ['index']],

            // Role Management
            ['name' => 'Roles', 'route' => 'roles', 'category' => 'Role Management', 'sub_category' => 'Role Settings', 'actions' => ['index', 'store', 'update', 'destroy', 'edit', 'grantPermission', 'savePermissions']],
            ['name' => 'Create User', 'route' => 'role-management.create-user', 'category' => 'Role Management', 'sub_category' => 'User Management', 'actions' => ['create', 'store', 'get-student-details']],
        ];

        // Create a map of permission names to permission objects for quick lookup
        $permissionsMap = [];
        foreach ($allPermissions as $permission) {
            $permissionsMap[$permission->permission_name] = $permission;
        }

        $menuStructure = [];

        foreach ($permissionsData as $item) {
            $categoryName = $item['category'];
            $itemName = $item['name'];
            $baseRoute = $item['route'];
            $actions = $item['actions'];

            // Find or create category
            $categoryIndex = $this->findCategoryIndex($menuStructure, $categoryName);
            if ($categoryIndex === -1) {
                $menuStructure[] = [
                    'name' => $categoryName,
                    'items' => []
                ];
                $categoryIndex = count($menuStructure) - 1;
            }

            // Find or create item within category
            $itemIndex = $this->findItemIndex($menuStructure[$categoryIndex]['items'], $itemName);
            if ($itemIndex === -1) {
                $menuStructure[$categoryIndex]['items'][] = [
                    'name' => $itemName,
                    'permissions' => []
                ];
                $itemIndex = count($menuStructure[$categoryIndex]['items']) - 1;
            }

            // Add permissions for each action
            foreach ($actions as $action) {
                // Generate the permission name based on route and action
                $permissionName = $this->generatePermissionName($baseRoute, $action);

                // Check if this permission exists in the database
                if (isset($permissionsMap[$permissionName])) {
                    $permission = $permissionsMap[$permissionName];

                    $menuStructure[$categoryIndex]['items'][$itemIndex]['permissions'][] = [
                        'id' => $permission->id,
                        'name' => $itemName . ' - ' . ucfirst($action),
                        'permission_name' => $permission->permission_name,
                        'has_permission' => in_array($permission->id, $rolePermissions),
                    ];
                }
            }
        }

        // Sort the structure
        return $this->sortMenuStructure($menuStructure);
    }

    private function findCategoryIndex($menuStructure, $categoryName)
    {
        foreach ($menuStructure as $index => $category) {
            if ($category['name'] === $categoryName) {
                return $index;
            }
        }
        return -1;
    }

    private function findItemIndex($items, $itemName)
    {
        foreach ($items as $index => $item) {
            if ($item['name'] === $itemName) {
                return $index;
            }
        }
        return -1;
    }

    private function generatePermissionName($baseRoute, $action)
    {
        $combinedRouteAction = $baseRoute . '.' . $action;

        // Check if a specific mapping exists for the combined route and action
        if (isset($this->permissionNameMap[$combinedRouteAction])) {
            return $this->permissionNameMap[$combinedRouteAction];
        }

       
        // Default pattern: baseRoute.action
        return $combinedRouteAction;
    }

    private function sortMenuStructure($menuStructure)
    {
        // Sort categories alphabetically
        usort($menuStructure, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Sort items within each category
        foreach ($menuStructure as &$category) {
            usort($category['items'], function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });

            // Sort permissions within each item
            foreach ($category['items'] as &$item) {
                usort($item['permissions'], function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
            }
        }

        return $menuStructure;
    }

    public function savePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::findOrFail($id);
            $selectedPermissions = $request->input('permissions', []);

            // Debug: Log what permissions are being sent
            Log::info('Saving permissions for role: ' . $role->role_name, [
                'role_id' => $id,
                'selected_permissions' => $selectedPermissions
            ]);

            $role->permissions()->sync($selectedPermissions);

            return redirect()->back()->with('success', 'Permissions updated successfully for ' . $role->role_name);
        } catch (\Exception $e) {
            Log::error('Failed to save permissions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}

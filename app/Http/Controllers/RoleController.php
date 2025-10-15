<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
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
        $role = Role::findOrFail($id);
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $allPermissions = Permission::all();
        $menuStructure = [];

        $permissionsData = [
            // Academic
            ['name' => 'Courses', 'route' => 'course.index', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Intakes', 'route' => 'intake.index', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Subjects', 'route' => 'subject.index', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Assign Subjects', 'route' => 'assign-subject.index', 'category' => 'Academic', 'sub_category' => 'Course Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-courses']],

            // Lesson Plan
            ['name' => 'Lessons', 'route' => 'lesson.index', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-intakes', 'get-subjects']],
            ['name' => 'Topics', 'route' => 'topic.index', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'update', 'edit', 'destroy', 'get-intakes', 'get-subjects', 'get-lessons']],
            ['name' => 'Topic Overview', 'route' => 'topic-overview.index', 'category' => 'Lesson Plan', 'sub_category' => 'Lesson Management', 'actions' => ['index', 'store', 'get-intakes', 'get-subjects']],

            // Download Center
            ['name' => 'Upload Content', 'route' => 'upload-content', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['upload']],
            ['name' => 'Study Material Download', 'route' => 'study-material-download', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['download']],
            ['name' => 'Other Download', 'route' => 'other-download', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['download']],
            ['name' => 'Shared Content List', 'route' => 'shared-content-list', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['index', 'save', 'update', 'destroy', 'generate-url', 'view', 'delete-shared']],
            ['name' => 'Video List', 'route' => 'video-list', 'category' => 'Download Center', 'sub_category' => 'Content Management', 'actions' => ['index', 'save', 'update', 'destroy']],

            // Student Management
            ['name' => 'Student Categories', 'route' => 'student-category.index', 'category' => 'Student Management', 'sub_category' => 'Student Settings', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Add Student', 'route' => 'add-student.index', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'update', 'edit', 'destroy']],
            ['name' => 'Student Add Course', 'route' => 'student.add-course.index', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'edit', 'update', 'destroy']],
            ['name' => 'Student Add Intake', 'route' => 'student.add-intake.index', 'category' => 'Student Management', 'sub_category' => 'Student Enrollment', 'actions' => ['index', 'store', 'edit', 'update', 'destroy', 'search', 'get-intakes', 'get-courses']],
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
            ['name' => 'Roles', 'route' => 'roles.index', 'category' => 'Role Management', 'sub_category' => 'Role Settings', 'actions' => ['index', 'store', 'update', 'destroy', 'edit', 'grantPermission', 'savePermissions']],
            ['name' => 'Create User', 'route' => 'role-management.create-user', 'category' => 'Role Management', 'sub_category' => 'User Management', 'actions' => ['create', 'store', 'get-student-details']],
        ];

        $dbPermissionsMap = [];
        foreach ($allPermissions as $p) {
            $dbPermissionsMap[$p->permission_name] = $p;
        }

        $newMenuStructure = [];

        // Helper function to determine the actual permission name
        $getActualPermissionName = function($baseRoute, $action) {
            // Routes that are standalone permission names (action is just a descriptor)
            $standaloneRoutes = [
                'upload-content', 'study-material-download', 'other-download',
                'shared-content-list', 'video-list', 'student-list', 'disabled-students',
                'student-attendance', 'item-category', 'item-list', 'supplier',
                'item-receive', 'item-sell', 'item-issue', 'fee-group', 'fees.fees-type',
                'fees.fee-invoice', 'fee-return', 'fees.course-paid', 'fee-invoice-setting',
                'roles.index', 'role-management.create-user'
            ];

            if (in_array($baseRoute, $standaloneRoutes)) {
                return $baseRoute;
            }

            // For routes like 'course.index', 'intake.index', etc., the action replaces the last part
            if (str_contains($baseRoute, '.')) {
                $parts = explode('.', $baseRoute);
                array_pop($parts); // Remove 'index'
                $parts[] = $action; // Add 'store', 'update', etc.
                return implode('.', $parts);
            }

            // Default case: baseRoute.action (e.g., if baseRoute is 'some-feature' and action is 'view', then 'some-feature.view')
            return $baseRoute . '.' . $action;
        };

        foreach ($permissionsData as $item) {
            $categoryName = $item['category'];
            $subCategoryName = $item['sub_category'];
            $baseRoute = $item['route'];
            $displayPermissionName = $item['name'];
            $actions = $item['actions'];

            // Ensure category exists
            if (!isset($newMenuStructure[$categoryName])) {
                $newMenuStructure[$categoryName] = [
                    'name' => $categoryName,
                    'sub_categories' => []
                ];
            }

            // Ensure sub-category exists
            if (!isset($newMenuStructure[$categoryName]['sub_categories'][$subCategoryName])) {
                $newMenuStructure[$categoryName]['sub_categories'][$subCategoryName] = [
                    'name' => $subCategoryName,
                    'items' => [] // New level for items within sub-category
                ];
            }

            // Ensure item (sub-sub-category) exists
            if (!isset($newMenuStructure[$categoryName]['sub_categories'][$subCategoryName]['items'][$displayPermissionName])) {
                $newMenuStructure[$categoryName]['sub_categories'][$subCategoryName]['items'][$displayPermissionName] = [
                    'name' => $displayPermissionName,
                    'permissions' => []
                ];
            }

            foreach ($actions as $action) {
                $actualPermissionName = $getActualPermissionName($baseRoute, $action);

                // Find this permission in $dbPermissionsMap
                if (isset($dbPermissionsMap[$actualPermissionName])) {
                    $permission = $dbPermissionsMap[$actualPermissionName];
                    $newMenuStructure[$categoryName]['sub_categories'][$subCategoryName]['items'][$displayPermissionName]['permissions'][] = [
                        'id' => $permission->id,
                        'name' => $displayPermissionName . ' (' . ucfirst($action) . ')', // e.g., "Courses (Index)"
                        'permission_name' => $permission->permission_name, // The actual permission name from DB
                        'has_permission' => in_array($permission->id, $rolePermissions),
                    ];
                }
            }
        }

        // Convert associative arrays to indexed arrays for JSON output and sorting
        $menuStructure = [];
        foreach ($newMenuStructure as $category) {
            $subCategories = [];
            foreach ($category['sub_categories'] as $subCategory) {
                $items = [];
                foreach ($subCategory['items'] as $item) {
                    $items[] = $item;
                }
                $subCategory['items'] = $items; // Convert items to indexed array
                $subCategories[] = $subCategory;
            }
            $category['sub_categories'] = $subCategories;
            $menuStructure[] = $category;
        }

        // Sort categories, sub-categories, items, and permissions
        usort($menuStructure, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        foreach ($menuStructure as &$category) {
            usort($category['sub_categories'], function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            foreach ($category['sub_categories'] as &$subCategory) {
                usort($subCategory['items'], function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                foreach ($subCategory['items'] as &$item) {
                    usort($item['permissions'], function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                }
            }
        }

        return view('pages.role_management.grantpermission', compact('role', 'menuStructure', 'allPermissions', 'rolePermissions'));
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
            $role->permissions()->sync($selectedPermissions);

            return redirect()->back()->with('success', 'Permissions updated successfully for ' . $role->role_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}

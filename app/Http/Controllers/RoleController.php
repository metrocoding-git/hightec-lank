<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission; // Import the Permission model
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
        $roles = Role::all(); // For displaying the list alongside the edit form
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

        // Fetch pivot data for the role
        $rolePermissionsData = $role->permissions()->withPivot('view', 'add', 'edit', 'delete')->get();

        // Transform rolePermissionsData into an easily consumable format for the view
        $rolePermissionsMap = [];
        foreach ($rolePermissionsData as $permission) {
            $rolePermissionsMap[$permission->pivot->permission_id] = [
                'view' => $permission->pivot->view,
                'add' => $permission->pivot->add,
                'edit' => $permission->pivot->edit,
                'delete' => $permission->pivot->delete,
            ];
        }

        // Manually define menu structure based on side-menu.blade.php
        $menuStructure = [
            [
                'name' => 'Academic',
                'sub_items' => [
                    ['name' => 'Courses', 'route' => '/administration/academic/courses'],
                    ['name' => 'Intake', 'route' => '/administration/academic/intake'],
                    ['name' => 'Subjects', 'route' => '/administration/academic/subjects'],
                    ['name' => 'Assign Subject', 'route' => '/administration/academic/assign-subject'],
                ]
            ],
            [
                'name' => 'Lesson Plane',
                'sub_items' => [
                    ['name' => 'Lesson', 'route' => '/administration/lesson-plan/lesson'],
                    ['name' => 'Topic', 'route' => '/administration/lesson-plan/topic'],
                    ['name' => 'Topic Overview', 'route' => '/administration/lesson-plan/topic-overview'],
                ]
            ],
            [
                'name' => 'Download Center',
                'sub_items' => [
                    ['name' => 'Content-Type', 'route' => '/administration/download-center/content-type'],
                    ['name' => 'Content List', 'route' => '/administration/download-center/content-list'],
                    ['name' => 'Shared Content List', 'route' => '/administration/download-center/shared-content-list'],
                    ['name' => 'Video List', 'route' => '/administration/download-center/video-list'],
                ]
            ],
            [
                'name' => 'Student Info',
                'sub_items' => [
                    ['name' => 'Student Category', 'route' => '/student/student-info/student-category'],
                    ['name' => 'Add Student', 'route' => '/student/student-info/add-student'],
                    ['name' => 'Student List', 'route' => '/student/student-info/student-list'],
                    ['name' => 'Disabled Students', 'route' => '/student/student-info/disabled-student'],
                    ['name' => 'Student Attendance', 'route' => '/student/student-info/student-attendance'],
                    ['name' => 'Add Intake & Courses', 'route' => '/student/student-info/student-add-intake'],
                ]
            ],
            [
                'name' => 'Fees',
                'sub_items' => [
                    ['name' => 'Fee Group', 'route' => '/student/fees/fee-group'],
                    ['name' => 'Fee Type', 'route' => '/student/fees/fee-type'],
                    ['name' => 'Add Invoice', 'route' => '/student/fees/add-invoice'],
                    ['name' => 'Course Paid', 'route' => '/student/fees/course-paid'],
                    ['name' => 'Fee Invoice', 'route' => '/student/fees/fee-invoice'],
                    ['name' => 'Fee Invoice Setting', 'route' => '/student/fees/fee-invoice-setting'],
                    ['name' => 'Fee Return', 'route' => '/student/fees/fee-return'],
                ]
            ],
            [
                'name' => 'Inventory',
                'sub_items' => [
                    ['name' => 'Item Category', 'route' => '/account/inventory/item-category'],
                    ['name' => 'Item List', 'route' => '/account/inventory/item-list'],
                    ['name' => 'Supplier', 'route' => '/account/inventory/supplier'],
                    ['name' => 'Item Receive', 'route' => '/account/inventory/item-receive'],
                    ['name' => 'Item Receive List', 'route' => '/account/inventory/item-receive-list'],
                    ['name' => 'Item Issue', 'route' => '/account/inventory/item-issue'],
                    ['name' => 'Item Issue List', 'route' => '/account/inventory/item-issue-list'],
                    ['name' => 'Item Sell', 'route' => '/account/inventory/item-sell'],
                    ['name' => 'Item Sell List', 'route' => '/account/inventory/item-sell-list'],
                ]
            ],
            [
                'name' => 'Role Management',
                'sub_items' => [
                    ['name' => 'Roles', 'route' => '/role-management/roles'],
                ]
            ],
        ];

        // Ensure permissions exist for each route and add permission_id to menuStructure
        $allPermissions = collect(); // Initialize as a collection
        foreach ($menuStructure as &$mainItem) {
            foreach ($mainItem['sub_items'] as &$subItem) {
                $permission = Permission::firstOrCreate(['permission_name' => $subItem['route']]);
                $subItem['permission_id'] = $permission->id;
                $allPermissions->push($permission);
            }
        }
        $allPermissions = $allPermissions->keyBy('id'); // Key by ID for easy lookup in the view


        return view('pages.role_management.grantpermission', compact('role', 'allPermissions', 'rolePermissionsMap', 'menuStructure'));
    }

    public function savePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        try {
            $role = Role::findOrFail($id);
            $permissionsToSync = [];

            $permissionsToSync = [];

            // Get existing permissions for the role
            $existingRolePermissions = $role->permissions()->withPivot('view', 'add', 'edit', 'delete')->get();

            // Populate permissionsToSync with existing permissions first
            foreach ($existingRolePermissions as $permission) {
                $permissionsToSync[$permission->pivot->permission_id] = [
                    'view' => $permission->pivot->view,
                    'add' => $permission->pivot->add,
                    'edit' => $permission->pivot->edit,
                    'delete' => $permission->pivot->delete,
                ];
            }

            // Now, update the permissions for the specific resource submitted in the request
            if ($request->has('permissions')) {
                foreach ($request->input('permissions') as $resourcePermissionId => $permissionActions) {
                    // Ensure the resourcePermissionId exists in our overall permissions list
                    // If it's a new permission being granted for a resource not previously associated,
                    // we need to initialize its actions to 0 first before applying the submitted ones.
                    if (!isset($permissionsToSync[$resourcePermissionId])) {
                        $permissionsToSync[$resourcePermissionId] = [
                            'view' => 0,
                            'add' => 0,
                            'edit' => 0,
                            'delete' => 0,
                        ];
                    }

                    foreach ($permissionActions as $actionName => $value) {
                        // $value will be '1' if checked, so we set the flag to 1
                        $permissionsToSync[$resourcePermissionId][$actionName] = (int) $value;
                    }
                }
            }

          
            $role->permissions()->sync($permissionsToSync);


            return redirect()->back()->with('success', 'Permissions updated successfully for ' . $role->role_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}

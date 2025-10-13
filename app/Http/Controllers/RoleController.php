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

        foreach ($allPermissions as $permission) {
            $mainCategoryName = 'General';
            $subItemName = $permission->permission_name;

            if (str_starts_with($permission->permission_name, 'administration/academic/')) {
                $mainCategoryName = 'Academic Administration';
                $subItemName = str_replace('administration/academic/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'administration/lesson-plan/')) {
                $mainCategoryName = 'Lesson Plan Administration';
                $subItemName = str_replace('administration/lesson-plan/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'administration/download-center/')) {
                $mainCategoryName = 'Download Center Administration';
                $subItemName = str_replace('administration/download-center/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'student/student-info/')) {
                $mainCategoryName = 'Student Information';
                $subItemName = str_replace('student/student-info/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'student/fees/')) {
                $mainCategoryName = 'Student Fees';
                $subItemName = str_replace('student/fees/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'account/inventory/')) {
                $mainCategoryName = 'Inventory Management';
                $subItemName = str_replace('account/inventory/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'role-management/')) {
                $mainCategoryName = 'Role & User Management';
                $subItemName = str_replace('role-management/', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'fees.')) {
                $mainCategoryName = 'Fees';
                $subItemName = str_replace('fees.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'item-issue.')) {
                $mainCategoryName = 'Item Issue';
                $subItemName = str_replace('item-issue.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'item-sell.')) {
                $mainCategoryName = 'Item Sell';
                $subItemName = str_replace('item-sell.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'course.')) {
                $mainCategoryName = 'Courses';
                $subItemName = str_replace('course.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'intake.')) {
                $mainCategoryName = 'Intakes';
                $subItemName = str_replace('intake.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'subject.')) {
                $mainCategoryName = 'Subjects';
                $subItemName = str_replace('subject.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'assign-subject.')) {
                $mainCategoryName = 'Assign Subjects';
                $subItemName = str_replace('assign-subject.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'lesson.')) {
                $mainCategoryName = 'Lessons';
                $subItemName = str_replace('lesson.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'topic.')) {
                $mainCategoryName = 'Topics';
                $subItemName = str_replace('topic.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'topic-overview.')) {
                $mainCategoryName = 'Topic Overview';
                $subItemName = str_replace('topic-overview.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'student-category.')) {
                $mainCategoryName = 'Student Categories';
                $subItemName = str_replace('student-category.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'add-student.')) {
                $mainCategoryName = 'Add Student';
                $subItemName = str_replace('add-student.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'download-center.')) {
                $mainCategoryName = 'Download Center';
                $subItemName = str_replace('download-center.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'item-category')) {
                $mainCategoryName = 'Item Category';
                $subItemName = str_replace('item-category', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'item-list')) {
                $mainCategoryName = 'Item List';
                $subItemName = str_replace('item-list', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'supplier')) {
                $mainCategoryName = 'Supplier';
                $subItemName = str_replace('supplier', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'content-type')) {
                $mainCategoryName = 'Content Type';
                $subItemName = str_replace('content-type', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'content-list')) {
                $mainCategoryName = 'Content List';
                $subItemName = str_replace('content-list', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'return-fees.')) {
                $mainCategoryName = 'Return Fees';
                $subItemName = str_replace('return-fees.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'roles.')) {
                $mainCategoryName = 'Roles';
                $subItemName = str_replace('roles.', '', $permission->permission_name);
            } elseif (str_starts_with($permission->permission_name, 'role-management.')) {
                $mainCategoryName = 'Role Management';
                $subItemName = str_replace('role-management.', '', $permission->permission_name);
            }
            
            $subItemName = trim(str_replace(['.', '-'], ' ', $subItemName));
            $subItemName = ucwords($subItemName);

            if (empty($subItemName)) {
                $subItemName = $permission->permission_name;
            }

            $mainCategoryKey = array_search($mainCategoryName, array_column($menuStructure, 'name'));
            if ($mainCategoryKey === false) {
                $menuStructure[] = [
                    'name' => $mainCategoryName,
                    'sub_items' => []
                ];
                $mainCategoryKey = count($menuStructure) - 1;
            }

            $menuStructure[$mainCategoryKey]['sub_items'][] = [
                'name' => $subItemName,
                'permission_id' => $permission->id,
                'permission_name' => $permission->permission_name,
                'has_permission' => in_array($permission->id, $rolePermissions),
            ];
        }

        usort($menuStructure, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        foreach ($menuStructure as &$mainItem) {
            usort($mainItem['sub_items'], function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
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

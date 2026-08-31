<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $permissionsMap = [];
        foreach ($allPermissions as $permission) {
            $permissionsMap[$permission->permission_name] = $permission;
        }

        $categories = config('permissions.categories', []);
        $menuStructure = [];

        foreach ($categories as $category) {
            $items = [];

            foreach ($category['windows'] as $window) {
                $permissions = [];

                $w = $window['window'];
                if (($w['key'] ?? null) !== null
                    && ($w['built'] ?? true) !== false
                    && isset($permissionsMap[$w['key']])
                ) {
                    $permissions[] = [
                        'id'    => $permissionsMap[$w['key']]->id,
                        'name'  => $w['key'],
                        'label' => $w['label'],
                    ];
                }

                foreach ($window['buttons'] as $button) {
                    if (($button['built'] ?? true) === false) {
                        continue;
                    }
                    if (!isset($permissionsMap[$button['key']])) {
                        continue;
                    }
                    $permissions[] = [
                        'id'    => $permissionsMap[$button['key']]->id,
                        'name'  => $button['key'],
                        'label' => $button['label'],
                    ];
                }

                // Skip a window row entirely if it has nothing grantable yet
                // (e.g. every one of its keys is 'built' => false, or none
                // have been seeded into the DB yet).
                if (empty($permissions)) {
                    continue;
                }

                $items[] = [
                    'name'        => $window['name'],
                    'permissions' => $permissions,
                ];
            }

            // Skip a whole category if none of its windows had anything
            // grantable (e.g. "Training Vehicles" before you've confirmed
            // and seeded it).
            if (empty($items)) {
                continue;
            }

            $menuStructure[] = [
                'name'  => $category['name'],
                'items' => $items,
            ];
        }

        // Deliberately NOT re-sorted — config/permissions.php's order already
        // matches the sidebar (Dashboard, Academic, Lesson Plan, ...), so the
        // Grant Permissions screen reads in the same order admins already
        // know from the menu.
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

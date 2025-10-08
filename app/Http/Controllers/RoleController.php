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
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('pages.role_management.grantpermission', compact('role', 'permissions', 'rolePermissions'));
    }

    public function savePermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->permissions()->sync($request->input('permissions', []));

            return redirect()->route('roles.index')->with('success', 'Permissions updated successfully for ' . $role->role_name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Operation Failed: ' . $e->getMessage());
        }
    }
}

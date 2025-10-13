<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get Admin role
        $adminRole = Role::firstOrCreate(['role_name' => 'Admin', 'status' => 1]);
        // Create or get Student role
        $studentRole = Role::firstOrCreate(['role_name' => 'Student', 'status' => 1]);
        // Create or get Teacher role
        $teacherRole = Role::firstOrCreate(['role_name' => 'Teacher', 'status' => 1]);

        $permissions = Permission::all();

        // Assign all permissions to Admin role
        $adminRole->permissions()->sync($permissions->pluck('id')->toArray());

        // Assign only 'view' related permissions to Student role
        $studentViewPermissions = $permissions->filter(function ($permission) {
            return str_contains($permission->permission_name, '.index') || str_contains($permission->permission_name, '.edit') || str_contains($permission->permission_name, '.view') || str_contains($permission->permission_name, '.show') || str_contains($permission->permission_name, '.get-');
        })->pluck('id')->toArray();
        $studentRole->permissions()->sync($studentViewPermissions);

        // Assign view, add, edit related permissions to Teacher role
        $teacherPermissions = $permissions->filter(function ($permission) {
            return str_contains($permission->permission_name, '.index') || str_contains($permission->permission_name, '.store') || str_contains($permission->permission_name, '.update') || str_contains($permission->permission_name, '.edit') || str_contains($permission->permission_name, '.view') || str_contains($permission->permission_name, '.show') || str_contains($permission->permission_name, '.get-');
        })->pluck('id')->toArray();
        $teacherRole->permissions()->sync($teacherPermissions);
    }
}

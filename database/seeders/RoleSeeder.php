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
        $adminRole = Role::firstOrCreate(['role_name' => 'Admin', 'status' => 1]);

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $adminRole->permissions()->syncWithoutDetaching([
                $permission->id => ['view' => 1, 'add' => 1, 'edit' => 1, 'delete' => 1]
            ]);
        }
    }
}

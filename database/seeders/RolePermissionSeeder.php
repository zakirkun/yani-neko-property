<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $resources = ['users', 'agents', 'properties', 'property types', 'amenities', 'leads', 'roles'];
        $actions = ['view', 'create', 'update', 'delete'];
        $permissions = [];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissions[] = Permission::firstOrCreate([
                    'name' => $action.' '.$resource,
                    'guard_name' => 'web',
                ]);
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $agent = Role::firstOrCreate(['name' => 'agent', 'guard_name' => 'web']);

        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'create roles', 'update roles', 'delete roles',
        ])->get());
        $agent->syncPermissions(Permission::where(function ($query) {
            $query->where('name', 'like', '% properties')
                ->orWhere('name', 'like', '% leads');
        })->get());
    }
}

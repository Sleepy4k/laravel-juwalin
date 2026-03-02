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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Admin permissions
            'admin.dashboard',
            'admin.packages.view', 'admin.packages.create', 'admin.packages.edit', 'admin.packages.delete',
            'admin.orders.view', 'admin.orders.edit', 'admin.orders.delete',
            'admin.users.view', 'admin.users.edit', 'admin.users.delete',
            'admin.containers.view', 'admin.containers.manage',
            'admin.payments.view', 'admin.payments.confirm',
            'admin.settings.view', 'admin.settings.edit',
            // User permissions
            'portal.dashboard',
            'portal.orders.create', 'portal.orders.view',
            'portal.containers.view', 'portal.containers.manage',
            'portal.billing.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'portal.dashboard',
            'portal.orders.create', 'portal.orders.view',
            'portal.containers.view', 'portal.containers.manage',
            'portal.billing.view',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(' Tạo Roles và Permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo permissions
        $permissions = [
            'audit_logs.view',
            'contracts.terminate',
            'properties.create',
            'properties.delete',
            'properties.update',
            'properties.view',
            'rooms.create',
            'rooms.delete',
            'rooms.update',
            'rooms.view',
            'users.create',
            'users.delete',
            'users.update',
            'users.view',
            'properties.staff.assign',
            'properties.staff.revoke',
            'properties.staff.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'sanctum']
            );
        }

        $this->command->info('  ✓ Tạo ' . count($permissions) . ' permissions');

        // Tạo roles
        $ownerRole = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'sanctum']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'sanctum']);
        $tenantRole = Role::firstOrCreate(['name' => 'Tenant', 'guard_name' => 'sanctum']);

        // Gán quyền cho Owner (tất cả)
        $ownerRole->syncPermissions(Permission::all());

        // Gán quyền cho Manager
        $managerRole->syncPermissions([
            'audit_logs.view',
            'contracts.terminate',
            'properties.create',
            'properties.delete',
            'properties.update',
            'properties.view',
            'rooms.create',
            'rooms.delete',
            'rooms.update',
            'rooms.view',
            'properties.staff.assign',
            'properties.staff.revoke',
            'properties.staff.view',
        ]);

        // Gán quyền cho Staff (chỉ xem và cập nhật)
        $staffRole->syncPermissions([
            'properties.view',
            'rooms.view',
            'rooms.update',
        ]);

        // Tenant không có quyền gì

        $this->command->info('  ✓ Tạo 4 roles: Owner, Manager, Staff, Tenant');
        $this->command->info('  ✓ Gán permissions cho roles');
    }
}

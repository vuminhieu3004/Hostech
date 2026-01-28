<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Thứ tự seed quan trọng: Roles -> Orgs -> Users -> Properties -> PropertyUserRoles -> AuditLogs
        $this->call([
            RolePermissionSeeder::class,    // Bước 1: Tạo roles & permissions
            OrgSeeder::class,                // Bước 2: Tạo organizations
            UserSeeder::class,               // Bước 3: Tạo users (owner, manager, staff, tenant)
            PropertySeeder::class,           // Bước 4: Tạo properties
            PropertyUserRoleSeeder::class,   // Bước 5: Gán users vào properties
            AuditLogSeeder::class,           // Bước 6: Tạo audit logs
        ]);

    }
}

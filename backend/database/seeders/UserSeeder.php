<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(' Tạo Users cho mỗi Organization...');

        $orgs = Org::all();

        foreach ($orgs as $org) {
            $this->command->info("   Org: {$org->name}");

            // 1. Tạo 1 Owner
            $owner = User::factory()->owner()->create([
                'org_id' => $org->id,
                'full_name' => 'Owner ' . $org->name,
                'email' => 'owner.' . $org->id . '@hostech.test',
                'phone' => '090' . substr($org->id, 0, 7),
                'password_hash' => Hash::make('12345678'),
            ]);
            $owner->syncRoles(['Owner']);
            $this->command->info("    ✓ 1 Owner: {$owner->full_name}");

            // 2. Tạo 2-3 Managers
            $managerCount = rand(2, 3);
            $managers = collect();
            for ($i = 1; $i <= $managerCount; $i++) {
                $manager = User::factory()->manager()->create([
                    'org_id' => $org->id,
                    'full_name' => "Manager {$i} - {$org->name}",
                    'email' => "manager{$i}." . $org->id . '@hostech.test',
                    'phone' => '091' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'password_hash' => Hash::make('12345678'),
                    'invited_by_user_id' => $owner->id,
                ]);
                $manager->syncRoles(['Manager']);
                $managers->push($manager);
            }
            $this->command->info("    ✓ {$managerCount} Managers");

            // 3. Tạo 3-5 Staff
            $staffCount = rand(3, 5);
            $staffs = collect();
            for ($i = 1; $i <= $staffCount; $i++) {
                $staff = User::factory()->staff()->create([
                    'org_id' => $org->id,
                    'full_name' => "Staff {$i} - {$org->name}",
                    'email' => "staff{$i}." . $org->id . '@hostech.test',
                    'phone' => '092' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'password_hash' => Hash::make('12345678'),
                    'invited_by_user_id' => $owner->id,
                ]);
                $staff->syncRoles(['Staff']);
                $staffs->push($staff);
            }
            $this->command->info("    ✓ {$staffCount} Staffs");

            // 4. Tạo 5-10 Tenants
            $tenantCount = rand(5, 10);
            for ($i = 1; $i <= $tenantCount; $i++) {
                $tenant = User::factory()->tenant()->create([
                    'org_id' => $org->id,
                    'full_name' => "Tenant {$i} - {$org->name}",
                    'phone' => '093' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'invited_by_user_id' => $owner->id,
                ]);
                $tenant->syncRoles(['Tenant']);
            }
            $this->command->info("    ✓ {$tenantCount} Tenants");
            $this->command->info('');
        }

        // Tạo Demo Organization với credentials dễ nhớ
        $this->createDemoOrg();
    }

    private function createDemoOrg(): void
    {

        $demoOrg = Org::create([
            'name' => 'Hostech Demo Company',
            'phone' => '0368326144',
            'email' => 'phungxuanquy24721@gmail.com',
            'address' => 'Hanoi, Vietnam',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'currency' => 'VND',
        ]);

        $demoOwner = User::factory()->owner()->create([
            'org_id' => $demoOrg->id,
            'full_name' => 'Demo Owner',
            'email' => 'phungxuanquy24721@gmail.com',
            'phone' => '0368326144',
            'password_hash' => Hash::make('12345678'),
        ]);
        $demoOwner->syncRoles(['Owner']);

        $demoManager = User::factory()->manager()->create([
            'org_id' => $demoOrg->id,
            'full_name' => 'Demo Manager',
            'email' => 'manager@hostech.test',
            'phone' => '0900000001',
            'password_hash' => Hash::make('12345678'),
            'invited_by_user_id' => $demoOwner->id,
        ]);
        $demoManager->syncRoles(['Manager']);

        $demoStaff = User::factory()->staff()->create([
            'org_id' => $demoOrg->id,
            'full_name' => 'Demo Staff',
            'email' => 'staff@hostech.test',
            'phone' => '0900000002',
            'password_hash' => Hash::make('12345678'),
            'invited_by_user_id' => $demoOwner->id,
        ]);
        $demoStaff->syncRoles(['Staff']);

        $this->command->info('  ✓ Demo Org created!');
        $this->command->info('  ✓ Email: vutienhieu202@gmail.com | Phone: 0368326144 | Password: 12345678');
        $this->command->info('');
    }
}

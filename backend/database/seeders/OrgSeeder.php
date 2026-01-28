<?php

namespace Database\Seeders;

use App\Models\Org;
use Illuminate\Database\Seeder;

class OrgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏢 Tạo 10 Organizations...');

        // Tạo 10 orgs ngẫu nhiên
        $orgs = Org::factory()->count(10)->create();

        foreach ($orgs as $org) {
            $this->command->info("  ✓ {$org->name}");
        }

        $this->command->info('');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(' Tạo Audit Logs...');

        $orgs = Org::all();

        foreach ($orgs as $org) {
            $users = User::where('org_id', $org->id)->pluck('id')->toArray();

            if (empty($users)) {
                continue;
            }

            $logCount = rand(20, 30);

            AuditLog::factory()->count($logCount)->create([
                'org_id' => $org->id,
                'actor_user_id' => $users[array_rand($users)],
            ]);

            $this->command->info("  ✓ {$org->name}: {$logCount} audit logs");
        }

        $this->command->info('');
    }
}
<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyUserRole;
use Illuminate\Database\Seeder;

class PropertyUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔗 Gán Users vào Properties...');

        $orgs = Org::all();

        foreach ($orgs as $org) {
            $properties = Property::where('org_id', $org->id)->get();
            $managers = User::where('org_id', $org->id)->where('role', 'MANAGER')->get();
            $staffs = User::where('org_id', $org->id)->where('role', 'STAFF')->get();

            if ($properties->isEmpty() || ($managers->isEmpty() && $staffs->isEmpty())) {
                continue;
            }

            foreach ($properties as $property) {
                // Gán 1-2 managers vào mỗi property
                $assignedManagers = $managers->random(min(2, $managers->count()));
                foreach ($assignedManagers as $manager) {
                    PropertyUserRole::factory()->manager()->create([
                        'org_id' => $org->id,
                        'property_id' => $property->id,
                        'user_id' => $manager->id,
                    ]);
                }

                // Gán 2-3 staffs vào mỗi property
                if ($staffs->isNotEmpty()) {
                    $assignedStaffs = $staffs->random(min(3, $staffs->count()));
                    foreach ($assignedStaffs as $staff) {
                        PropertyUserRole::factory()->staff()->create([
                            'org_id' => $org->id,
                            'property_id' => $property->id,
                            'user_id' => $staff->id,
                        ]);
                    }
                }
            }

            $totalAssignments = PropertyUserRole::where('org_id', $org->id)->count();
            $this->command->info("  ✓ {$org->name}: {$totalAssignments} assignments");
        }

        $this->command->info('');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info(' Tạo Properties cho mỗi Organization...');

        $orgs = Org::all();

        foreach ($orgs as $org) {
            $propertyCount = rand(5, 10);

            Property::factory()->count($propertyCount)->create([
                'org_id' => $org->id,
            ]);

            $this->command->info("  ✓ {$org->name}: {$propertyCount} properties");
        }

        $this->command->info('');
    }
}

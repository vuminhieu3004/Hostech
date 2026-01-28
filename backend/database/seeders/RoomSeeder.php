<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();

        foreach ($properties as $property) {
            // Mỗi property có 3-8 rooms
            $roomCount = rand(3, 8);

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = rand(1, 5);
                $roomNum = str_pad($i, 2, '0', STR_PAD_LEFT);

                Room::factory()->create([
                    'org_id' => $property->org_id,
                    'property_id' => $property->id,
                    'code' => chr(65 + ($floor - 1)) . $roomNum, // A01, A02, B01, B02...
                    'floor' => $floor,
                ]);
            }
        }

        $this->command->info("  ✓ Tạo " . Room::count() . " rooms");
    }
}

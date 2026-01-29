<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['studio', 'apartment', 'house', 'dormitory', 'other'];
        $statuses = ['available', 'occupied', 'maintenance', 'reserved'];

        $type = $this->faker->randomElement($types);
        $floor = $this->faker->numberBetween(1, 10);

        return [
            'id' => (string) Str::uuid(),
            'org_id' => null, // Sẽ set trong seeder
            'property_id' => null, // Sẽ set trong seeder
            'code' => strtoupper($this->faker->bothify('?###')), // VD: A101, B202
            'name' => $this->faker->randomElement([
                'Phòng ' . $this->faker->numberBetween(100, 999),
                'Room ' . $this->faker->bothify('?-###'),
                'Căn hộ ' . $this->faker->numberBetween(1, 50),
            ]),
            'type' => $type,
            'area' => $this->faker->randomFloat(2, 15, 80), // 15-80 m2
            'floor' => $floor,
            'capacity' => $this->faker->numberBetween(1, 4),
            'base_price' => $this->faker->randomElement([
                1500000,
                2000000,
                2500000,
                3000000,
                3500000,
                4000000,
                5000000,
                6000000,
                7000000,
                8000000
            ]),
            'status' => $this->faker->randomElement($statuses),
            'description' => $this->faker->optional(0.7)->sentence(10),
            'amenities' => $this->faker->randomElements([
                'Điều hòa',
                'Tủ lạnh',
                'Máy giặt',
                'Nóng lạnh',
                'Giường',
                'Tủ áo',
                'Bàn ghế',
                'TV',
                'Bếp',
                'Ban công'
            ], $this->faker->numberBetween(3, 7)),
            'utilities' => [
                'electric_price' => $this->faker->numberBetween(3000, 4000),
                'water_price' => $this->faker->numberBetween(15000, 25000),
                'internet_included' => $this->faker->boolean(70),
                'parking_fee' => $this->faker->optional(0.5)->numberBetween(50000, 150000),
            ],
            'created_at' => now(),
        ];
    }

    public function available(): static
    {
        return $this->state(fn() => ['status' => 'available']);
    }

    public function occupied(): static
    {
        return $this->state(fn() => ['status' => 'occupied']);
    }

    public function studio(): static
    {
        return $this->state(fn() => [
            'type' => 'studio',
            'capacity' => 1,
            'area' => $this->faker->randomFloat(2, 15, 30),
        ]);
    }

    public function apartment(): static
    {
        return $this->state(fn() => [
            'type' => 'apartment',
            'capacity' => $this->faker->numberBetween(2, 4),
            'area' => $this->faker->randomFloat(2, 35, 80),
        ]);
    }
}

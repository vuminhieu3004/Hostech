<?php

namespace Database\Factories;

use App\Models\Org;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org>
 */
class OrgFactory extends Factory
{
    protected $model = Org::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyTypes = ['Hotel', 'Apartment', 'Resort', 'Hostel', 'Villa', 'Homestay'];

        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->company() . ' ' . $this->faker->randomElement($propertyTypes),
            'phone' => $this->faker->numerify('09########'),
            'email' => $this->faker->unique()->companyEmail(),
            'address' => $this->faker->address(),
            'timezone' => $this->faker->randomElement(['Asia/Bangkok', 'Asia/Ho_Chi_Minh', 'Asia/Singapore']),
            'currency' => $this->faker->randomElement(['VND', 'THB', 'USD']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

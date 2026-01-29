<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $propertyTypes = ['Nhà trọ', 'Chung cư mini', 'Homestay', 'Khu trọ', 'Căn hộ dịch vụ', 'Nhà nguyên căn'];
        $type = $this->faker->randomElement($propertyTypes);

        return [
            'id' => (string) Str::uuid(),
            'org_id' => null, // Sẽ được set trong seeder
            'code' => strtoupper($this->faker->unique()->bothify('P###??')),
            'name' => $type . ' ' . $this->faker->streetName(),
            'address' => $this->faker->address(),
            'note' => $this->faker->optional(0.3)->sentence(),
            'default_billing_cycle' => $this->faker->randomElement(['MONTHLY', 'QUARTERLY', 'YEARLY']),
            'default_due_day' => $this->faker->numberBetween(1, 28),
            'default_cutoff_day' => $this->faker->numberBetween(25, 31),
            'bank_accounts' => [
                [
                    'bank_name' => $this->faker->randomElement(['Vietcombank', 'Techcombank', 'MB Bank', 'VPBank', 'ACB', 'Sacombank']),
                    'account_number' => $this->faker->numerify('##########'),
                    'account_name' => $this->faker->name(),
                    'branch' => $this->faker->city(),
                ]
            ],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

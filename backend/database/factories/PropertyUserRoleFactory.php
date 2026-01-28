<?php

namespace Database\Factories;

use App\Models\PropertyUserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyUserRole>
 */
class PropertyUserRoleFactory extends Factory
{
    protected $model = PropertyUserRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'org_id' => null, // Sẽ được set trong seeder
            'property_id' => null, // Sẽ được set trong seeder
            'user_id' => null, // Sẽ được set trong seeder
            'role' => $this->faker->randomElement(['MANAGER', 'STAFF']),
            'is_active' => true,
            'permissions' => null,
            'created_at' => now(),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn() => [
            'role' => 'MANAGER',
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn() => [
            'role' => 'STAFF',
        ]);
    }
}

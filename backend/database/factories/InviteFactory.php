<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\Org;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InviteFactory extends Factory
{
    protected $model = Invite::class;

    public function definition(): array
    {
        return [
            'org_id' => Org::factory(),
            'created_by_user_id' => User::factory(),
            'target_role' => fake()->randomElement(['MANAGER', 'STAFF', 'TENANT']),
            'target_email' => fake()->unique()->safeEmail(),
            'target_phone' => null,
            'code' => strtoupper(Str::random(8)),
            'expires_at' => now()->addDays(7),
            'max_uses' => 1,
            'used_count' => 0,
            'status' => 'PENDING',
            'property_id' => null,
            'room_id' => null,
            'accepted_by_user_id' => null,
            'accepted_at' => null,
            'revoked_at' => null,
            'meta' => null,
        ];
    }

    public function forManager(): static
    {
        return $this->state(fn(array $attributes) => [
            'target_role' => 'MANAGER',
        ]);
    }

    public function forStaff(): static
    {
        return $this->state(fn(array $attributes) => [
            'target_role' => 'STAFF',
        ]);
    }

    public function forTenant(): static
    {
        return $this->state(fn(array $attributes) => [
            'target_role' => 'TENANT',
        ]);
    }

    public function withProperty(): static
    {
        return $this->state(fn(array $attributes) => [
            'property_id' => Property::factory(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'expires_at' => now()->subDays(1),
            'status' => 'EXPIRED',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'ACCEPTED',
            'accepted_by_user_id' => User::factory(),
            'accepted_at' => now()->subHours(2),
            'used_count' => 1,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'REVOKED',
            'revoked_at' => now()->subHours(1),
        ]);
    }

    public function withPhone(): static
    {
        return $this->state(fn(array $attributes) => [
            'target_phone' => '09' . fake()->numerify('########'),
            'target_email' => null,
        ]);
    }
}

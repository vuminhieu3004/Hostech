<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $phone = $this->faker->unique()->numerify('09########');

        return [
            'id' => (string) Str::uuid(),
            'org_id' => null, // sẽ set trong seeder

            'role' => 'TENANT', // default tenant
            'full_name' => $this->faker->name(),
            'phone' => $phone,
            'email' => $this->faker->unique()->safeEmail(),

            // DB của bạn dùng password_hash
            'password_hash' => null, // tenant không có password
            'phone_verified_at' => now(),
            'email_verified_at' => null,

            'failed_login_count' => 0,
            'locked_until' => null,
            'last_login_at' => null,

            'invite_code' => null,
            'invite_expires_at' => null,
            'invited_by_user_id' => null,

            'mfa_enabled' => false,
            'mfa_method' => null,
            'mfa_secret_encrypted' => null,
            'mfa_enrolled_at' => null,

            'is_active' => true,
            'deleted_at' => null,

            'meta' => ['seed' => true],

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // trạng thái: owner/staff có password để login
    public function withPassword(string $plain = '12345678'): static
    {
        return $this->state(fn() => [
            'password_hash' => Hash::make($plain),
            'email_verified_at' => now(),
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn() => [
            'role' => 'OWNER',
        ])->withPassword('12345678');
    }

    public function staff(): static
    {
        return $this->state(fn() => [
            'role' => 'STAFF',
        ])->withPassword('12345678');
    }

    public function manager(): static
    {
        return $this->state(fn() => [
            'role' => 'MANAGER',
        ])->withPassword('12345678');
    }

    public function tenant(): static
    {
        return $this->state(fn() => [
            'role' => 'TENANT',
            'password_hash' => null,
            'email' => null, // tenant thường login bằng phone
            'phone_verified_at' => now(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }

    public function locked(int $minutes = 15): static
    {
        return $this->state(fn() => [
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }
}

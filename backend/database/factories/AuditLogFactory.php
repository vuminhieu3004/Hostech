<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = [
            'AUTH_LOGIN_PASSWORD_ATTEMPT',
            'AUTH_LOGIN_PASSWORD_OK_OTP_SENT',
            'AUTH_OTP_VERIFY_SUCCESS',
            'AUTH_LOGOUT',
            'USER_CREATED',
            'USER_UPDATED',
            'USER_DELETED',
            'PROPERTY_CREATED',
            'PROPERTY_UPDATED',
            'PROPERTY_DELETED',
        ];

        $entityTypes = ['users', 'properties', 'user_sessions', 'rooms'];

        return [
            'id' => (string) Str::uuid(),
            'org_id' => null, // Sẽ được set trong seeder
            'actor_user_id' => null, // Sẽ được set trong seeder
            'action' => $this->faker->randomElement($actions),
            'entity_type' => $this->faker->randomElement($entityTypes),
            'entity_id' => (string) Str::uuid(),
            'ip' => $this->faker->ipv4(),
            'device_id' => $this->faker->optional(0.5)->uuid(),
            'user_agent' => $this->faker->userAgent(),
            'before' => null,
            'after' => null,
            'meta' => [
                'action_time' => now()->toDateTimeString(),
                'seed' => true,
            ],
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}

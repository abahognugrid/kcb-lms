<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audit>
 */
class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        $auditableTypes = [
            'App\Models\Customer',
            'App\Models\Loan',
            'App\Models\LoanApplication',
            'App\Models\LoanRepayment',
            'App\Models\User',
        ];

        $events = ['created', 'updated', 'deleted', 'restored'];

        return [
            'user_type' => User::class,
            'user_id' => User::factory(),
            'partner_id' => function (array $attributes) {
                return User::find($attributes['user_id'])?->partner_id;
            },
            'event' => $this->faker->randomElement($events),
            'auditable_type' => $this->faker->randomElement($auditableTypes),
            'auditable_id' => $this->faker->numberBetween(1, 1000),
            'old_values' => json_encode([
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'status' => 'old_value',
            ]),
            'new_values' => json_encode([
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'status' => 'new_value',
            ]),
            'url' => $this->faker->url,
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'tags' => null,
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    public function withEvent(string $event): static
    {
        return $this->state(fn(array $attributes) => [
            'event' => $event,
        ]);
    }

    public function withUser(User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
            'partner_id' => $user->partner_id,
        ]);
    }

    public function withIpAddress(string $ipAddress): static
    {
        return $this->state(fn(array $attributes) => [
            'ip_address' => $ipAddress,
        ]);
    }

    public function withUrl(string $url): static
    {
        return $this->state(fn(array $attributes) => [
            'url' => $url,
        ]);
    }

    public function withUserAgent(string $userAgent): static
    {
        return $this->state(fn(array $attributes) => [
            'user_agent' => $userAgent,
        ]);
    }
}

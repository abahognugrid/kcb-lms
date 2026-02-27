<?php

namespace Database\Factories;

use App\Models\Partner;
use App\Models\PartnerApiSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerApiSetting>
 */
class PartnerApiSettingFactory extends Factory
{
    protected $model = PartnerApiSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partner_id' => Partner::first(),
            'api_key' => $this->faker->uuid(),
            'api_name' => $this->faker->word(),
            'refresh_token' => $this->faker->uuid(),
            'expires_at' => now()->addDay(),
            'last_used_at' => now(),
            'api_scopes' => ['read', 'write'],
            'has_been_used' => false,
        ];
    }

    /**
     * Configure the factory for expired tokens.
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->subHour(),
            ];
        });
    }

    /**
     * Configure the factory for tokens that expire soon (within buffer).
     */
    public function expiringSoon(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => now()->addMinutes(2), // Within 5-minute buffer
            ];
        });
    }

    /**
     * Configure the factory for tokens with no expiry.
     */
    public function permanent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'expires_at' => null,
            ];
        });
    }
}

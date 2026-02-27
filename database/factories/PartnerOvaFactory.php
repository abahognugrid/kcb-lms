<?php

namespace Database\Factories;

use App\Models\PartnerOva;
use App\Models\Switches;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Switches>
 */
class PartnerOvaFactory extends Factory
{
    protected $model = PartnerOva::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_name' => 'ova',
            'airtel_url' => 'https://example.com',
            'airtel_callback' => 'https://example.com/callback',
            'airtel_public_key' => 'public_key',
            'pin' => '1234',
            'client_key' => 'client_key',
            'client_secret' => 'client_secret',
        ];
    }
}

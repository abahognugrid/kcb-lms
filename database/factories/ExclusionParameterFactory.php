<?php

namespace Database\Factories;

use App\Models\ExclusionParameter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExclusionParameter>
 */
class ExclusionParameterFactory extends Factory
{
    protected $model = ExclusionParameter::class;

    public function definition(): array
    {
        return [
            'Name' => $this->faker->word(),
            'Parameter' => $this->faker->word(),
            'Model' => $this->faker->randomElement(['Customer', 'Loan', 'Application']),
            'Type' => $this->faker->randomElement(['string', 'number', 'date']),
        ];
    }
}



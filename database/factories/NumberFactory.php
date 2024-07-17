<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class NumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'postcode' => fake()->postcode(),
            'identificatie' => fake()->numberBetween(1, 1000000000),
            'nummer' => fake()->numberBetween(1, 1500),
            'huisletter' => fake()->randomLetter(),
            'ligtAan' => fake()->numberBetween(1, 5000)
        ];
    }
}

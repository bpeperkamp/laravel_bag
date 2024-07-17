<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PublicSpace>
 */
class PublicSpaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naam' => fake()->streetName(),
            'identificatie' => fake()->numberBetween(1, 1000000000),
            'type' => "Weg",
            'status' => "Naamgeving uitgegeven",
            'geconstateerd' => fake()->boolean(),
            'documentdatum' => fake()->date(),
            'documentnummer' => 'FB 2010/OR0001',
            'ligtIn' => fake()->numberBetween(1, 5000)
        ];
    }
}

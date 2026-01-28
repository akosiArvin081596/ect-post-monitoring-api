<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['Typhoon', 'Earthquake', 'Flood', 'Fire', 'Landslide']),
            'starts_at' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'ends_at' => fake()->optional()->dateTimeBetween('now', '+2 months')?->format('Y-m-d'),
            'is_active' => true,
            'description' => fake()->optional()->sentence(),
        ];
    }
}

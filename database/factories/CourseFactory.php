<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'image' => $this->faker->imageUrl(),
            'price' => $this->faker->numberBetween(1000, 999999),
            'sold' => $this->faker->numberBetween(0, 200),
            'short_description' => $this->faker->text(),
            'description' => $this->faker->text(),
        ];
    }
}

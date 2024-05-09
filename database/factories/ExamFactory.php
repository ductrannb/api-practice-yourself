<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomElement(User::whereIn('role_id', [User::ROLE_TEACHER, User::ROLE_ADMIN])->pluck('id')->toArray()),
            'name' => Arr::join($this->faker->words(8), ' '),
            'time' => $this->faker->randomElement([45,120,90]),
        ];
    }
}

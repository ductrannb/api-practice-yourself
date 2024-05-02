<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
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
            'course_id' => $this->faker->randomElement(Course::all()->pluck('id')->toArray()),
            'name' => $this->faker->name,
        ];
    }
}

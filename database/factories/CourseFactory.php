<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
        'instructor_id' => User::factory(),
        'title' => fake()->sentence(4),
        'description' => fake()->paragraph(),
        'price' => fake()->randomFloat(2, 0, 200),
        'is_free' => fake()->boolean(20),
        'status' => 'published',
    ];
    }
}

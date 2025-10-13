<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseVersion>
 */
class CourseVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            return [
            'course_id' => Course::factory(),
            'version_number' => 'v1.0',
            'snapshot' => json_encode(['meta' => 'initial version']),
        ];

    }
}

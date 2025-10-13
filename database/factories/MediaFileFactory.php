<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lesson;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MediaFile>
 */
class MediaFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'lesson_id' => Lesson::factory(),
        'file_name' => fake()->word().'.mp4',
        'file_path' => '/uploads/'.fake()->uuid().'.mp4',
        'mime_type' => 'video/mp4',
        'size' => fake()->numberBetween(1000,100000),
    ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Module;

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
            'module_id' => Module::factory(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(3),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}

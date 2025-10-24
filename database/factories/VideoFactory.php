<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Video;
use App\Models\MediaFile;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition()
    {
        // attempt to create or use an existing MediaFile to satisfy the media_file_id relation
        $mediaFileId = null;

        if (class_exists(MediaFile::class) && method_exists(MediaFile::class, 'factory')) {
            $mediaFile = MediaFile::factory()->create();
            $mediaFileId = $mediaFile->id;
        } else {
            $first = MediaFile::first();
            $mediaFileId = $first?->id;
        }

        return [
            'id' => (string) Str::uuid(),
            'media_file_id' => $mediaFileId,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
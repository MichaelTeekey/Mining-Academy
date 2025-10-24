<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TranscodingJob;
use App\Models\Video;

class TranscodingJobFactory extends Factory
{
    protected $model = TranscodingJob::class;

    public function definition()
    {
        // create a Video if a factory exists; otherwise try to use first Video
        $videoId = null;
        if (method_exists(Video::class, 'factory')) {
            $videoId = Video::factory()->create()->id;
        } else {
            $videoId = Video::first()?->id;
        }

        return [
            'id' => (string) Str::uuid(),
            'video_id' => $videoId,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
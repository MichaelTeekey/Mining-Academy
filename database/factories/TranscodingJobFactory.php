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
        return [
            'id' => (string) Str::uuid(),

            // most apps tie a transcoding job to a video record
            // ensure App\Models\Video exists; otherwise set a valid existing id in tests
            'video_id'      => Video::factory(), // use a Video factory or replace with an existing video id
            'status'        => 'pending',
            'output_format' => 'mp4',
            'preset'        => 'default',
            'meta'          => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
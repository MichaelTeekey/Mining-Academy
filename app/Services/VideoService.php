<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\DB;

class VideoService
{
    public function all(array $filters = [])
    {
        $query = Video::query()->with(['mediaFile', 'renditions']);

        if (isset($filters['media_file_id'])) {
            $query->where('media_file_id', $filters['media_file_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): Video
    {
        return DB::transaction(fn() => Video::create($data));
    }

    public function update(Video $video, array $data): Video
    {
        $video->update($data);
        return $video->fresh(['mediaFile','renditions']);
    }

    public function delete(Video $video): void
    {
        $video->renditions()->delete();
        $video->delete();
    }
}

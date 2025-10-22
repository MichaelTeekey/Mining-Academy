<?php

namespace App\Services;

use App\Models\VideoRendition;
use Illuminate\Support\Facades\DB;

class VideoRenditionService
{
    public function all(array $filters = [])
    {
        $query = VideoRendition::query()->with('video');

        if (isset($filters['video_id'])) {
            $query->where('video_id', $filters['video_id']);
        }

        return $query->orderBy('resolution')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): VideoRendition
    {
        return DB::transaction(fn() => VideoRendition::create($data));
    }

    public function update(VideoRendition $rendition, array $data): VideoRendition
    {
        $rendition->update($data);
        return $rendition->fresh('video');
    }

    public function delete(VideoRendition $rendition): void
    {
        $rendition->delete();
    }
}

<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Support\Facades\DB;

class MediaFileService
{
    public function all(array $filters = [])
    {
        $query = MediaFile::query()->with('lesson');

        if (isset($filters['lesson_id'])) {
            $query->where('lesson_id', $filters['lesson_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): MediaFile
    {
        return DB::transaction(fn() => MediaFile::create($data));
    }

    public function update(MediaFile $mediaFile, array $data): MediaFile
    {
        $mediaFile->update($data);
        return $mediaFile->fresh('lesson');
    }

    public function delete(MediaFile $mediaFile): void
    {
        $mediaFile->delete();
    }
}

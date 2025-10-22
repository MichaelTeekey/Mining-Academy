<?php

namespace App\Services;

use App\Models\TranscodingJob;
use Illuminate\Support\Facades\DB;

class TranscodingJobService
{
    public function all(array $filters = [])
    {
        $query = TranscodingJob::query()->with('video');

        if (isset($filters['video_id'])) {
            $query->where('video_id', $filters['video_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): TranscodingJob
    {
        return DB::transaction(fn() => TranscodingJob::create($data));
    }

    public function update(TranscodingJob $job, array $data): TranscodingJob
    {
        $job->update($data);
        return $job->fresh('video');
    }

    public function delete(TranscodingJob $job): void
    {
        $job->delete();
    }
}

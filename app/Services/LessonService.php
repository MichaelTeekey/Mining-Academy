<?php

namespace App\Services;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class LessonService
{
    public function all(array $filters = [])
    {
        $query = Lesson::query()->with('module');

        if (isset($filters['module_id'])) {
            $query->where('module_id', $filters['module_id']);
        }

        return $query->orderBy('order')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): Lesson
    {
        return DB::transaction(fn() => Lesson::create($data));
    }

    public function update(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);
        return $lesson->fresh('module');
    }

    public function delete(Lesson $lesson): void
    {
        $lesson->delete();
    }
}

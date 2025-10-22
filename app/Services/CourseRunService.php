<?php

namespace App\Services;

use App\Models\CourseRun;
use Illuminate\Support\Str;

class CourseRunService
{
    public function all(array $filters = [])
    {
        $query = CourseRun::with('course');

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->paginate(10);
    }

    public function store(array $data): CourseRun
    {
        $data['id'] = Str::uuid();
        return CourseRun::create($data);
    }

    public function update(CourseRun $run, array $data): CourseRun
    {
        $run->update($data);
        return $run;
    }

    public function delete(CourseRun $run): void
    {
        $run->delete();
    }
}

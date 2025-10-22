<?php

namespace App\Services;

use App\Models\CourseVersion;

class CourseVersionService
{
    public function all(array $filters = [])
    {
        $query = CourseVersion::with('course', 'modules');

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->paginate(10);
    }

    public function store(array $data): CourseVersion
    {
        $data['id'] = \Illuminate\Support\Str::uuid();
        return CourseVersion::create($data);
    }

    public function update(CourseVersion $version, array $data): CourseVersion
    {
        $version->update($data);
        return $version;
    }

    public function delete(CourseVersion $version): void
    {
        $version->delete();
    }
}

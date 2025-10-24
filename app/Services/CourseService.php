<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function all($filters = [])
    {
        $query = Course::query()->with('instructor');

        if (isset($filters['q'])) {
            $query->where('title', 'like', '%'.$filters['q'].'%');
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $data['instructor_id'] = auth()->id();
            $user = auth()->user();

            if (! $user) {
                abort(401, 'Unauthenticated.');
            }

            $allowed = false;

            if (method_exists($user, 'hasRole')) {
                $allowed = $user->hasRole('instructor') || $user->hasRole('admin');
            } elseif (isset($user->role)) {
                $allowed = in_array($user->role, ['instructor', 'admin']);
            } else {
                $allowed = !empty($user->is_admin) || !empty($user->is_instructor);
            }

            if (! $allowed) {
                abort(403, 'Only instructors or admins can create courses.');
            }
            return Course::create($data);
        });
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        return $course->fresh(['instructor']);
    }

    public function delete(Course $course): void
    {
        $course->delete();
    }
}

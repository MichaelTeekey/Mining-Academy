<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EnrollmentService
{
    public function all(array $filters = [])
    {
        $query = Enrollment::with(['user', 'courseRun.course']);

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['course_run_id'])) {
            $query->where('course_run_id', $filters['course_run_id']);
        }

        return $query->get();
    }

    public function enrollUser($userId, $courseRunId)
    {
        try {
            return DB::transaction(function () use ($userId, $courseRunId) {
                $enrollment = Enrollment::firstOrCreate([
                    'user_id' => $userId,
                    'course_run_id' => $courseRunId,
                ], [
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);

                return $enrollment;
            });
        } catch (Exception $e) {
            Log::error('Enrollment error: ' . $e->getMessage());
            throw new Exception('Unable to enroll user at this time.');
        }
    }

    public function getUserCourses($userId)
    {
        return Enrollment::where('user_id', $userId)
            ->with('courseRun.course')
            ->get();
    }

    public function delete($id)
    {
        try {
            $enrollment = Enrollment::findOrFail($id);
            $enrollment->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Enrollment delete error: ' . $e->getMessage());
            throw new Exception('Unable to delete enrollment.');
        }
    }
}

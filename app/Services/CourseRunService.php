<?php

namespace App\Services;

use App\Models\CourseRun;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Carbon\Carbon;

class CourseRunService
{
    /**
     * Fetch all course runs with optional filters and deep relationships.
     */
    public function all(array $filters = [])
    {
        $query = CourseRun::with([
            // Deep eager loading: Course -> Modules -> Lessons -> Media
            'course.modules.lessons.media',
            'course.instructor',
        ]);

        // Optional filters
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        if (!empty($filters['start_after'])) {
            $query->whereDate('start_date', '>=', $filters['start_after']);
        }

        if (!empty($filters['end_before'])) {
            $query->whereDate('end_date', '<=', $filters['end_before']);
        }

        return $query->orderBy('start_date', 'asc')->paginate(10);
    }

    /**
     * Create a new course run
     */
    public function store(array $data): CourseRun
    {
        $user = auth()->user();

        // Role restriction
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'])) {
            abort(403, 'Only admins and instructors can create course runs.');
        }

        // Validate dates
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start = Carbon::parse($data['start_date']);
            $end = Carbon::parse($data['end_date']);
            if ($start->gte($end)) {
                throw new Exception('Start date must be before end date.');
            }
        }

        // Assign UUID
        $data['id'] = $data['id'] ?? Str::uuid();

        // Determine status based on dates
        $data['status'] = $this->calculateStatus($data['start_date'] ?? null, $data['end_date'] ?? null);

        try {
            return DB::transaction(function () use ($data) {
                $run = CourseRun::create($data);
                Log::info('Course run created', ['id' => $run->id]);
                return $run->load('course.modules.lessons.media');
            });
        } catch (Exception $e) {
            Log::error('Error creating course run', ['error' => $e->getMessage()]);
            throw new Exception('Failed to create course run.');
        }
    }

    /**
     * Update a course run safely
     */
    public function update(CourseRun $run, array $data): CourseRun
    {
        try {
            // Recalculate status if dates are updated
            if (!empty($data['start_date']) && !empty($data['end_date'])) {
                $data['status'] = $this->calculateStatus($data['start_date'], $data['end_date']);
            }

            $run->update($data);
            Log::info('Course run updated', ['id' => $run->id]);

            return $run->load('course.modules.lessons.media');
        } catch (Exception $e) {
            Log::error('Course run update failed', ['id' => $run->id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to update course run.');
        }
    }

    /**
     * Soft delete a course run (prevent if active or completed)
     */
    public function delete(CourseRun $run): void
    {
        try {
            if (in_array($run->status, ['active', 'completed'])) {
                throw new Exception('Cannot delete an active or completed course run.');
            }

            $run->delete();
            Log::info('Course run soft-deleted', ['id' => $run->id]);
        } catch (ModelNotFoundException $e) {
            throw new Exception('Course run not found.');
        } catch (Exception $e) {
            Log::error('Error deleting course run', ['id' => $run->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Determine run status from dates
     */
    protected function calculateStatus(?string $startDate, ?string $endDate): string
    {
        if (!$startDate || !$endDate) {
            return 'upcoming';
        }

        $now = now();
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($now->lt($start)) {
            return 'upcoming';
        }

        if ($now->between($start, $end)) {
            return 'active';
        }

        if ($now->gt($end)) {
            return 'completed';
        }

        return 'upcoming';
    }
}

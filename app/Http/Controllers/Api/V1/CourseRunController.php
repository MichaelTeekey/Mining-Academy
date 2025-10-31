<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\CourseRunRequest;
use App\Services\CourseRunService;
use App\Models\CourseRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CourseRunController extends BaseController
{
    protected CourseRunService $service;

    public function __construct(CourseRunService $service)
    {
        $this->service = $service;

        // Allow guests to view but restrict create/update/delete
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the course runs.
     * Includes deep relationships: course, modules, lessons, and media files.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['course_id', 'status', 'instructor_id', 'start_after', 'end_before']);
            $runs = $this->service->all($filters);

            return response()->json([
                'status' => true,
                'message' => 'Course runs retrieved successfully.',
                'data' => $runs,
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching course runs', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch course runs.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created course run.
     */
    public function store(CourseRunRequest $request): JsonResponse
    {
        try {
            $run = $this->service->store($request->validated());

            Log::info('Course run created successfully', [
                'course_run_id' => $run->id,
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Course run created successfully.',
                'data' => $run,
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating course run', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create course run.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a specific course run with its full course hierarchy.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $run = CourseRun::with([
                'course.instructor',
                'course.modules.lessons.media',
            ])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Course run details retrieved successfully.',
                'data' => $run,
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Course run not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update a course run.
     */
    public function update(CourseRunRequest $request, string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            $updated = $this->service->update($run, $request->validated());

            Log::info('Course run updated successfully', [
                'course_run_id' => $id,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Course run updated successfully.',
                'data' => $updated,
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update course run.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a course run (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            $this->service->delete($run);

            Log::info('Course run deleted successfully', [
                'course_run_id' => $id,
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Course run deleted successfully.',
            ]);
        } catch (Throwable $e) {
            Log::error('Error deleting course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete course run.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

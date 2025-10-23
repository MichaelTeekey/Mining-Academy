<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CourseController extends Controller
{
    protected CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->service = $service;
    }

    /**
     * List all courses
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $courses = $this->service->all($request->all());

            return response()->json([
                'status' => true,
                'data' => $courses
            ]);
        } catch (Throwable $e) {
            log::error('Error fetching courses', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new course
     */
    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            log::info('Creating new course', [
                'title' => $data['title'],
                'instructor_id' => $request->user()->id,
                'ip' => $request->ip(),
            ]);

            $data['instructor_id'] = $request->user()->id;

            log::info('Storing new course', [
                'title' => $data['title'],
                'instructor_id' => $data['instructor_id'],
                'ip' => $request->ip(),
            ]);

            $course = $this->service->store($data);

            log::info('Course created successfully', [
                'course_id' => $course->id,
                'instructor_id' => $course->instructor_id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Course created successfully',
                'data' => $course
            ], 201);
        } catch (Throwable $e) {
            log::error('Error creating course', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single course
     */
    public function show(string $id): JsonResponse
    {
        try {
            $course = Course::with(['instructor', 'courseVersions', 'courseRuns', 'modules'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $course
            ]);
        } catch (Throwable $e) {
            log::error('Error fetching course', [
                'course_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Course not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update an existing course
     */
    public function update(CourseRequest $request, string $id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);
            log::info('Updating course', [
                'course_id' => $course->id,
                'ip' => $request->ip(),
            ]);

            $updated = $this->service->update($course, $request->validated());
            
            log::info('Course updated successfully', [
                'course_id' => $course->id,
                'ip' => $request->ip(),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Course updated successfully',
                'data' => $updated
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a course
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);
            $this->service->delete($course);

            return response()->json([
                'status' => true,
                'message' => 'Course deleted successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete course',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

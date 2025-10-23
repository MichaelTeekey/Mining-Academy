<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use App\Services\LessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LessonController extends BaseController
{
    protected LessonService $service;

    public function __construct(LessonService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $lessons = $this->service->all($request->all());
            log::info('Fetched lessons', ['count' => count($lessons)]);
            return response()->json([
                'status' => true,
                'data' => $lessons
            ]);
        } catch (Throwable $e) {
            log::error('Error fetching lessons', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch lessons',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(LessonRequest $request): JsonResponse
    {
        try {
            $lesson = $this->service->store($request->validated());
            log::info('Created lesson', ['lesson_id' => $lesson->id]);
            return response()->json([
                'status' => true,
                'message' => 'Lesson created successfully',
                'data' => $lesson
            ], 201);
        } catch (Throwable $e) {
            log::error('Error creating lesson', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to create lesson',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $lesson = Lesson::with('module', 'mediaFiles')->findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $lesson
            ]);
        } catch (Throwable $e) {
            log::error('Error fetching lesson', [
                'error' => $e->getMessage(),
                'lesson_id' => $id
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Lesson not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(LessonRequest $request, string $id): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($id);
            log::info('Updating lesson', ['lesson_id' => $id]);
            $lesson = $this->service->update($lesson, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Lesson updated successfully',
                'data' => $lesson
            ]);
        } catch (Throwable $e) {
            log::error('Error updating lesson', [
                'error' => $e->getMessage(),
                'lesson_id' => $id
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to update lesson',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $lesson = Lesson::findOrFail($id);
            $this->service->delete($lesson);

            return response()->json([
                'status' => true,
                'message' => 'Lesson deleted successfully'
            ]);
        } catch (Throwable $e) {
            log::error('Error deleting lesson', [
                'error' => $e->getMessage(),
                'lesson_id' => $id
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete lesson',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

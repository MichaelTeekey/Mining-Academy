<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use App\Services\LessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    protected LessonService $service;

    public function __construct(LessonService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $lessons = $this->service->all($request->all());
        return response()->json($lessons);
    }

    public function store(LessonRequest $request): JsonResponse
    {
        $lesson = $this->service->store($request->validated());
        return response()->json($lesson, 201);
    }

    public function show(string $id): JsonResponse
    {
        $lesson = Lesson::with('module', 'mediaFiles')->findOrFail($id);
        return response()->json($lesson);
    }

    public function update(LessonRequest $request, string $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $lesson = $this->service->update($lesson, $request->validated());
        return response()->json($lesson);
    }

    public function destroy(string $id): JsonResponse
    {
        $lesson = Lesson::findOrFail($id);
        $this->service->delete($lesson);

        return response()->json(['message' => 'Lesson deleted successfully']);
    }
}

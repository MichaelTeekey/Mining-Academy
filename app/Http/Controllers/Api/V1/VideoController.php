<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    protected VideoService $service;

    public function __construct(VideoService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $videos = $this->service->all($request->all());
        return response()->json($videos);
    }

    public function store(VideoRequest $request): JsonResponse
    {
        $video = $this->service->store($request->validated());
        return response()->json($video, 201);
    }

    public function show(string $id): JsonResponse
    {
        $video = Video::with(['mediaFile','renditions'])->findOrFail($id);
        return response()->json($video);
    }

    public function update(VideoRequest $request, string $id): JsonResponse
    {
        $video = Video::findOrFail($id);
        $video = $this->service->update($video, $request->validated());
        return response()->json($video);
    }

    public function destroy(string $id): JsonResponse
    {
        $video = Video::findOrFail($id);
        $this->service->delete($video);

        return response()->json(['message' => 'Video deleted successfully']);
    }
}

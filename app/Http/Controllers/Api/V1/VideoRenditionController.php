<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\VideoRenditionRequest;
use App\Models\VideoRendition;
use App\Services\VideoRenditionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoRenditionController extends BaseController
{
    protected VideoRenditionService $service;

    public function __construct(VideoRenditionService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $renditions = $this->service->all($request->all());
        return response()->json($renditions);
    }

    public function store(VideoRenditionRequest $request): JsonResponse
    {
        $rendition = $this->service->store($request->validated());
        return response()->json($rendition, 201);
    }

    public function show(string $id): JsonResponse
    {
        $rendition = VideoRendition::with('video')->findOrFail($id);
        return response()->json($rendition);
    }

    public function update(VideoRenditionRequest $request, string $id): JsonResponse
    {
        $rendition = VideoRendition::findOrFail($id);
        $rendition = $this->service->update($rendition, $request->validated());
        return response()->json($rendition);
    }

    public function destroy(string $id): JsonResponse
    {
        $rendition = VideoRendition::findOrFail($id);
        $this->service->delete($rendition);

        return response()->json(['message' => 'Video rendition deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use App\Services\MediaFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    protected MediaFileService $service;

    public function __construct(MediaFileService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $mediaFiles = $this->service->all($request->all());
        return response()->json($mediaFiles);
    }

    public function store(MediaFileRequest $request): JsonResponse
    {
        $mediaFile = $this->service->store($request->validated());
        return response()->json($mediaFile, 201);
    }

    public function show(string $id): JsonResponse
    {
        $mediaFile = MediaFile::with('lesson')->findOrFail($id);
        return response()->json($mediaFile);
    }

    public function update(MediaFileRequest $request, string $id): JsonResponse
    {
        $mediaFile = MediaFile::findOrFail($id);
        $mediaFile = $this->service->update($mediaFile, $request->validated());
        return response()->json($mediaFile);
    }

    public function destroy(string $id): JsonResponse
    {
        $mediaFile = MediaFile::findOrFail($id);
        $this->service->delete($mediaFile);

        return response()->json(['message' => 'Media file deleted successfully']);
    }
}

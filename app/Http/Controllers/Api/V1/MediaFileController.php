<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\MediaFileRequest;
use App\Models\MediaFile;
use App\Services\MediaFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MediaFileController extends BaseController
{
    protected MediaFileService $service;

    public function __construct(MediaFileService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * List all media files
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $mediaFiles = $this->service->all($request->all());
            Log::info('Fetched media files', [
                'count' => count($mediaFiles),
                'filters' => $request->all()
            ]);

            return response()->json($mediaFiles);
        } catch (Exception $e) {
            Log::error('Error fetching media files', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to retrieve media files'
            ], 500);
        }
    }

    /**
     * Store a new media file
     */
    public function store(MediaFileRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $mediaFile = $this->service->store($validated);

            Log::info('Media file created successfully', [
                'media_file_id' => $mediaFile->id,
                'user_id' => $request->user()->id
            ]);

            return response()->json($mediaFile, 201);
        } catch (Exception $e) {
            Log::error('Error creating media file', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Unable to create media file'
            ], 500);
        }
    }

    /**
     * Show a single media file
     */
    public function show(string $id): JsonResponse
    {
        try {
            $mediaFile = MediaFile::with('lesson')->findOrFail($id);

            Log::info('Fetched media file details', [
                'media_file_id' => $id
            ]);

            return response()->json($mediaFile);
        } catch (Exception $e) {
            Log::error('Error fetching media file', [
                'error' => $e->getMessage(),
                'media_file_id' => $id
            ]);

            return response()->json([
                'error' => 'Media file not found'
            ], 404);
        }
    }

    /**
     * Update a media file
     */
    public function update(MediaFileRequest $request, string $id): JsonResponse
    {
        try {
            $mediaFile = MediaFile::findOrFail($id);
            $updated = $this->service->update($mediaFile, $request->validated());

            Log::info('Media file updated successfully', [
                'media_file_id' => $id,
                'user_id' => $request->user()->id
            ]);

            return response()->json($updated);
        } catch (Exception $e) {
            Log::error('Error updating media file', [
                'error' => $e->getMessage(),
                'media_file_id' => $id,
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to update media file'
            ], 500);
        }
    }

    /**
     * Delete a media file
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $mediaFile = MediaFile::findOrFail($id);
            $this->service->delete($mediaFile);

            Log::info('Media file deleted successfully', [
                'media_file_id' => $id
            ]);

            return response()->json([
                'message' => 'Media file deleted successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error deleting media file', [
                'error' => $e->getMessage(),
                'media_file_id' => $id
            ]);

            return response()->json([
                'error' => 'Failed to delete media file'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRenditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'video_id' => 'required|uuid|exists:videos,id',
            'resolution' => 'required|string|max:50', // e.g., 720p, 1080p
            'file_path' => 'required|string|max:1024',
            'mime_type' => 'nullable|string|max:100',
            'size' => 'nullable|integer|min:0',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranscodingJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'video_id' => 'required|uuid|exists:videos,id',
            'status' => 'required|string|in:pending,processing,completed,failed',
            'settings' => 'nullable|array',
            'error_message' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'media_file_id' => 'required|uuid|exists:media_files,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}

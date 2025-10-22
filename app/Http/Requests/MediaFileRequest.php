<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'lesson_id' => 'required|uuid|exists:lessons,id',
            'file_name' => 'required|string|max:255',
            'file_path' => 'required|string|max:1024',
            'mime_type' => 'required|string|max:100',
            'size' => 'nullable|integer|min:0',
        ];
    }
}

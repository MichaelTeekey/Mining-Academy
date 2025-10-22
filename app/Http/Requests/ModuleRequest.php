<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'course_version_id' => 'required|uuid|exists:course_versions,id',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
        ];
    }
}

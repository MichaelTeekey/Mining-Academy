<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|uuid|exists:courses,id',
            'version_number' => 'required|string|max:20',
            'snapshot' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {

        $user = auth()->user();
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'])) {
            abort(403, 'Only admins and instructors can create course runs.');
        }
        return [
            'module_id' => 'required|uuid|exists:modules,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
        ];
    }
}

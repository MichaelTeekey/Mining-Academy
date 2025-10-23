<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Organization;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'account_type' => 'required|string|in:student,instructor,admin',
            // organization_id will be normalized in prepareForValidation()
            'organization_id' => 'nullable|uuid|exists:organizations,id',
        ];
    }


    protected function prepareForValidation(): void
    {
        $inputOrgId = $this->input('organization_id');

        // Find first existing organization or create a default one if none exist
        $firstOrg = Organization::firstOrCreate(
            [], // try to get the first record
            ['name' => 'Mining Academy'] 
        );

        if ($inputOrgId) {
            // If provided id exists, keep it; otherwise fall back to first org
            $exists = Organization::where('id', $inputOrgId)->exists();
            if (!$exists) {
                $this->merge(['organization_id' => $firstOrg->id]);
            }
            // if exists -> no change
        } else {
            // not provided -> set to firstOrg
            $this->merge(['organization_id' => $firstOrg->id]);
        }
    }
}
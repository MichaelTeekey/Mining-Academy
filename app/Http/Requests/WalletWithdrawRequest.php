<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletWithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }
}

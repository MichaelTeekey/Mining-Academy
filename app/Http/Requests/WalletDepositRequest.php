<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:20.00',
            'reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'currency' => 'nullable|string|max:10',
        ];
    }
}

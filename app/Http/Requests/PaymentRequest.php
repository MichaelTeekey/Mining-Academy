<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_run_id' => 'required|uuid|exists:course_runs,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|in:stripe,paypal,flutterwave,manual',
            'currency' => 'nullable|string|size:3',
        ];
    }
}

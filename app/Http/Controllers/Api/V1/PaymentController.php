<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    //
    public function index(Request $request)
    {
        return Payment::where('user_id', $request->user()->id)->get();
    }

    public function store(PaymentRequest $request)
    {
        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'course_run_id' => $request->course_run_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method ?? 'stripe',
            'currency' => $request->currency ?? 'USD',
            'transaction_id' => (string) Str::uuid(),
            'status' => 'completed',
        ]);

        return response()->json($payment, 201);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\CourseRun;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id' => User::factory(),
        'course_run_id' => CourseRun::factory(),
        'payment_method' => 'stripe',
        'amount' => fake()->randomFloat(2, 10, 500),
        'currency' => 'USD',
        'transaction_id' => (string) \Illuminate\Support\Str::uuid(),
        'status' => fake()->randomElement(['completed','pending']),
    ];
    }
}

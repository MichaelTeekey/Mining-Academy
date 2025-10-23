<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Organization;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
       
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'account_type' => 'student',
            // 'organization_id' => $organization->id,
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        // Assert created
        $response->assertStatus(201);

        $response->assertJsonFragment(['email' => 'test@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_validation_error()
    {
        $this->postJson('/api/v1/register', [])
            ->assertStatus(422);
    }
}
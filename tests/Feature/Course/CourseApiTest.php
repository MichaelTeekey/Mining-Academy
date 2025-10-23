<?php

namespace Tests\Feature\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_course()
    {
        $payload = [
            'title' => 'Intro to Mining'
        ];

        $this->postJson('/api/v1/courses', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_course()
    {
        $instructor = User::factory()->create([
            // adjust attribute name if your app uses a different field for role
            'account_type' => 'instructor'
        ]);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'title' => 'Intro to Mining',
            'description' => 'Course description',
            'price' => 100.0
        ];

        $this->postJson('/api/v1/courses', $payload)
            ->assertStatus(201)
            ->assertJson([
                'status' => true,
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'instructor_id']
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Intro to Mining',
            'instructor_id' => $instructor->id
        ]);
    }
}
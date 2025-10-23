<?php

namespace Tests\Feature\CourseRun;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseRun;
use Laravel\Sanctum\Sanctum;

class CourseRunApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_course_runs()
    {
        CourseRun::factory()->count(3)->create();

        $this->getJson('/api/v1/course-runs')
            ->assertStatus(200)
            ->assertJsonStructure(['status','data']);
    }

    public function test_public_can_view_course_run()
    {
        $run = CourseRun::factory()->create();

        $this->getJson("/api/v1/course-runs/{$run->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $run->id]);
    }

    public function test_guest_cannot_create_course_run()
    {
        $course = Course::factory()->create();

        $payload = [
            'course_id' => $course->id,
            'name' => 'Test Cohort',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ];

        $this->postJson('/api/v1/course-runs', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_course_run()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_id' => $course->id,
            'name' => 'October Cohort',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ];

        $this->postJson('/api/v1/course-runs', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['status','message','data' => ['id','course_id','name']]);

        $this->assertDatabaseHas('course_runs', ['name' => 'October Cohort', 'course_id' => $course->id]);
    }

    public function test_instructor_can_update_course_run()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $run = CourseRun::factory()->create(['course_id' => $course->id, 'name' => 'Old Name']);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_id' => $course->id,
            'name' => 'Updated Cohort',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ];

        $this->putJson("/api/v1/course-runs/{$run->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Cohort']);

        $this->assertDatabaseHas('course_runs', ['id' => $run->id, 'name' => 'Updated Cohort']);
    }

    public function test_instructor_can_delete_course_run()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $run = CourseRun::factory()->create(['course_id' => $course->id]);

        Sanctum::actingAs($instructor, ['*']);

        $this->deleteJson("/api/v1/course-runs/{$run->id}")
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('course_runs', ['id' => $run->id]);
    }
}
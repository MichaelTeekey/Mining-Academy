<?php

namespace Tests\Feature\CourseVersion;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseVersion;
use Laravel\Sanctum\Sanctum;

class CourseVersionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_course_versions()
    {
        CourseVersion::factory()->count(3)->create();

        $this->getJson('/api/v1/course-versions')
            ->assertStatus(200)
            ->assertJsonStructure(['status','data']);
    }

    public function test_public_can_view_course_version()
    {
        $version = CourseVersion::factory()->create();

        $this->getJson("/api/v1/course-versions/{$version->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $version->id]);
    }

    public function test_guest_cannot_create_course_version()
    {
        $course = Course::factory()->create();

        $payload = [
            'course_id' => $course->id,
            'version_number' => 'v1.0',
            'snapshot' => 'Initial snapshot',
        ];

        $this->postJson('/api/v1/course-versions', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_course_version()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_id' => $course->id,
            'version_number' => 'v1.0',
            'snapshot' => 'Initial snapshot',
        ];

        $this->postJson('/api/v1/course-versions', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['status','message','data' => ['id','course_id','version_number']]);

        $this->assertDatabaseHas('course_versions', ['course_id' => $course->id, 'version_number' => 'v1.0']);
    }

    public function test_instructor_can_update_course_version()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $version = CourseVersion::factory()->create(['course_id' => $course->id, 'version_number' => 'v1.0']);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_id' => $course->id,        
            'version_number' => 'v1.1',
            'snapshot' => 'Updated snapshot',
        ];

        $this->putJson("/api/v1/course-versions/{$version->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['version_number' => 'v1.1']);

        $this->assertDatabaseHas('course_versions', ['id' => $version->id, 'version_number' => 'v1.1']);
    }

    public function test_instructor_can_delete_course_version()
    {
        $instructor = User::factory()->create(['account_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $version = CourseVersion::factory()->create(['course_id' => $course->id]);

        Sanctum::actingAs($instructor, ['*']);

        $this->deleteJson("/api/v1/course-versions/{$version->id}")
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('course_versions', ['id' => $version->id]);
    }
}
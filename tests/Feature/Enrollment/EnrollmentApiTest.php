<?php

namespace Tests\Feature\Enrollment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseRun;
use App\Models\Enrollment;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;

class EnrollmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_enroll()
    {
        $course = Course::factory()->create();
        $run = CourseRun::factory()->create(['course_id' => $course->id]);

        $this->postJson('/api/v1/enroll', ['course_run_id' => $run->id])
            ->assertStatus(401);
    }

    public function test_user_can_enroll_in_course_run()
    {
        // ensure user has a valid organization and is a student (authorized to enroll)
        $org = Organization::factory()->create();
        $user = User::factory()->create([
            'account_type' => 'student',
            'organization_id' => $org->id,
        ]);

        $course = Course::factory()->create();

        // create an ACTIVE course run (ensure any validation that requires start/end dates passes)
        $run = CourseRun::factory()->create([
            'course_id' => $course->id,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/v1/enroll', ['course_run_id' => $run->id]);

        // dump response when not created to inspect why the API returned 403/422 etc.
        if ($response->status() !== 201) {
            $response->dump();
        }

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id','user_id','course_run_id','enrolled_at']
            ]);

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_run_id' => $run->id,
        ]);
    }

    public function test_user_can_list_my_enrolled_courses()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);
        $course = Course::factory()->create();
        $run = CourseRun::factory()->create(['course_id' => $course->id]);

        // create enrollment record
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_run_id' => $run->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/v1/my-courses')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }
}
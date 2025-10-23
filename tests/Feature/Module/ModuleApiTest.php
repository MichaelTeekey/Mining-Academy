<?php

namespace Tests\Feature\Module;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Module;
use App\Models\CourseVersion;
use Laravel\Sanctum\Sanctum;

class ModuleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_modules()
    {
        Module::factory()->count(3)->create();

        $this->getJson('/api/v1/modules')
            ->assertStatus(200)
            ->assertJsonStructure(['status','data']);
    }

    public function test_public_can_view_module()
    {
        $module = Module::factory()->create();

        $this->getJson("/api/v1/modules/{$module->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $module->id]);
    }

    public function test_guest_cannot_create_module()
    {
        $version = CourseVersion::factory()->create();

        $payload = [
            'course_version_id' => $version->id,
            'title' => 'Module 1',
            'order' => 1,
        ];

        $this->postJson('/api/v1/modules', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_module()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $version = CourseVersion::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_version_id' => $version->id,
            'title' => 'Module 1: Intro',
            'order' => 1,
        ];

        $this->postJson('/api/v1/modules', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['status','message','data' => ['id','title','course_version_id']]);

        $this->assertDatabaseHas('modules', ['title' => 'Module 1: Intro', 'course_version_id' => $version->id]);
    }

    public function test_instructor_can_update_module()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $version = CourseVersion::factory()->create();
        $module = Module::factory()->create(['course_version_id' => $version->id, 'title' => 'Old Title']);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'course_version_id' => $version->id,
            'title' => 'Updated Module',
            'order' => 2,
        ];

        $this->putJson("/api/v1/modules/{$module->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Module']);

        $this->assertDatabaseHas('modules', ['id' => $module->id, 'title' => 'Updated Module']);
    }

    public function test_instructor_can_delete_module()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $module = Module::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        $this->deleteJson("/api/v1/modules/{$module->id}")
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
    }
}
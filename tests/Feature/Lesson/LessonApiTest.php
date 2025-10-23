<?php

namespace Tests\Feature\Lesson;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Organization;
use Laravel\Sanctum\Sanctum;

class LessonApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_lessons()
    {
        Lesson::factory()->count(3)->create();

        $this->getJson('/api/v1/lessons')
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);
    }

    public function test_public_can_view_lesson()
    {
        $lesson = Lesson::factory()->create();

        $this->getJson("/api/v1/lessons/{$lesson->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $lesson->id]);
    }

    public function test_guest_cannot_create_lesson()
    {
        $module = Module::factory()->create();

        $payload = [
            'module_id' => $module->id,
            'title' => 'New Lesson',
            'content' => 'Lesson content',
            'order' => 1,
        ];

        $this->postJson('/api/v1/lessons', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_lesson()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $module = Module::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'module_id' => $module->id,
            'title' => 'Intro to Safety',
            'content' => 'Safety guidelines',
            'order' => 1,
        ];

        $this->postJson('/api/v1/lessons', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['status', 'message', 'data' => ['id', 'title', 'module_id']]);

        $this->assertDatabaseHas('lessons', ['title' => 'Intro to Safety', 'module_id' => $module->id]);
    }

   public function test_instructor_can_update_lesson()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $module = Module::factory()->create();
        $lesson = Lesson::factory()->create(['module_id' => $module->id, 'title' => 'Old Title']);

        Sanctum::actingAs($instructor, ['*']);

        $payload = [
            'module_id' => $module->id,        // include required module_id
            'title' => 'Updated Title',
            'content' => 'Updated content'
        ];

        $this->putJson("/api/v1/lessons/{$lesson->id}", $payload)
            ->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'title' => 'Updated Title']);
    }

    public function test_instructor_can_delete_lesson()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $lesson = Lesson::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        $this->deleteJson("/api/v1/lessons/{$lesson->id}")
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }
}
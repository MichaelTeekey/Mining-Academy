<?php

namespace Tests\Feature\MediaFile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Lesson;
use App\Models\MediaFile;
use Laravel\Sanctum\Sanctum;

class MediaFileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_media_files()
    {
        MediaFile::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/media-files')
            ->assertStatus(200);

        // flexible: ensure at least the 3 we created are returned
        $this->assertGreaterThanOrEqual(3, count($response->json()));
    }

    public function test_public_can_view_media_file()
    {
        $media = MediaFile::factory()->create();

        $this->getJson("/api/v1/media-files/{$media->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $media->id]);
    }

    public function test_guest_cannot_create_media_file()
    {
        $lesson = Lesson::factory()->create();

        $payload = MediaFile::factory()->make(['lesson_id' => $lesson->id])->toArray();

        $this->postJson('/api/v1/media-files', $payload)
            ->assertStatus(401);
    }

    public function test_instructor_can_create_media_file()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);
        $lesson = Lesson::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        // Use factory->make() to build a payload that matches your model/factory fields
        $payload = MediaFile::factory()->make(['lesson_id' => $lesson->id])->toArray();

        $response = $this->postJson('/api/v1/media-files', $payload);

        if ($response->status() !== 201) {
            $response->dump(); // helpful for debugging validation issues
        }

        $response->assertStatus(201);

        // assert API returned an id and that record exists in DB
        $createdId = $response->json('id') ?? $response->json('data.id') ?? null;
        $this->assertNotNull($createdId, 'API did not return created resource id');
        $this->assertDatabaseHas('media_files', ['id' => $createdId]);
    }

    public function test_instructor_can_update_media_file()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);

        $media = MediaFile::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        // build an update payload from factory (only fields, not id/timestamps)
        $updatePayload = MediaFile::factory()->make()->toArray();
        // remove any id/timestamps if present
        unset($updatePayload['id'], $updatePayload['created_at'], $updatePayload['updated_at']);

        $response = $this->putJson("/api/v1/media-files/{$media->id}", $updatePayload);

        if ($response->status() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200);

        $this->assertDatabaseHas('media_files', ['id' => $media->id]);
    }

    public function test_instructor_can_delete_media_file()
    {
        $org = Organization::factory()->create();
        $instructor = User::factory()->create([
            'account_type' => 'instructor',
            'organization_id' => $org->id,
        ]);
        $media = MediaFile::factory()->create();

        Sanctum::actingAs($instructor, ['*']);

        $this->deleteJson("/api/v1/media-files/{$media->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
    }
}
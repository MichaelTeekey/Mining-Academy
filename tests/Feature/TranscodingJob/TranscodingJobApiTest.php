<?php

namespace Tests\Feature\TranscodingJob;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\TranscodingJob;
use Laravel\Sanctum\Sanctum;

class TranscodingJobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_list_transcoding_jobs()
    {
        TranscodingJob::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/transcoding-jobs')
            ->assertStatus(200)
            ->assertJsonStructure(['status','data']);

        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    public function test_public_can_view_transcoding_job()
    {
        $job = TranscodingJob::factory()->create();

        $this->getJson("/api/v1/transcoding-jobs/{$job->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $job->id]);
    }

    public function test_guest_cannot_create_transcoding_job()
    {
        $payload = TranscodingJob::factory()->make()->toArray();

        $this->postJson('/api/v1/transcoding-jobs', $payload)
            ->assertStatus(401);
    }

    public function test_authenticated_can_create_transcoding_job()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $payload = TranscodingJob::factory()->make()->toArray();

        $response = $this->postJson('/api/v1/transcoding-jobs', $payload);

        if ($response->status() !== 201) {
            $response->dump();
        }

        $response->assertStatus(201);
        $createdId = $response->json('data.id') ?? $response->json('id') ?? null;
        $this->assertNotNull($createdId);
        $this->assertDatabaseHas('transcoding_jobs', ['id' => $createdId]);
    }

    public function test_authenticated_can_update_transcoding_job()
    {
        $user = User::factory()->create();
        $job = TranscodingJob::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $payload = TranscodingJob::factory()->make()->toArray();

        $response = $this->putJson("/api/v1/transcoding-jobs/{$job->id}", $payload);

        if ($response->status() !== 200) {
            $response->dump();
        }

        $response->assertStatus(200);
        $this->assertDatabaseHas('transcoding_jobs', ['id' => $job->id]);
    }

    public function test_authenticated_can_delete_transcoding_job()
    {
        $user = User::factory()->create();
        $job = TranscodingJob::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->deleteJson("/api/v1/transcoding-jobs/{$job->id}")
            ->assertStatus(200)
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('transcoding_jobs', ['id' => $job->id]);
    }
}
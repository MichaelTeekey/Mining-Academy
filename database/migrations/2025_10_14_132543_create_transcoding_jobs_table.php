<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transcoding_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('video_id')->constrained('videos')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->json('settings')->nullable(); // e.g., resolutions/formats
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcoding_jobs');
    }
};

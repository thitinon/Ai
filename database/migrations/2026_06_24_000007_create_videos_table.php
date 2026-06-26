<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->string('original_path')->nullable();
            $table->string('hls_path')->nullable(); // s3://bucket/path/to/master.m3u8
            $table->string('poster_path')->nullable(); // s3://bucket/path/to/poster.jpg
            $table->integer('duration_seconds')->default(0);
            $table->bigInteger('size_bytes')->default(0);
            $table->string('bitrate')->nullable(); // e.g. "5000k"
            $table->enum('status', ['processing', 'ready', 'failed'])->default('processing');
            $table->json('metadata')->nullable(); // codec, resolution, etc
            $table->timestamps();

            $table->index(['lesson_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

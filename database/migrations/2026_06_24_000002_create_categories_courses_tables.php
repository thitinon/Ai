<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('parent_id');
        });

        // courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->json('requirements')->nullable();
            $table->json('objectives')->nullable();
            $table->text('target_audience')->nullable();
            $table->enum('level', ['beginner','intermediate','advanced','all'])->default('all');
            $table->string('language')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->enum('status', ['draft','review','published','archived'])->default('draft');
            $table->boolean('is_free')->default(false);
            $table->boolean('certificate_enabled')->default(false);
            $table->integer('total_duration_seconds')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->unsignedBigInteger('enrolled_count')->default(0);
            $table->float('rating_avg')->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes(); // soft deletes for courses
            $table->timestamps();

            $table->index(['instructor_id','category_id']);
            $table->index('status');
            $table->index('published_at');
        });

        // course_tags
        Schema::create('course_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('tag');
            $table->timestamps();

            $table->unique(['course_id','tag']);
        });

        // sections
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();

            $table->index(['course_id','sort_order']);
        });

        // lessons
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->nullable();
            $table->enum('type', ['video','text','quiz','assignment'])->default('video');
            $table->longText('content')->nullable(); // for text lessons or extra data
            $table->string('video_url')->nullable(); // S3/hls url or external
            $table->integer('video_duration_seconds')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['section_id','sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('course_tags');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('categories');
    }
};

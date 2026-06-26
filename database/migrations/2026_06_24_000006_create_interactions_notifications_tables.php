<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id','course_id']);
            $table->index('rating');
        });

        // review_helpful (pivot)
        Schema::create('review_helpful', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['user_id','review_id']);
        });

        // wishlist
        Schema::create('wishlist', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamps();

            $table->primary(['user_id','course_id']);
        });

        // announcements
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        // questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->longText('question');
            $table->unsignedInteger('upvotes')->default(0);
            $table->timestamps();

            $table->index(['lesson_id','user_id']);
        });

        // answers
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->longText('answer');
            $table->boolean('is_instructor')->default(false);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();

            $table->index(['question_id','is_accepted']);
        });

        // notification_preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // e.g. course_updates, announcements, purchase_receipt
            $table->boolean('email_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id','type']);
        });

        // Laravel notifications table (if not present)
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('wishlist');
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('reviews');
    }
};

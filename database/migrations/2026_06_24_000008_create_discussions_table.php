<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->unsignedInteger('reply_count')->default(0);
            $table->timestamps();

            $table->index(['lesson_id', 'is_pinned']);
        });

        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('discussions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->longText('content');
            $table->boolean('is_instructor')->default(false);
            $table->boolean('is_accepted_answer')->default(false);
            $table->timestamps();

            $table->index(['discussion_id', 'is_accepted_answer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
        Schema::dropIfExists('discussions');
    }
};

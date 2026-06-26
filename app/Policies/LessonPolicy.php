<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lesson;

class LessonPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;
        return $course->status === 'published' || $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'instructor' || $user->role === 'admin';
    }

    public function update(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }
}

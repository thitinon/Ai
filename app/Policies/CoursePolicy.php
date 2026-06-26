<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        // Anyone can view published courses
        if ($course->status === 'published') {
            return true;
        }

        // Only instructor or admin can view draft/review courses
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'instructor' || $user->role === 'admin';
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    public function enroll(User $user, Course $course): bool
    {
        // Students can enroll in published courses
        return $user->role === 'student' && $course->status === 'published';
    }
}

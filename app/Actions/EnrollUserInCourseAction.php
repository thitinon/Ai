<?php

namespace App\Actions;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class EnrollUserInCourseAction
{
    public function execute(int $userId, int $courseId): ?Enrollment
    {
        return DB::transaction(function () use ($userId, $courseId) {
            // Check if already enrolled
            $existing = Enrollment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            if ($existing) {
                return $existing;
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'enrolled_at' => now(),
            ]);

            // Update course enrolled count
            Course::find($courseId)->increment('enrolled_count');

            // Fire event for notifications
            event(new \App\Events\UserEnrolledInCourse($enrollment));

            return $enrollment;
        });
    }
}

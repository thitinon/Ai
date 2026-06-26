<?php

namespace App\Actions;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class PublishCourseAction
{
    public function execute(int $courseId): Course
    {
        return DB::transaction(function () use ($courseId) {
            $course = Course::find($courseId);

            $course->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            // Fire event for notifications
            event(new \App\Events\CoursePublished($course));

            return $course->refresh();
        });
    }
}

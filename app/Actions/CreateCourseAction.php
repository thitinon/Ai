<?php

namespace App\Actions;

use App\DTOs\CourseDTO;
use App\Models\Course;
use App\Models\CourseTag;
use Illuminate\Support\Facades\DB;

class CreateCourseAction
{
    public function execute(CourseDTO $dto, array $tags = []): Course
    {
        return DB::transaction(function () use ($dto, $tags) {
            $course = Course::create($dto->toArray());

            // Add tags
            if (! empty($tags)) {
                foreach ($tags as $tag) {
                    CourseTag::create([
                        'course_id' => $course->id,
                        'tag' => $tag,
                    ]);
                }
            }

            // Fire event
            event(new \App\Events\CourseCreated($course));

            return $course;
        });
    }
}

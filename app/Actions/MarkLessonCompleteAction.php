<?php

namespace App\Actions;

use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\DB;

class MarkLessonCompleteAction
{
    public function execute(int $userId, int $lessonId): LessonProgress
    {
        return DB::transaction(function () use ($userId, $lessonId) {
            $progress = LessonProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'lesson_id' => $lessonId,
                ],
                [
                    'is_completed' => true,
                    'completed_at' => now(),
                ]
            );

            // Update course progress
            $lesson = Lesson::find($lessonId);
            $section = $lesson->section;
            $course = $section->course;

            $enrollments = $course->enrollments()->where('user_id', $userId)->get();

            foreach ($enrollments as $enrollment) {
                $this->updateEnrollmentProgress($enrollment);
            }

            return $progress;
        });
    }

    private function updateEnrollmentProgress($enrollment): void
    {
        $course = $enrollment->course;
        $completedLessons = LessonProgress::whereHas('lesson.section', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })
            ->where('user_id', $enrollment->user_id)
            ->where('is_completed', true)
            ->count();

        $totalLessons = $course->total_lessons ?: 1;
        $progressPercent = ($completedLessons / $totalLessons) * 100;

        $enrollment->update([
            'progress_percent' => min($progressPercent, 100),
            'last_accessed_at' => now(),
        ]);

        // Fire event
        event(new \App\Events\EnrollmentProgressUpdated($enrollment));
    }
}

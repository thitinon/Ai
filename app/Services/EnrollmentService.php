<?php

namespace App\Services;

use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentService
{
    public function __construct(
        protected EnrollmentRepositoryInterface $enrollmentRepository
    ) {}

    public function getUserEnrollments(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->enrollmentRepository->getByUser($userId, $perPage);
    }

    public function getEnrollment(int $userId, int $courseId)
    {
        return $this->enrollmentRepository->getByUserAndCourse($userId, $courseId);
    }

    public function enrollUser(int $userId, int $courseId, array $data = []): \App\Models\Enrollment
    {
        return $this->enrollmentRepository->create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            ...$data,
        ]);
    }

    public function updateProgress(int $enrollmentId, float $progressPercent)
    {
        return $this->enrollmentRepository->update($enrollmentId, [
            'progress_percent' => min($progressPercent, 100),
            'last_accessed_at' => now(),
        ]);
    }

    public function completeEnrollment(int $enrollmentId)
    {
        return $this->enrollmentRepository->update($enrollmentId, [
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);
    }

    public function getActiveEnrollments(int $userId): Collection
    {
        return $this->enrollmentRepository->getActive($userId);
    }

    public function getCompletedEnrollments(int $userId): Collection
    {
        return $this->enrollmentRepository->getCompleted($userId);
    }
}

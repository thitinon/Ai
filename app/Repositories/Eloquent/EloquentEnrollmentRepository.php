<?php

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentEnrollmentRepository implements EnrollmentRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return Enrollment::where('user_id', $userId)
            ->with('course', 'course.instructor')
            ->latest('enrolled_at')
            ->paginate($perPage);
    }

    public function getByUserAndCourse(int $userId, int $courseId): ?Enrollment
    {
        return Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->with('course')
            ->first();
    }

    public function findById(int $id): ?Enrollment
    {
        return Enrollment::with('user', 'course')
            ->find($id);
    }

    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function update(int $id, array $data): Enrollment
    {
        $enrollment = Enrollment::find($id);
        $enrollment->update($data);
        return $enrollment->refresh();
    }

    public function delete(int $id): bool
    {
        $enrollment = Enrollment::find($id);
        return $enrollment ? $enrollment->delete() : false;
    }

    public function getActive(int $userId): Collection
    {
        return Enrollment::where('user_id', $userId)
            ->active()
            ->with('course')
            ->get();
    }

    public function getCompleted(int $userId): Collection
    {
        return Enrollment::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->with('course')
            ->get();
    }
}

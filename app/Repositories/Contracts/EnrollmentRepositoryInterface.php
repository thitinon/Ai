<?php

namespace App\Repositories\Contracts;

use App\Models\Enrollment;
use Illuminate\Pagination\LengthAwarePaginator;

interface EnrollmentRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 12): LengthAwarePaginator;
    public function getByUserAndCourse(int $userId, int $courseId): ?Enrollment;
    public function findById(int $id): ?Enrollment;
    public function create(array $data): Enrollment;
    public function update(int $id, array $data): Enrollment;
    public function delete(int $id): bool;
    public function getActive(int $userId): \Illuminate\Database\Eloquent\Collection;
    public function getCompleted(int $userId): \Illuminate\Database\Eloquent\Collection;
}

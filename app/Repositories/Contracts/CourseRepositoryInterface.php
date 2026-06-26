<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CourseRepositoryInterface
{
    public function getPublished(int $perPage = 12): LengthAwarePaginator;
    public function getFeatured(int $limit = 6): Collection;
    public function getByInstructor(int $instructorId, int $perPage = 12): LengthAwarePaginator;
    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator;
    public function findById(int $id): ?Course;
    public function findBySlug(string $slug): ?Course;
    public function search(string $query, int $perPage = 12): LengthAwarePaginator;
    public function create(array $data): Course;
    public function update(int $id, array $data): Course;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function getStats(): array;
}

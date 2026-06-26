<?php

namespace App\Services;

use App\Repositories\Contracts\CourseRepositoryInterface;
use App\DTOs\CourseDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    public function __construct(
        protected CourseRepositoryInterface $courseRepository
    ) {}

    public function listPublished(int $perPage = 12): LengthAwarePaginator
    {
        return $this->courseRepository->getPublished($perPage);
    }

    public function listByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->courseRepository->getByCategory($categoryId, $perPage);
    }

    public function listByInstructor(int $instructorId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->courseRepository->getByInstructor($instructorId, $perPage);
    }

    public function getFeaturedCourses(int $limit = 6): Collection
    {
        return $this->courseRepository->getFeatured($limit);
    }

    public function findById(int $id)
    {
        return $this->courseRepository->findById($id);
    }

    public function findBySlug(string $slug)
    {
        return $this->courseRepository->findBySlug($slug);
    }

    public function search(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return $this->courseRepository->search($query, $perPage);
    }

    public function createCourse(CourseDTO $dto)
    {
        return $this->courseRepository->create($dto->toArray());
    }

    public function updateCourse(int $id, CourseDTO $dto)
    {
        return $this->courseRepository->update($id, $dto->toArray());
    }

    public function deleteCourse(int $id): bool
    {
        return $this->courseRepository->delete($id);
    }

    public function restoreCourse(int $id): bool
    {
        return $this->courseRepository->restore($id);
    }

    public function publishCourse(int $id)
    {
        return $this->courseRepository->update($id, [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function getStats(): array
    {
        return $this->courseRepository->getStats();
    }
}

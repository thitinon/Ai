<?php

namespace App\Repositories\Eloquent;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentCourseRepository implements CourseRepositoryInterface
{
    public function getPublished(int $perPage = 12): LengthAwarePaginator
    {
        return Course::published()
            ->with('instructor', 'category')
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Course::published()
            ->where('enrolled_count', '>', 100)
            ->orderByDesc('enrolled_count')
            ->limit($limit)
            ->get();
    }

    public function getByInstructor(int $instructorId, int $perPage = 12): LengthAwarePaginator
    {
        return Course::where('instructor_id', $instructorId)
            ->with('category')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return Course::published()
            ->where('category_id', $categoryId)
            ->with('instructor')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Course
    {
        return Course::with('instructor', 'category', 'sections.lessons')
            ->find($id);
    }

    public function findBySlug(string $slug): ?Course
    {
        return Course::where('slug', $slug)
            ->with('instructor', 'category', 'sections.lessons')
            ->first();
    }

    public function search(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return Course::published()
            ->search($query)
            ->paginate($perPage);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(int $id, array $data): Course
    {
        $course = $this->findById($id);
        $course->update($data);
        return $course->refresh();
    }

    public function delete(int $id): bool
    {
        $course = Course::find($id);
        return $course ? $course->delete() : false;
    }

    public function restore(int $id): bool
    {
        $course = Course::withTrashed()->find($id);
        return $course ? (bool) $course->restore() : false;
    }

    public function getStats(): array
    {
        return [
            'total_courses' => Course::count(),
            'published_courses' => Course::published()->count(),
            'total_students' => Course::published()->sum('enrolled_count'),
            'avg_rating' => Course::published()->avg('rating_avg'),
        ];
    }
}

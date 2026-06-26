<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getStudents(int $perPage = 15): LengthAwarePaginator
    {
        return User::students()
            ->with('profile')
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getInstructors(int $perPage = 15): LengthAwarePaginator
    {
        return User::instructors()
            ->with('profile', 'courses')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with('profile', 'courses', 'enrollments')
            ->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::find($id);
        $user->update($data);
        return $user->refresh();
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        return $user ? $user->delete() : false;
    }

    public function getTopInstructors(int $limit = 10): Collection
    {
        return User::instructors()
            ->withCount('courses')
            ->orderByDesc('courses_count')
            ->limit($limit)
            ->get();
    }
}

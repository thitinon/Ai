<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getStudents(int $perPage = 15): LengthAwarePaginator;
    public function getInstructors(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
    public function getTopInstructors(int $limit = 10): Collection;
}

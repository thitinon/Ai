<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function listStudents(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getStudents($perPage);
    }

    public function listInstructors(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getInstructors($perPage);
    }

    public function getUserById(int $id)
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function createUser(array $data)
    {
        $data['password'] = bcrypt($data['password'] ?? 'password');
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getTopInstructors(int $limit = 10): Collection
    {
        return $this->userRepository->getTopInstructors($limit);
    }
}

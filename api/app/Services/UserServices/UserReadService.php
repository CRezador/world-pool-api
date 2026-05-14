<?php

namespace App\Services\UserServices;

use App\Models\User;
use App\Repositories\UserRepositories\UserRepository;

class UserReadService
{
    public function __construct(private UserRepository $userRepository) {}

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }
}

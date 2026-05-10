<?php

namespace App\Services\UserServices;

use App\Http\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function login(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('Usuário não encontrado.', 404);
        }

        if (!Hash::check($password, $user->password)) {
            throw new \Exception('Credenciais inválidas.', 401);
        }

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($data['password']),
        ]);
    }

    public function update(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function updateRole(User $user, UserRole $role): User
    {
        return $this->userRepository->updateRole($user, $role);
    }
}

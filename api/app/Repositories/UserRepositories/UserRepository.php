<?php

namespace App\Repositories\UserRepositories;

use App\Http\Enums\UserRole;
use App\Models\User;

class UserRepository
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh();
    }

    public function updateRole(User $user, UserRole $role): User
    {
        $user->update(['role' => $role]);
        return $user->fresh();
    }
}

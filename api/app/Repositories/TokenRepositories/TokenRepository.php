<?php

namespace App\Repositories\TokenRepositories;

use App\Models\User;

class TokenRepository
{
    public function createToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}

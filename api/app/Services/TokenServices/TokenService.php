<?php

namespace App\Services\TokenServices;

use App\Models\User;
use App\Repositories\TokenRepositories\TokenRepository;

class TokenService
{
    public function __construct(
        private TokenRepository $tokenRepository
    ) {}

    public function createToken(User $user): string
    {
        return $this->tokenRepository->createToken($user);
    }

    public function revokeCurrentToken(User $user): void
    {
        $this->tokenRepository->revokeCurrentToken($user);
    }
}

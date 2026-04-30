<?php

namespace App\Services\TokenServices;

use App\Repositories\TokenRepositories\TokenRepository;

class TokenService
{
    public function __construct(
        private TokenRepository $tokenRepository
    ) {}
}

<?php

namespace App\Services\GuessServices;

use App\Repositories\GuessRepositories\GuessRepository;

class GuessService
{
    public function __construct(
        private GuessRepository $guessRepository
    ) {}
}

<?php

namespace App\Services\LeaderboardServices;

use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;

class LeaderboardWriteService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
        private GuessRepository $guessRepository,
    ) {}
}

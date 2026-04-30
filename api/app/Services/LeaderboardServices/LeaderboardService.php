<?php

namespace App\Services\LeaderboardServices;

use App\Repositories\LeaderboardRepositories\LeaderboardRepository;

class LeaderboardService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository
    ) {}
}

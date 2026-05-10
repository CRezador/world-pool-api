<?php

namespace App\Services\LeaderboardServices;

use App\Models\Leaderboard;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LeaderboardReadService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
    ) {}

    // delega ao repository com perPage=20
    public function ranking(int $poolId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->leaderboardRepository->getPaginated($poolId, $perPage);
    }
    
    public function top(int $poolId, int $limit = 3): Collection
    {
        if($limit < 3) {
            $limit = 3;
        }

        if($limit > 10){
            throw new \InvalidArgumentException('O limite deve ser entre 3 e 10', 422);
        }

        return $this->leaderboardRepository->getTop($poolId, $limit);
    }

    // retorna entry + rank position do usuário autenticado
    public function myPosition(int $poolId, int $userId): array
    {
        $entry = $this->leaderboardRepository->getByUser($poolId, $userId);

        if (!$entry) {
            return [
                'entry' => null,
                'rank_position' => 0,
            ];
        }

        $rankPosition = $this->leaderboardRepository->getRankPosition($poolId, $userId);

        return [
            'entry' => $entry,
            'rank_position' => $rankPosition,
        ];
    }

    // retorna entry + rank position de um membro específico; lança 404 se não encontrado
    public function show(int $poolId, int $userId): array
    {
        $entry = $this->leaderboardRepository->getByUser($poolId, $userId);

        if (!$entry) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Entry não encontrado');
        }

        $rankPosition = $this->leaderboardRepository->getRankPosition($poolId, $userId);

        return [
            'entry' => $entry,
            'rank_position' => $rankPosition,
        ];
    }
}

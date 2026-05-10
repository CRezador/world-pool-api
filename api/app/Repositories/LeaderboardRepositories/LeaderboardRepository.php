<?php

namespace App\Repositories\LeaderboardRepositories;

use App\Models\Leaderboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaderboardRepository
{
    public function getPaginated(int $poolId, int $perPage): LengthAwarePaginator
    {
        return $this->rankedQuery($poolId)->with('user:id,name')->paginate($perPage);
    }

    public function getTop(int $poolId, int $limit): Collection
    {
        return $this->rankedQuery($poolId)->with('user:id,name')->limit($limit)->get();
    }

    public function getByUser(int $poolId, int $userId): ?Leaderboard
    {
        return Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->with('user:id,name')
            ->first();
    }

    public function getAllByPool(int $poolId): Collection
    {
        return Leaderboard::where('pool_id', $poolId)
            ->whereNull('archived_at')
            ->get();
    }
    
    public function getStatsByUser(int $poolId, int $userId): ?array
    {
        $entry = Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->first();

        if (!$entry) {
            return null;
        }

        return [
            'points' => $entry->points,
            'exact_hits' => $entry->exact_hits,
            'result_hits' => $entry->result_hits,
            'guesses_count' => $entry->guesses_count,
        ];
    }

    public function getRankPosition(int $poolId, int $userId): int
    {
        $entry = $this->getByUser($poolId, $userId);

        if (!$entry) {
            return 0;
        }

        $above = Leaderboard::where('pool_id', $poolId)
            ->whereNull('archived_at')
            ->where(function (Builder $q) use ($entry) {
                $q->where('points', '>', $entry->points)
                    ->orWhere(function (Builder $q) use ($entry) {
                        $q->where('points', $entry->points)
                            ->where('exact_hits', '>', $entry->exact_hits);
                    })
                    ->orWhere(function (Builder $q) use ($entry) {
                        $q->where('points', $entry->points)
                            ->where('exact_hits', $entry->exact_hits)
                            ->where('result_hits', '>', $entry->result_hits);
                    })
                    ->orWhere(function (Builder $q) use ($entry) {
                        $q->where('points', $entry->points)
                            ->where('exact_hits', $entry->exact_hits)
                            ->where('result_hits', $entry->result_hits)
                            ->where('guesses_count', '>', $entry->guesses_count);
                    });
            })
            ->count();

        return $above + 1;
    }

    public function createEntry(int $poolId, int $userId): Leaderboard
    {
        return Leaderboard::create([
            'pool_id'       => $poolId,
            'user_id'       => $userId,
            'points'        => 0,
            'exact_hits'    => 0,
            'result_hits'   => 0,
            'guesses_count' => 0,
        ]);
    }

    public function deleteEntry(int $poolId, int $userId): void
    {
        Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->delete();
    }

    public function archiveEntry(int $poolId, int $userId): void
    {
        Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update(['archived_at' => now()]);
    }

    public function restoreEntry(int $poolId, int $userId): void
    {
        Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update(['archived_at' => null]);
    }

    public function updateStats(int $poolId, int $userId, array $stats): void
    {
        Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update($stats);
    }

    private function rankedQuery(int $poolId): Builder
    {
        return Leaderboard::where('pool_id', $poolId)
            ->whereNull('archived_at')
            ->orderBy('points', 'desc')
            ->orderBy('exact_hits', 'desc')
            ->orderBy('result_hits', 'desc')
            ->orderBy('guesses_count', 'desc');
    }
}

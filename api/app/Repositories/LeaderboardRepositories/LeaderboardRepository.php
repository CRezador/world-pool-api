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

    public function getLeader(int $poolId): ?Leaderboard
    {
        return $this->rankedQuery($poolId)
            ->with('user:id,name')
            ->first();
    }

    public function getByUser(int $poolId, int $userId): ?Leaderboard
    {
        $entry = Leaderboard::where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->with('user:id,name')
            ->first();

        if ($entry) {
            $entry->position = $this->computeRank($poolId, $entry);
        }

        return $entry;
    }

    private function computeRank(int $poolId, Leaderboard $entry): int
    {
        return Leaderboard::where('pool_id', $poolId)
            ->whereNull('archived_at')
            ->where(function ($q) use ($entry) {
                $q->where('points', '>', $entry->points)
                  ->orWhere(fn($q) => $q
                      ->where('points', $entry->points)
                      ->where('exact_hits', '>', $entry->exact_hits))
                  ->orWhere(fn($q) => $q
                      ->where('points', $entry->points)
                      ->where('exact_hits', $entry->exact_hits)
                      ->where('result_hits', '>', $entry->result_hits))
                  ->orWhere(fn($q) => $q
                      ->where('points', $entry->points)
                      ->where('exact_hits', $entry->exact_hits)
                      ->where('result_hits', $entry->result_hits)
                      ->where('guesses_count', '>', $entry->guesses_count));
            })
            ->count() + 1;
    }

    public function getAllByPool(int $poolId): Collection
    {
        return Leaderboard::where('pool_id', $poolId)
            ->whereNull('archived_at')
            ->get();
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

    public function updateRanks(int $poolId): void
    {
        $entries = $this->rankedQuery($poolId)->get();

        foreach ($entries as $index => $entry) {
            $entry->update([
                'previous_position' => $entry->position,
                'position'          => $index + 1,
            ]);
        }
    }

    public function getStatsByUser(int $userId): object
    {
        return Leaderboard::where('user_id', $userId)
            ->whereNull('archived_at')
            ->selectRaw('
                COUNT(*) as pools_count,
                COALESCE(SUM(points), 0) as total_points,
                COALESCE(SUM(exact_hits), 0) as total_exact_hits,
                COALESCE(SUM(result_hits), 0) as total_result_hits,
                COALESCE(SUM(guesses_count), 0) as total_guesses,
                MIN(position) as best_rank
            ')
            ->first();
    }

    private function rankedQuery(int $poolId): Builder
    {
        return Leaderboard::where('leaderboard.pool_id', $poolId)
            ->whereNull('leaderboard.archived_at')
            ->join('pool_members', function ($join) use ($poolId) {
                $join->on('pool_members.user_id', '=', 'leaderboard.user_id')
                     ->where('pool_members.pool_id', '=', $poolId)
                     ->where('pool_members.status', '=', 'ACTIVE');
            })
            ->select('leaderboard.*', 'pool_members.role as member_role')
            ->orderBy('leaderboard.points', 'desc')
            ->orderBy('leaderboard.exact_hits', 'desc')
            ->orderBy('leaderboard.result_hits', 'desc')
            ->orderBy('leaderboard.guesses_count', 'desc')
            // Tiebreaker estável: garante ordem determinística em empates perfeitos.
            ->orderBy('leaderboard.user_id', 'asc');
    }
}

<?php

namespace App\Repositories\GuessRepositories;

use App\Http\Enums\GuessPoints;
use App\Http\Enums\MatchStatus;
use App\Models\Guess;
use App\Models\PoolMember;
use Illuminate\Database\Eloquent\Collection;

class GuessRepository
{
    public function create(array $data): Guess
    {
        return Guess::create($data);
    }

    public function findById(int $id): ?Guess
    {
        return Guess::find($id);
    }

    public function updateById(int $id, array $data): Guess
    {
        $guess = $this->findById($id);
        if (!$guess) {
            throw new \Exception('Palpite não encontrado.', 404);
        }
        $guess->update($data);

        return $guess;
    }

    public function deleteById(int $id): bool
    {
        $guess = $this->findById($id);
        if ($guess) {
            $guess->delete();
            return true;
        }

        return false;
    }

    public function getByUser(int $userId): Collection
    {
        return Guess::where('user_id', $userId)
            ->with('match.homeTeam', 'match.awayTeam')
            ->orderBy('match_id')
            ->get();
    }

    public function getByUserAndMatch(int $userId, int $matchId): ?Guess
    {
        return Guess::where('user_id', $userId)
            ->where('match_id', $matchId)
            ->first();
    }

    public function getByMatch(int $matchId): Collection
    {
        return Guess::where('match_id', $matchId)->get();
    }

    public function getByMatchInPool(int $matchId, int $poolId): Collection
    {
        $memberUserIds = PoolMember::where('pool_id', $poolId)->pluck('user_id');

        return Guess::where('match_id', $matchId)
            ->whereIn('user_id', $memberUserIds)
            ->get();
    }

    public function getByUserInPool(int $userId, int $poolId): Collection
    {
        return $this->getByUser($userId);
    }

    public function getLastScoredByUser(int $userId, int $limit = 3): array
    {
        return Guess::where('user_id', $userId)
            ->whereNotNull('points')
            ->join('matches', 'matches.id', '=', 'guesses.match_id')
            ->orderBy('matches.kickoff_at', 'desc')
            ->limit($limit)
            ->pluck('guesses.points')
            ->toArray();
    }

    public function recentActivityForPools(array $poolIds, int $limit = 20): Collection
    {
        $memberUserIds = PoolMember::whereIn('pool_id', $poolIds)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        return Guess::whereIn('user_id', $memberUserIds)
            ->with(['user', 'match.homeTeam', 'match.awayTeam'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function aggregateStatsByUser(int $userId): array
    {
        $result = Guess::where('guesses.user_id', $userId)
            ->join('matches', 'matches.id', '=', 'guesses.match_id')
            ->where('matches.status', MatchStatus::FINISHED->value)
            ->selectRaw('
                COALESCE(SUM(guesses.points), 0) as points,
                COUNT(CASE WHEN guesses.points = ? THEN 1 END) as exact_hits,
                COUNT(CASE WHEN guesses.points = ? THEN 1 END) as result_hits,
                COUNT(guesses.id) as guesses_count
            ', [GuessPoints::EXACT->value, GuessPoints::RESULT->value])
            ->first();

        return [
            'points'        => (int) $result->points,
            'exact_hits'    => (int) $result->exact_hits,
            'result_hits'   => (int) $result->result_hits,
            'guesses_count' => (int) $result->guesses_count,
        ];
    }
}

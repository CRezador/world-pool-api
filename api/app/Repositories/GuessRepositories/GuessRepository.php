<?php

namespace App\Repositories\GuessRepositories;

use App\Http\Enums\GuessPoints;
use App\Http\Enums\MatchStatus;
use App\Models\Guess;
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

    public function getByUserAndPool(int $userId, int $poolId): Collection
    {
        return Guess::where('user_id', $userId)
            ->where('pool_id', $poolId)
            ->get();
    }

    public function getByMatchAndPool(int $matchId, int $poolId): Collection
    {
        return Guess::where('match_id', $matchId)
            ->where('pool_id', $poolId)
            ->get();
    }

    public function getByMatch(int $matchId): Collection
    {
        return Guess::where('match_id', $matchId)->get();
    }

    public function getByMemberAndPool(int $userId, int $poolId): Collection
    {
        return Guess::where('user_id', $userId)
            ->where('pool_id', $poolId)
            ->get();
    }

    public function aggregateStatsByUserAndPool(int $poolId, int $userId): array
    {
        $result = Guess::where('guesses.pool_id', $poolId)
            ->where('guesses.user_id', $userId)
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

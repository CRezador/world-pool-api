<?php

namespace App\Repositories\GuessRepositories;

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
}

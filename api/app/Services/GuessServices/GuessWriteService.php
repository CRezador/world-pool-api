<?php

namespace App\Services\GuessServices;

use App\Models\Guess;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;

class GuessWriteService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository
    ) {}

    private function findGuessOrFail(int $guessId): Guess
    {
        $guess = $this->guessRepository->findById($guessId);
        if (!$guess) {
            throw new \Exception('Palpite não encontrado.', 404);
        }
        return $guess;
    }

    private function assertOwnership(Guess $guess, int $userId): void
    {
        if ($guess->user_id !== $userId) {
            throw new \Exception('Não é possível realizar esta ação em um palpite de outro usuário.', 403);
        }
    }

    private function assertPool(Guess $guess, int $poolId): void
    {
        if ($guess->pool_id !== $poolId) {
            throw new \Exception('Palpite não pertence a este bolão.', 403);
        }
    }

    public function createGuess(array $data): Guess
    {
        $this->matchRepository->assertScheduled($data['match_id']);
        return $this->guessRepository->create($data);
    }

    public function updateGuess(int $id, int $userId, int $poolId, array $data): Guess
    {
        $guess = $this->findGuessOrFail($id);
        $this->assertOwnership($guess, $userId);
        $this->assertPool($guess, $poolId);
        $this->matchRepository->assertScheduled($guess->match_id);
        return $this->guessRepository->updateById($id, $data);
    }

    public function deleteGuess(int $guessId, int $userId, int $poolId): bool
    {
        $guess = $this->findGuessOrFail($guessId);
        $this->assertOwnership($guess, $userId);
        $this->assertPool($guess, $poolId);
        $this->matchRepository->assertScheduled($guess->match_id);
        return $this->guessRepository->deleteById($guessId);
    }
}

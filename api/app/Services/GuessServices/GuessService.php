<?php

namespace App\Services\GuessServices;

use App\Http\Enums\MatchStatus;
use App\Models\Guess;
use App\Models\Matches;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;
use Illuminate\Database\Eloquent\Collection;

class GuessService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository
    ) {}

    private function getMatchStatus(int $matchId): MatchStatus|null
    {
        return $this->matchRepository->getStatusById($matchId);
    }

    private function hasGuess(int $guessId): bool
    {
        $guess = $this->guessRepository->findById($guessId);
        return $guess && !$guess->isEmpty();
    }

    private function isUserGuess(Guess $guess, int $userId): bool
    {
        return $guess && $guess->user_id === $userId;
    }


    public function createGuess(array $data): Guess
    {
        $status = $this->getMatchStatus($data['match_id']);

        if ($status !== MatchStatus::SCHEDULED) {
            throw new \Exception('Não é possível criar um palpite para uma partida que não está agendada.', 400);
        }

        if ($status === MatchStatus::FINISHED) {
            throw new \Exception('Não é possível criar um palpite para uma partida que já foi finalizada.', 400);
        }
        
        return $this->guessRepository->create($data);
    }

    public function updateGuess(int $id, int $userId, array $data): Guess
    {
        $guess = $this->guessRepository->findById($id);
        $status = $this->getMatchStatus($guess->match_id);

        if (!$this->isUserGuess($guess, $userId)) {
            throw new \Exception('Não é possível atualizar um palpite de outro usuário.', 403);
        }

        if (!$this->hasGuess($id)) {
            throw new \Exception('Palpite não encontrado.', 404);
        }

        if ($status !== MatchStatus::SCHEDULED) {
            throw new \Exception('Não é possível atualizar um palpite para uma partida que não está agendada.', 400);
        }
        
        if ($status === MatchStatus::FINISHED) {
            throw new \Exception('Não é possível atualizar um palpite para uma partida que já foi finalizada.', 400);
        }

        return $this->guessRepository->updateById($id, $data);
    }

    public function deleteGuess(int $guessId, int $userId): bool
    {
        $guess = $this->guessRepository->findById($guessId);
        $status = $this->getMatchStatus($guess->match_id);

        if (!$this->isUserGuess($guess, $userId)) {
            throw new \Exception('Não é possível excluir um palpite de outro usuário.', 403);
        }

        if (!$this->hasGuess($guessId)) {
            throw new \Exception('Palpite não encontrado.', 404);
        }

        if ($status !== MatchStatus::SCHEDULED) {
            throw new \Exception('Não é possível excluir um palpite para uma partida que não está agendada.', 400);
        }

        if ($status === MatchStatus::FINISHED) {
            throw new \Exception('Não é possível excluir um palpite para uma partida que já foi finalizada.', 400);
        }

        return $this->guessRepository->deleteById($guessId);
    }

    public function getMyGuesses(int $poolId, int $userId): Collection
    {
        return $this->guessRepository->getByUserAndPool($userId, $poolId);
    }

    public function getMatchGuesses(int $matchId, int $poolId): Collection
    {
        $status = $this->getMatchStatus($matchId);

        if ($status !== MatchStatus::SCHEDULED) {
            throw new \Exception('Não é possível visualizar os palpites de uma partida que já começou.', 400);
        }

        return $this->guessRepository->getByMatchAndPool($matchId, $poolId);
    }

    public function getMemberGuesses(int $memberId, int $poolId): Collection
    {
        return $this->guessRepository->getByMemberAndPool($memberId, $poolId);
    }

    public function scoreGuess(Guess $guess, Matches $match): int
    {
        if ($match->home_score === $guess->home_score && $match->away_score === $guess->away_score) {
            return 3; // Acerto exato
        }

        if (
            ($match->home_score > $match->away_score && $guess->home_score > $guess->away_score) ||
            ($match->home_score < $match->away_score && $guess->home_score < $guess->away_score) ||
            ($match->home_score === $match->away_score && $guess->home_score === $guess->away_score)
        ) {
            return 1; // Acerto do resultado (vitória, derrota ou empate)
        }

        return 0; // Erro
    }

    public function scoreGuessesForMatch(Matches $match): void
    {
        $guesses = $this->guessRepository->getByMatch($match->id);

        foreach ($guesses as $guess) {
            $points = $this->scoreGuess($guess, $match);
            $this->guessRepository->updateById($guess->id, ['points' => $points]);
        }
    }
}

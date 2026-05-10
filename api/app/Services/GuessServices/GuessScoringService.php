<?php

namespace App\Services\GuessServices;

use App\Models\Guess;
use App\Models\Matches;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;

class GuessScoringService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository,
    ) {}

    public function scoreGuess(Guess $guess, Matches $match): int
    {
        if ($match->home_score === $guess->home_score && $match->away_score === $guess->away_score) {
            return 3;
        }

        if (
            ($match->home_score > $match->away_score && $guess->home_score > $guess->away_score)
            || ($match->home_score < $match->away_score && $guess->home_score < $guess->away_score)
            || ($match->home_score === $match->away_score && $guess->home_score === $guess->away_score)
        ) {
            return 1;
        }

        return 0;
    }

    public function scoreGuessesForMatch(int $matchId): void
    {
        $match = $this->matchRepository->findById($matchId);
        if (!$match) {
            throw new \Exception('Partida não encontrada.', 404);
        }

        $guesses = $this->guessRepository->getByMatch($matchId);

        foreach ($guesses as $guess) {
            $points = $this->scoreGuess($guess, $match);
            $this->guessRepository->updateById($guess->id, ['points' => $points]);
        }
    }
}

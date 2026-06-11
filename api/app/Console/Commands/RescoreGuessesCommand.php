<?php

namespace App\Console\Commands;

use App\Http\Enums\MatchStatus;
use App\Models\Matches;
use App\Models\Pool;
use App\Services\GuessServices\GuessScoringService;
use App\Services\LeaderboardServices\LeaderboardWriteService;
use Illuminate\Console\Command;

class RescoreGuessesCommand extends Command
{
    protected $signature = 'guesses:rescore';
    protected $description = 'Repontua os palpites de todas as partidas finalizadas e reconstrói os leaderboards';

    public function __construct(
        private GuessScoringService $guessScoringService,
        private LeaderboardWriteService $leaderboardWriteService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $matchIds = Matches::query()
            ->where('status', MatchStatus::FINISHED->value)
            ->pluck('id');

        foreach ($matchIds as $matchId) {
            $this->guessScoringService->scoreGuessesForMatch($matchId);
        }

        $this->info("✓ {$matchIds->count()} partida(s) finalizada(s) repontuada(s).");

        $poolIds = Pool::query()->pluck('id');

        foreach ($poolIds as $poolId) {
            $this->leaderboardWriteService->rebuild($poolId);
        }

        $this->info("✓ {$poolIds->count()} leaderboard(s) reconstruído(s).");

        return Command::SUCCESS;
    }
}

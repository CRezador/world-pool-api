<?php

namespace App\Console\Commands;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use App\Models\Group;
use App\Models\Matches;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportMatchesCommand extends Command
{
    protected $signature = 'matches:import';
    protected $description = 'Importa/atualiza partidas da Copa do Mundo a partir da API football-data.org';

    private const STAGE_MAP = [
        'GROUP_STAGE'    => MatchStage::GROUP_STAGE,
        'LAST_16'        => MatchStage::ROUND_OF_16,
        'QUARTER_FINALS' => MatchStage::QUARTER_FINALS,
        'SEMI_FINALS'    => MatchStage::SEMI_FINALS,
        'THIRD_PLACE'    => MatchStage::THIRD_PLACE,
        'FINAL'          => MatchStage::FINAL,
    ];

    private const STATUS_MAP = [
        'SCHEDULED' => MatchStatus::SCHEDULED,
        'TIMED'     => MatchStatus::SCHEDULED,
        'IN_PLAY'   => MatchStatus::IN_PROGRESS,
        'PAUSED'    => MatchStatus::IN_PROGRESS,
        'FINISHED'  => MatchStatus::FINISHED,
        'AWARDED'   => MatchStatus::FINISHED,
    ];

    public function handle(): int
    {
        $response = Http::withHeader('X-Auth-Token', config('services.football_data.token'))
            ->get('https://api.football-data.org/v4/competitions/WC/matches');

        if (!$response->successful()) {
            $this->error("Falha ao buscar partidas da API: {$response->status()}");
            return Command::FAILURE;
        }

        $teams  = Team::all()->keyBy('code');
        $groups = Group::all()->keyBy('name');

        $matches   = $response->json('matches', []);
        $imported  = 0;
        $skipped   = 0;

        foreach ($matches as $match) {
            $homeTla = $match['homeTeam']['tla'] ?? null;
            $awayTla = $match['awayTeam']['tla'] ?? null;

            $homeTeam = $homeTla ? $teams->get($homeTla) : null;
            $awayTeam = $awayTla ? $teams->get($awayTla) : null;

            if (!$homeTeam || !$awayTeam) {
                $skipped++;
                continue;
            }

            $stage  = self::STAGE_MAP[$match['stage']] ?? null;
            $status = self::STATUS_MAP[$match['status']] ?? MatchStatus::SCHEDULED;

            if (!$stage) {
                $skipped++;
                continue;
            }

            preg_match('/([A-L])$/', (string) ($match['group'] ?? ''), $m);
            $group = isset($m[1]) ? $groups->get($m[1]) : null;

            $score = $match['score']['fullTime'] ?? [];

            Matches::updateOrCreate(
                ['external_id' => $match['id']],
                [
                    'kickoff_at'   => $match['utcDate'],
                    'game_day'     => $match['matchday'],
                    'stage'        => $stage->value,
                    'group_id'     => $group?->id,
                    'status'       => $status->value,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'home_score'   => $score['home'] ?? null,
                    'away_score'   => $score['away'] ?? null,
                ]
            );

            $imported++;
        }

        $this->info("✓ {$imported} partidas importadas/atualizadas.");

        if ($skipped > 0) {
            $this->warn("  {$skipped} partidas ignoradas (times ainda não definidos ou fase desconhecida).");
        }

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use App\Models\Group;
use App\Models\Matches;
use App\Models\Team;
use App\Services\GuessServices\GuessScoringService;
use App\Services\StandingsServices\StandingsWriteService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportMatchesCommand extends Command
{
    protected $signature = 'matches:import';
    protected $description = 'Importa/atualiza partidas da Copa do Mundo a partir da API football-data.org';

    public function __construct(
        private GuessScoringService $guessScoringService,
        private StandingsWriteService $standingsWriteService,
    ) {
        parent::__construct();
    }

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

        $allTeams    = Team::all();
        $teamsByTla  = $allTeams->filter(fn($t) => $t->tla)->keyBy('tla');
        $teamsByCode = $allTeams->keyBy('code');
        $groups = Group::all()->keyBy('name');

        $previousStatusByExternal = Matches::query()
            ->whereNotNull('external_id')
            ->pluck('status', 'external_id');

        $matches   = $response->json('matches', []);
        $imported  = 0;
        $skipped   = 0;
        $toScore   = [];

        foreach ($matches as $match) {
            $homeTla = $match['homeTeam']['tla'] ?? null;
            $awayTla = $match['awayTeam']['tla'] ?? null;

            $homeTeam = $homeTla ? ($teamsByTla->get($homeTla) ?? $teamsByCode->get($homeTla)) : null;
            $awayTeam = $awayTla ? ($teamsByTla->get($awayTla) ?? $teamsByCode->get($awayTla)) : null;

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

            $previousStatus = $previousStatusByExternal->get($match['id']);

            $matchModel = Matches::updateOrCreate(
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

            if ($status === MatchStatus::FINISHED && $previousStatus !== MatchStatus::FINISHED) {
                $toScore[] = $matchModel->id;
            }

            $imported++;
        }

        $this->info("✓ {$imported} partidas importadas/atualizadas.");

        if ($skipped > 0) {
            $this->warn("  {$skipped} partidas ignoradas (times ainda não definidos ou fase desconhecida).");
        }

        foreach ($toScore as $matchId) {
            $this->guessScoringService->scoreGuessesForMatch($matchId);
        }

        if (\count($toScore) > 0) {
            $this->info('✓ ' . \count($toScore) . ' partida(s) finalizada(s): palpites pontuados e leaderboard atualizado.');

            // Uma partida finalizada muda a classificação, então refrescamos aqui mesmo
            // (em vez de esperar o standings:refresh diário). Falha na API externa não
            // pode derrubar a importação/pontuação, então tratamos a exceção localmente.
            try {
                $this->standingsWriteService->refresh();
                $this->info('✓ Classificação atualizada.');
            } catch (\Exception $e) {
                $this->warn("  Não foi possível atualizar a classificação: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}

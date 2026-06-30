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
        'LAST_32'        => MatchStage::SECOND_ROUND,
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

        // Time sentinela usado quando a vaga ainda não tem seleção definida (TBD do mata-mata).
        $tbdTeam = $teamsByCode->get(Team::TBD_CODE);

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

            $stage  = self::STAGE_MAP[$match['stage']] ?? null;
            $status = self::STATUS_MAP[$match['status']] ?? MatchStatus::SCHEDULED;

            if (!$stage) {
                $skipped++;
                continue;
            }

            // Vagas ainda sem seleção definida (ex.: mata-mata antes da fase de grupos terminar)
            // apontam para o time sentinela TBD, então o jogo é importado mesmo assim.
            $homeTeam = ($homeTla ? ($teamsByTla->get($homeTla) ?? $teamsByCode->get($homeTla)) : null) ?? $tbdTeam;
            $awayTeam = ($awayTla ? ($teamsByTla->get($awayTla) ?? $teamsByCode->get($awayTla)) : null) ?? $tbdTeam;

            preg_match('/([A-L])$/', (string) ($match['group'] ?? ''), $m);
            $group = isset($m[1]) ? $groups->get($m[1]) : null;

            $scoreData = $this->resolveScoreData($match['score'] ?? [], $stage, $homeTeam, $awayTeam);

            $previousStatus = $previousStatusByExternal->get($match['id']);

            $matchModel = Matches::updateOrCreate(
                ['external_id' => $match['id']],
                [
                    'kickoff_at'     => $match['utcDate'],
                    'game_day'       => $match['matchday'],
                    'stage'          => $stage->value,
                    'group_id'       => $group?->id,
                    'status'         => $status->value,
                    'home_team_id'   => $homeTeam->id,
                    'away_team_id'   => $awayTeam->id,
                    'home_score'     => $scoreData['home_score'],
                    'away_score'     => $scoreData['away_score'],
                    'home_penalties' => $scoreData['home_penalties'],
                    'away_penalties' => $scoreData['away_penalties'],
                    'winner_team_id' => $scoreData['winner_team_id'],
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

    /**
     * Normaliza o bloco `score` da API em placar do confronto + desempate por pênaltis
     * + vencedor.
     *
     * Em jogos decididos nos pênaltis (`duration = PENALTY_SHOOTOUT`), o `fullTime` da
     * API agrega as cobranças convertidas — não é o placar do jogo. O placar real é o do
     * tempo normal somado à prorrogação (`regularTime + extraTime`), e as cobranças saem
     * da diferença entre `fullTime` e esse placar.
     *
     * @return array{home_score:?int, away_score:?int, home_penalties:?int, away_penalties:?int, winner_team_id:?int}
     */
    private function resolveScoreData(array $score, MatchStage $stage, Team $home, Team $away): array
    {
        $fullTime  = $score['fullTime'] ?? [];
        $homeScore = $fullTime['home'] ?? null;
        $awayScore = $fullTime['away'] ?? null;
        $homePenalties = null;
        $awayPenalties = null;

        if (($score['duration'] ?? null) === 'PENALTY_SHOOTOUT' && isset($score['regularTime'])) {
            $extra   = $score['extraTime'] ?? [];
            $regHome = ($score['regularTime']['home'] ?? 0) + ($extra['home'] ?? 0);
            $regAway = ($score['regularTime']['away'] ?? 0) + ($extra['away'] ?? 0);

            if ($homeScore !== null && $awayScore !== null) {
                $homePenalties = $homeScore - $regHome;
                $awayPenalties = $awayScore - $regAway;
            }

            $homeScore = $regHome;
            $awayScore = $regAway;
        }

        return [
            'home_score'     => $homeScore,
            'away_score'     => $awayScore,
            'home_penalties' => $homePenalties,
            'away_penalties' => $awayPenalties,
            'winner_team_id' => $this->resolveWinnerTeamId($score, $stage, $home, $away),
        ];
    }

    /**
     * Vencedor do confronto (apenas mata-mata). Usa o campo `winner` da API; quando ele
     * vem nulo — comum em jogos decididos nos pênaltis — cai para o `fullTime`, que agrega
     * as cobranças e portanto aponta o vencedor do desempate.
     */
    private function resolveWinnerTeamId(array $score, MatchStage $stage, Team $home, Team $away): ?int
    {
        if (!$stage->isKnockout()) {
            return null;
        }

        return match ($score['winner'] ?? null) {
            'HOME_TEAM' => $home->id,
            'AWAY_TEAM' => $away->id,
            default     => $this->winnerFromFullTime($score['fullTime'] ?? [], $home, $away),
        };
    }

    private function winnerFromFullTime(array $fullTime, Team $home, Team $away): ?int
    {
        $h = $fullTime['home'] ?? null;
        $a = $fullTime['away'] ?? null;

        if ($h === null || $a === null || $h === $a) {
            return null;
        }

        return $h > $a ? $home->id : $away->id;
    }
}

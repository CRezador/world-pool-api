<?php

namespace App\Services\MatchServices;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use App\Http\Requests\Match\MatchUpdateRequest;
use App\Models\Matches;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Repositories\TeamRepositories\TeamRepository;
use App\Services\GuessServices\GuessScoringService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MatchService
{
    private MatchRepository $matchRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        MatchRepository $matchRepository,
        TeamRepository $teamRepository,
        private GuessScoringService $guessScoringService,
    ) {
        $this->matchRepository = $matchRepository;
        $this->teamRepository = $teamRepository;
    }
    private function kickoffFormat(?string $kickoff_at): ?string
    {
        return $kickoff_at === null ? null : Carbon::createFromFormat('d/m/Y H:i', $kickoff_at)->format('Y-m-d H:i:s');
    }

    public function createMatch(array $match): Matches
    {


        if ($match['code_home_team'] === $match['code_away_team']) {
            throw new \Exception('O código do time da casa e do time visitante não podem ser iguais.', );
        }

        //Precisa informar a fase do campeonato com base no Enum MatchStage.
        $homeTeam = $this->teamRepository->findByCode((strtoupper($match['code_home_team'])));
        $awayTeam = $this->teamRepository->findByCode((strtoupper($match['code_away_team'])));

        $stageToUpper = strtoupper($match['stage']);
        if ($stageToUpper === MatchStage::GROUP_STAGE->value && $homeTeam->group_id !== $awayTeam->group_id) {
            throw new \Exception('Na fase de grupos, os times devem pertencer ao mesmo grupo.');
        }

        //Verificar se partida já existe, ou seja, se já existe uma partida com os mesmos times e na mesma fase do campeonato.
        if ($this->matchRepository->matchAlreadyExists($homeTeam->id, $awayTeam->id, $match['stage'])) {
            throw new \Exception(
                'Essa partida já existe.',
            );
        }

        try {
            $matchCreated = $this->matchRepository->create([
                'home_team_id' => $homeTeam->id,
                'game_day' => $match['game_day'],
                'away_team_id' => $awayTeam->id,
                'group_id' => $stageToUpper === MatchStage::GROUP_STAGE->value ? $homeTeam->group_id : null,
                'stage' => $stageToUpper,
                'status' => MatchStatus::SCHEDULED,
                'kickoff_at' => $this->kickoffFormat($match['kickoff_at']),
                'home_score' => 0,
                'away_score' => 0,
            ]);
        } catch (\Exception) {
            throw new \Exception('Erro ao criar a partida');
        }

        return $matchCreated;
    }

    public function updateMatch(MatchUpdateRequest $request, Matches $match): Matches
    {
        $data = [];

        if ($request->has('home_score')) {
            $data['home_score'] = $request->home_score;
        }

        if ($request->has('away_score')) {
            $data['away_score'] = $request->away_score;
        }

        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        if ($request->has('stage')) {
            $data['stage'] = $request->stage;
        }

        if ($request->has('kickoff_at')) {
            $data['kickoff_at'] = $this->kickoffFormat($request->kickoff_at);
        }

        $finishingMatch = $request->has('status')
            && $request->status === MatchStatus::FINISHED->value
            && $match->status !== MatchStatus::FINISHED;

        return DB::transaction(function () use ($match, $data, $finishingMatch) {
            $updated = $this->matchRepository->update($match, $data);

            if ($finishingMatch) {
                $this->guessScoringService->scoreGuessesForMatch($match->id);
            }

            return $updated;
        });
    }
}

<?php

namespace App\Services\MatchServices;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use App\Http\Requests\Match\MatchUpdateRequest;
use App\Models\Matches;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Repositories\TeamRepositories\TeamRepository;
use Carbon\Carbon;

class MatchService
{
    private MatchRepository $matchRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        MatchRepository $matchRepository,
        TeamRepository $teamRepository
    ) {
        $this->matchRepository = $matchRepository;
        $this->teamRepository = $teamRepository;
    }
    private function kickoffFormat($kickoff_at): string|null
    {
        return $kickoff_at === null ? null : Carbon::createFromFormat('d/m/Y', $kickoff_at)->format('Y-m-d H:i:s');
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
        if ($this->matchRepository->matchAlreadyExits($homeTeam->id, $awayTeam->id, $match['stage'])) {
            throw new \Exception(
                'Essa partida já existe.',
            );
        }

        try {
            $matchCreated = $this->matchRepository->create([
              'home_team_id' => $homeTeam->id,
              'game_day' => $match['game_day'],
              'away_team_id' => $awayTeam->id,
              'group_id' => $stageToUpper === null ? null : $homeTeam->group_id, // Considerando que ambos os times estão no mesmo grupo
              'stage' => $stageToUpper,
              'status' => MatchStatus::SCHEDULED,
              'kickoff_at' => $this->kickoffFormat($match['kickoff_at']),
              'home_score' => 0,
              'away_score' => 0
            ]);
        } catch (\Exception $e) {
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

        return $this->matchRepository->update($match, $data);
    }
}

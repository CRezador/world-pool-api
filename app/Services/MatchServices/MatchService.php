<?php

namespace App\Services\MatchServices;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use App\Http\Requests\Match\MatchRequest;
use App\Http\Requests\Match\MatchUpdateRequest;
use App\Models\Matches;
use App\Models\Team;
use App\Repositories\MatchRepositories\MatchRepository;
use Carbon\Carbon;

class MatchService
{
  private MatchRepository $matchRepository;

  public function __construct(MatchRepository $matchRepository)
  {
    $this->matchRepository = $matchRepository;
  }
  private function kickoffFormat($kickoff_at): string|null
  {
    return $kickoff_at === null ? null : Carbon::createFromFormat('d/m/Y', $kickoff_at)->format('Y-m-d H:i:s');
  }

  public function createMatch(MatchRequest $request): Matches
  {


    if ($request->code_home_team === $request->code_away_team) {
      throw new \Exception('O código do time da casa e do time visitante não podem ser iguais.',);
    }

    //Precisa informar a fase do campeonato com base no Enum MatchStage.
    $homeTeam = Team::query()->select(['id', 'group_id'])->where('code', strtoupper($request->code_home_team))->firstOrFail();
    $awayTeam = Team::query()->select(['id', 'group_id'])->where('code', strtoupper($request->code_away_team))->firstOrFail();

    $stageToUpper = strtoupper($request->stage);
    if ($stageToUpper === MatchStage::GROUP_STAGE->value && $homeTeam->group_id !== $awayTeam->group_id) {
      throw new \Exception('Na fase de grupos, os times devem pertencer ao mesmo grupo.');
    }

    //Verificar se partida já existe, ou seja, se já existe uma partida com os mesmos times e na mesma fase do campeonato.
    if ($this->matchRepository->matchAlreadyExits($homeTeam->id, $awayTeam->id, $request->stage)) {
      throw new \Exception(
        'Essa partida já existe.',
      );
    }

    try {
      $match = $this->matchRepository->create([
        'home_team_id' => $homeTeam->id,
        'game_day' => $request->game_day,
        'away_team_id' => $awayTeam->id,
        'group_id' => $stageToUpper === null ? null : $homeTeam->group_id, // Considerando que ambos os times estão no mesmo grupo
        'stage' => $stageToUpper,
        'status' => MatchStatus::SCHEDULED,
        'kickoff_at' => $this->kickoffFormat($request->kickoff_at),
        'home_score' => 0,
        'away_score' => 0
      ]);
    } catch (\Exception $e) {
      throw new \Exception('Erro ao criar a partida');
    }

    return $match;
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

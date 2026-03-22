<?php

namespace App\Repositories\MatchRepositories;

use App\Models\Matches;

class MatchRepository
{
  public function findAll()
  {
    return Matches::query()->get();
  }

  public function findById($id): ?Matches
  {
    return Matches::query()->find($id);
  }

  public function findByStage($stage)
  {
    return Matches::query()
      ->select([
        'matches.id',
        'matches.kickoff_at',
        'matches.stage',
        'matches.group_id',
        'matches.home_team_id',
        'matches.away_team_id',
        'matches.status',
        'matches.home_score',
        'matches.away_score'
      ])
      ->with([
        'homeTeam:id,name,code',
        'awayTeam:id,name,code',
        'group:id,name'
      ])
      ->where('stage', $stage)
      ->orderBy('kickoff_at')
      ->get();
  }

  public function findByGroup($groupId)
  {
    return Matches::query()
      ->select([
        'matches.id',
        'matches.kickoff_at',
        'matches.stage',
        'matches.group_id',
        'matches.home_team_id',
        'matches.away_team_id',
        'matches.status',
        'matches.home_score',
        'matches.away_score'
      ])
      ->where('group_id', $groupId)
      ->orderBy('kickoff_at')
      ->get();
  }

  public function create(array $data): Matches
  {
    return Matches::create($data);
  }

  public function matchAlreadyExits($homeId, $awayId, $stage): bool
  {
    return Matches::query()->where('home_team_id', $homeId)
      ->where('away_team_id', $awayId)
      ->where('stage', $stage)
      ->exists();
  }

  public function update(Matches $match, array $data): Matches
  {
    $match->update($data);
    return $match->refresh();
  }

  public function delete(Matches $match)
  {
    try {
      $match->delete();
    } catch (\Exception $e) {
      return throw new \Exception('Erro ao deletar a partida');
    }

    return true;
  }
}

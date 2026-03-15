<?php

namespace App\Http\Transformers\MatchTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;


class MatchTransformer extends BaseTransformer
{

  public static function transform($match): array
  {
    return [
      'id' => $match->id,
      'game_day' => $match->game_day,
      'home_team' => $match->homeTeam->name,
      'away_team' => $match->awayTeam->name,
      'stage' => $match->stage->name,
      'group' => $match->stage->name === 'GROUP_STAGE'
        ? $match->group->name
        : null,
      'status' => $match->status->name,
      'kickoff_at' => $match->kickoff_at === null ? null : $match->kickoff_at->format('d/m/Y'),
      'home_score' => $match->home_score,
      'away_score' => $match->away_score,
    ];
  }

  public function transformMatchByGroup($matches): array
  {
    return [
      'message' => 'Partidas encontradas para o grupo',
      'Group' => $matches->first()->group->name,
      'Matches' => $this->collection($matches)
    ];
  }
}

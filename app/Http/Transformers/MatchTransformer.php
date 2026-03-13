<?php

namespace App\Http\Transformers;

use App\Models\Matches;

class MatchTransformer
{

  public static function transform(Matches $match): array
  {
    return [
      'id' => $match->id,
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
}

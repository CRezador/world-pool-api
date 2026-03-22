<?php

namespace App\Http\Transformers\TeamTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class TeamTransformer extends BaseTransformer
{

  public static function transform($team): array
  {
    return [
      'name' => $team->name,
      'group' => $team->group->name,
      'code' => $team->code,
    ];
  }

  public function transformTeamsByGroup($team): array
  {
    return [
      'message' => 'Times encontrados',
      'Group' => $team->first()->group->name,
      'team' => $this->collection($team)
    ];
  }
}

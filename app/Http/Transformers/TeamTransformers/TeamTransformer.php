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
}

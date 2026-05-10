<?php

namespace App\Http\Transformers\TeamTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\Team;

class TeamTransformer extends BaseTransformer
{
    public function transform(Team $team): array
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
            'team' => $this->collection($team, 'Times do grupo'),
        ];
    }
}

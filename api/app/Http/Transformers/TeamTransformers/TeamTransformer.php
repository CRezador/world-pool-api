<?php

namespace App\Http\Transformers\TeamTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class TeamTransformer extends BaseTransformer
{
    public function transform(mixed $team): array
    {
        return [
            'id'        => $team->id,
            'name'      => $team->name,
            'code'      => $team->code,
            'flag_code' => $team->flag_code,
            'flag_url'  => $team->flag_code ? "https://flagcdn.com/{$team->flag_code}.svg" : null,
            'group'     => $team->group->name,
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

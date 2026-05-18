<?php

namespace App\Http\Transformers\GroupTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class GroupTransformer extends BaseTransformer
{
    public function transform(mixed $group): array
    {
        return [
            'id' => $group->id,
            'name' => $group->name,
            'teams' => $group->teams->map(fn($team) => [
                'id'       => $team->id,
                'name'     => $team->name,
                'code'     => $team->code,
                'flag_code' => $team->flag_code,
                'flag_url'  => $team->flag_code ? "https://flagcdn.com/{$team->flag_code}.svg" : null,
            ]),
        ];
    }
}

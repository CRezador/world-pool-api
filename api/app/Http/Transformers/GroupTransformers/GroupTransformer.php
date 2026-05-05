<?php

namespace App\Http\Transformers\GroupTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class GroupTransformer extends BaseTransformer
{
    public function transform($group): array
    {
        return [
            'name' => $group->name,
            'teams' => $group->teams->map(fn($team) => [
                'name' => $team->name,
                'code' => $team->code,
            ]),
        ];
    }
}

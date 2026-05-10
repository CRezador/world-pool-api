<?php

namespace App\Http\Transformers\GroupTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\Group;

class GroupTransformer extends BaseTransformer
{
    public function transform(Group $group): array
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

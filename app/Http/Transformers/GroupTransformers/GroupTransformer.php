<?php

declare(strict_types=1);

namespace App\Http\Transformers\GroupTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class GroupTransformer extends BaseTransformer
{
    public function transform($group): array
    {
        return [
            'name' => $group->name,
            'teams' => $group->teams->map(static fn($team) => [
                'name' => $team->name,
                'code' => $team->code,
            ]),
        ];
    }
}

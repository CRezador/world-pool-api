<?php

namespace App\Http\Transformers\PoolTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class PoolTransformer extends BaseTransformer
{
    public function transform(mixed $pool): array
    {
        $leader = $pool->leader ?? null;

        $data = [
            'id'            => $pool->id,
            'name'          => $pool->name,
            'join_code'     => $pool->join_code,
            'is_public'     => (bool) $pool->is_public,
            'owner'         => $pool->owner->name,
            'members_count' => $pool->members_count ?? null,
            'leader'        => $leader ? [
                'id'     => $leader->user->id,
                'name'   => $leader->user->name,
                'points' => $leader->points,
            ] : null,
        ];

        $data['last_results'] = $pool->last_results ?? [];

        if (isset($pool->my_points)) {
            $data['my_points'] = $pool->my_points;
            $data['my_rank']   = $pool->my_rank;
        }

        return $data;
    }

}

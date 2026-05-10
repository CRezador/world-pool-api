<?php

namespace App\Http\Transformers\PoolTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
class PoolTransformer extends BaseTransformer
{
    public function transform(mixed $pool): array
    {
        return [
            'id' => $pool->id,
            'name' => $pool->name,
            'join_code' => $pool->join_code,
            'is_public' => (bool) $pool->is_public,
            'owner' => $pool->owner->name,
        ];
    }

}

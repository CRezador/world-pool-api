<?php

namespace App\Http\Transformers\PoolTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\Pool;

class PoolTransformer extends BaseTransformer
{
    public function transform(Pool $pool): array
    {
        return [
            'name' => $pool->name,
            'join_code' => $pool->join_code,
            'is_public' => $pool->is_public === 1 ? true : false,
            'owner' => $pool->owner->name,
        ];
    }

}

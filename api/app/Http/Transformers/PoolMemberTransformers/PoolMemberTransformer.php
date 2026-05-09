<?php

namespace App\Http\Transformers\PoolMemberTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class PoolMemberTransformer extends BaseTransformer
{
    public function transform($poolMember): array
    {
        return [
            'user_id' => $poolMember->user_id,
            'user_name' => $poolMember->user->name,
            'role' => $poolMember->role,
            'status' => $poolMember->status,
            'joined_at' => $poolMember->joined_at->toDateTimeString(),
        ];
    }
}

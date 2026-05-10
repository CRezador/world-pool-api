<?php

namespace App\Http\Transformers\PoolMemberTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\PoolMember;

class PoolMemberTransformer extends BaseTransformer
{
    public function transform(PoolMember $poolMember): array
    {
        return [
            'id' => $poolMember->id,
            'user_id' => $poolMember->user_id,
            'user_name' => $poolMember->user->name,
            'role' => $poolMember->role,
            'status' => $poolMember->status,
            'joined_at' => $poolMember->joined_at->toDateTimeString(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Transformers\PoolMemberTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class PoolMemberTransformer extends BaseTransformer
{
    public function transform($poolMember): array
    {
        return [
            'id' => $poolMember->id,
            'user_id' => $poolMember->user_id,
            'user_name' => $poolMember->user->name, // Acessa o nome do usuário relacionado
            'role' => $poolMember->role,
            'joined_at' => $poolMember->created_at->toDateTimeString(),
        ];
    }
}

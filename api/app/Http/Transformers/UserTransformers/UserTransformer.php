<?php

namespace App\Http\Transformers\UserTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\User;

class UserTransformer extends BaseTransformer
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}

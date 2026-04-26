<?php

namespace App\Http\Transformers\UserTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class UserTransformer extends BaseTransformer
{
    public function transform($user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}

<?php

namespace App\Http\Transformers\TokenTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class TokenTransformer extends BaseTransformer
{
    public function transform(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}

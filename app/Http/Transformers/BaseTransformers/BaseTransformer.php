<?php

declare(strict_types=1);

namespace App\Http\Transformers\BaseTransformers;

abstract class BaseTransformer
{
    public function item($data, $message): array
    {
        return [
            'message' => $message,
            'data' => $this->transform($data),
        ];
    }

    public function collection($items): array
    {
        return [
            'data' => $items->map(fn($item) => $this->transform($item))->values()->toArray(),
        ];
    }

    abstract public function transform($item): array;
}

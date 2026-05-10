<?php

namespace App\Http\Transformers\BaseTransformers;

use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseTransformer
{
    public function item($data, $message): array
    {
        return [
            'message' => $message,
            'data' => $this->transform($data),
        ];
    }

    public function collection($items, $message): array
    {
        return [
            'message' => $message,
            'data' => $items->map(fn($item) => $this->transform($item))->values()->toArray(),
        ];
    }

    public function paginated(LengthAwarePaginator $paginator, string $message): array
    {
        return [
            'message' => $message,
            'data' => $paginator->getCollection()->map(fn($item) => $this->transform($item))->values()->toArray(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ];
    }

    abstract public function transform($item): array;
}

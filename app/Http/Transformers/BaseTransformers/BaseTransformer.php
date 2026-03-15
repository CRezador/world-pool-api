<?php

namespace App\Http\Transformers\BaseTransformers;


abstract class BaseTransformer
{
  public function item($data): array
  {
    return [
      'data' => $this->transform($data)
    ];
  }

  public function collection($items): array
  {
    return [
      'data' => $items->map(fn($item) => $this->transform($item))->values()->toArray()
    ];
  }

  abstract public static function transform($item): array;
}

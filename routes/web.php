<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

Route::get('/', static fn() => response()->json([
    'message' => "404 Not Found",
    'Status-code' => '404',
]));

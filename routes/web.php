<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

Route::get('/', function () {
    return response()->json([
        'message' => "404 Not Found",
        'Status-code' => '404'
    ]);
});

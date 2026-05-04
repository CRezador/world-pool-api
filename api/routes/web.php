<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => response()->json([
    'message' => '404 Not Found',
    'Status-code' => '404',
]));

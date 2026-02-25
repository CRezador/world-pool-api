<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Users Routes
Route::post('/login', [TokenController::class, 'store']);
Route::post('/register', [UserController::class, 'store']);

//Authenticate Routes
Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'me']);
    }
);

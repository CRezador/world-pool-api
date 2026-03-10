<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\GroupController;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Raw;

//Users Routes
Route::post('/login', [TokenController::class, 'store']);
Route::post('/register', [UserController::class, 'store']);

//Authenticate Routes
Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/teams', [TeamsController::class, 'index']);
        Route::get('/groups', [GroupController::class, 'index']);
    }
);

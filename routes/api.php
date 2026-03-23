<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PoolController;
use Illuminate\Support\Facades\Route;

//Users Routes
Route::post('/login', [TokenController::class, 'store']);
Route::post('/register', [UserController::class, 'store']);

//Authenticate Routes
Route::middleware('auth:sanctum')->group(
    function () {
        //Rotas de usuário
        Route::get('/me', [UserController::class, 'me']);

        //Rotas de times
        Route::get('/teams', [TeamController::class, 'index']);
        Route::get('/teams/{id}', [TeamController::class, 'show']);

        //Rotas de grupos
        Route::get('/groups', [GroupController::class, 'index']);
        Route::get('/groups/{id}', [GroupController::class, 'show']);

        //Rotas de partidas
        Route::get('/matches/{id}', [MatchController::class, 'show']);
        Route::get('/matches', [MatchController::class, 'index']);
        Route::get('/group/{id}/matches', [MatchController::class, 'matchByGroup']);
        Route::get('/stages/matches', [MatchController::class, 'matchesByStage']);

        //Rotas de Bolão
        Route::post('/pools', [PoolController::class, 'store']);
        Route::get('/pools', [PoolController::class, 'index']);
        Route::get('/pools/{id}', [PoolController::class, 'show']);
        //Rotas admin
        Route::middleware('admin')->group(
            function () {
                //Rotas de partidas
                Route::post('/matches/create', [MatchController::class, 'store']);
                Route::put('/matches/{id}', [MatchController::class, 'update']);
                Route::delete('/matches/{id}', [MatchController::class, 'destroy']);
            }
        );
    }
);

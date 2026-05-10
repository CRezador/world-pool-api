<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\GuessController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\PoolMemberController;
use Illuminate\Support\Facades\Route;

//Users Routes
Route::post('/login', [TokenController::class, 'store']);
Route::post('/register', [UserController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/logout', [TokenController::class, 'destroy']);

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
        Route::get('/me/pools', [PoolController::class, 'myPools']);
        Route::get('/pools/{id}', [PoolController::class, 'show']);
        Route::delete('/pools/{id}', [PoolController::class, 'destroy']);
        Route::post('/pools/join', [PoolController::class, 'join']);
        Route::put('/pools/{id}', [PoolController::class, 'update'])->middleware('PoolOwner');

        //Rota PoolMember
        Route::get('/pools/{poolId}/members', [PoolMemberController::class, 'index'])->middleware('PoolMember');
        Route::get('/pools/{poolId}/members/me', [PoolMemberController::class, 'me'])->middleware('PoolMember');
        Route::get('/pools/{poolId}/members/{memberId}', [PoolMemberController::class, 'show'])->middleware('PoolMember');
        Route::patch('/pools/{poolId}/members/{memberId}/role', [PoolMemberController::class, 'updateRole'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{poolId}/leave', [PoolMemberController::class, 'leave'])->middleware('PoolMember');
        Route::post('/pools/{poolId}/regenerate-code', [PoolController::class, 'regenerateJoinCode'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{poolId}/members/{memberId}/ban', [PoolMemberController::class, 'ban'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{poolId}/members/{memberId}/unban', [PoolMemberController::class, 'unban'])->middleware('PoolMemberAdmin');

        //Rotas de Palpites
        Route::middleware('PoolMember')->group(function () {
            Route::get('/pools/{poolId}/guesses', [GuessController::class, 'index']);
            Route::post('/pools/{poolId}/guesses', [GuessController::class, 'store']);
            Route::put('/pools/{poolId}/guesses/{guessId}', [GuessController::class, 'update']);
            Route::delete('/pools/{poolId}/guesses/{guessId}', [GuessController::class, 'destroy']);
            Route::get('/pools/{poolId}/matches/{matchId}/guesses', [GuessController::class, 'matchGuesses']);
            Route::get('/pools/{poolId}/members/{memberId}/guesses', [GuessController::class, 'memberGuesses']);
        });

        //Rotas de Leaderboard
        Route::middleware('PoolMember')->group(function () {
            Route::get('/pools/{poolId}/leaderboard', [LeaderboardController::class, 'index']);
            Route::get('/pools/{poolId}/leaderboard/top', [LeaderboardController::class, 'top']);
            Route::get('/pools/{poolId}/leaderboard/me', [LeaderboardController::class, 'myPosition']);
            Route::get('/pools/{poolId}/leaderboard/{userId}', [LeaderboardController::class, 'show']);
        });

        //Rotas admin
        Route::middleware('admin')->group(
            function () {
                //Rotas de usuário
                Route::patch('/users/{id}', [UserController::class, 'update']);
                Route::patch('/users/{id}/role', [UserController::class, 'updateRole']);

                //Rotas de partidas
                Route::post('/matches/create', [MatchController::class, 'store']);
                Route::put('/matches/{id}', [MatchController::class, 'update']);
                Route::delete('/matches/{id}', [MatchController::class, 'destroy']);

                //Rotas internas de leaderboard
                Route::post('/pools/{poolId}/leaderboard/recalculate', [LeaderboardController::class, 'recalculate']);
            }
        );
    }
);

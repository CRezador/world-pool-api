<?php

declare(strict_types=1);

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PoolController;
use App\Http\Controllers\PoolMemberController;
use Illuminate\Support\Facades\Route;

//Users Routes
Route::post('/login', [TokenController::class, 'store']);
Route::post('/register', [UserController::class, 'store']);

//Authenticate Routes
Route::middleware('auth:sanctum')->group(
    static function (): void {
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
        Route::delete('/pools/{id}', [PoolController::class, 'destroy']);
        Route::post('/pools/join', [PoolController::class, 'join']);
        Route::put('/pools/{id}', [PoolController::class, 'update']);

        //Rota PoolMember
        Route::get('/pools/{poolId}/members', [PoolMemberController::class, 'index'])->middleware('PoolMember');
        Route::get('/me/pools', [PoolMemberController::class, 'myPools']);
        Route::get('/pools/{poolId}/members/{memberId}', [PoolMemberController::class, 'show']);
        Route::patch('/pools/{pool}/members/{member}/role', [PoolMemberController::class, 'updateRole'])->middleware('PoolMemberAdmin');
        Route::patch('/pools/{pool}/members/{member}/status', [PoolMemberController::class, 'updateStatus']);
        Route::delete('/pools/{pool}/members/{member}', [PoolMemberController::class, 'destroy'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{id}/regenerate-code', [PoolController::class, 'regenerateJoinCode'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{pool}/members/join', [PoolMemberController::class, 'joinPool']);
        Route::post('/pools/{pool}/members/{member}/ban', [PoolMemberController::class, 'ban'])->middleware('PoolMemberAdmin');
        Route::post('/pools/{pool}/members/{member}/unban', [PoolMemberController::class, 'unban'])->middleware('PoolMemberAdmin');

        //Rotas admin
        Route::middleware('admin')->group(
            static function (): void {
                //Rotas de partidas
                Route::post('/matches/create', [MatchController::class, 'store']);
                Route::put('/matches/{id}', [MatchController::class, 'update']);
                Route::delete('/matches/{id}', [MatchController::class, 'destroy']);
            }
        );
    }
);

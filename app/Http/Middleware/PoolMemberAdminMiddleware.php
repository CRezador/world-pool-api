<?php

namespace App\Http\Middleware;

use App\Services\PoolMemberServices\PoolMemberService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = new PoolMemberService(
            new \App\Repositories\PoolMemberRepositories\PoolMemberRepository(),
            new \App\Repositories\PoolRepositories\PoolRepository()
        );

        if (!$request->user() || !$isAdmin->isAdmin($request->route('poolId'), $request->user()->id)) {
            return response()->json([
                'message' => 'Acesso negado',
            ], 403);
        }
        return $next($request);
    }
}

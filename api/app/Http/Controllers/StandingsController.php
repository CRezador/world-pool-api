<?php

namespace App\Http\Controllers;

use App\Http\Transformers\StandingsTransformers\StandingsTransformer;
use App\Services\StandingsServices\StandingsReadService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StandingsController extends Controller
{
    public function __construct(
        private StandingsTransformer $standingsTransformer,
        private StandingsReadService $standingsReadService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $standings = $this->standingsReadService->getStandings();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(
            $this->standingsTransformer->collection($standings, 'Classificação da Copa do Mundo 2026'),
            200
        );
    }
}

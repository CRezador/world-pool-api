<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guess\StoreGuessRequest;
use App\Http\Requests\Guess\UpdateGuessRequest;
use App\Http\Transformers\GuessTransformers\AdversaryGuessTransformer;
use App\Http\Transformers\GuessTransformers\GuessTransformer;
use App\Services\GuessServices\GuessReadService;
use App\Services\GuessServices\GuessWriteService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuessController extends Controller
{
    public function __construct(
        private GuessWriteService $guessWriteService,
        private GuessReadService $guessReadService,
        private GuessTransformer $guessTransformer,
        private AdversaryGuessTransformer $adversaryGuessTransformer,
    ) {}

    public function index(Request $request): Response
    {
        $guesses = $this->guessReadService->getMyGuesses($request->user()->id);

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites listados com sucesso'),
            200
        );
    }

    public function store(StoreGuessRequest $request): Response
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        try {
            $existing = $this->guessReadService->getGuessForMatch($userId, $data['match_id']);

            if ($existing) {
                $guess = $this->guessWriteService->updateGuess($existing->id, $userId, [
                    'home_score'     => $data['home_score'],
                    'away_score'     => $data['away_score'],
                    'winner_team_id' => $data['winner_team_id'] ?? null,
                ]);
                $guess->load(['match.homeTeam', 'match.awayTeam']);
                return response()->json(
                    $this->guessTransformer->item($guess, 'Palpite atualizado com sucesso'),
                    200
                );
            }

            $guess = $this->guessWriteService->createGuess([
                'user_id'        => $userId,
                'match_id'       => $data['match_id'],
                'home_score'     => $data['home_score'],
                'away_score'     => $data['away_score'],
                'winner_team_id' => $data['winner_team_id'] ?? null,
            ]);
            $guess->load(['match.homeTeam', 'match.awayTeam']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }

        return response()->json(
            $this->guessTransformer->item($guess, 'Palpite criado com sucesso'),
            201
        );
    }

    public function update(UpdateGuessRequest $request, int $guessId): Response
    {
        $data = $request->validated();

        try {
            $guess = $this->guessWriteService->updateGuess($guessId, $request->user()->id, [
                'home_score'     => $data['home_score'],
                'away_score'     => $data['away_score'],
                'winner_team_id' => $data['winner_team_id'] ?? null,
            ]);
            $guess->load(['match.homeTeam', 'match.awayTeam']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(
            $this->guessTransformer->item($guess, 'Palpite atualizado com sucesso'),
            200
        );
    }

    public function destroy(Request $request, int $guessId): Response
    {
        try {
            $this->guessWriteService->deleteGuess($guessId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Palpite removido com sucesso'], 200);
    }

    public function matchGuesses(int $poolId, int $matchId): Response
    {
        try {
            $guesses = $this->guessReadService->getMatchGuesses($matchId, $poolId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites da partida listados com sucesso'),
            200
        );
    }

    public function adversaryGuesses(Request $request, int $matchId): Response
    {
        try {
            $guesses = $this->guessReadService->getAdversaryGuessesForMatch($request->user()->id, $matchId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(
            $this->adversaryGuessTransformer->collection($guesses, 'Palpites dos adversários listados com sucesso'),
            200
        );
    }

    public function memberGuesses(int $poolId, int $memberId): Response
    {
        try {
            $guesses = $this->guessReadService->getMemberGuesses($memberId, $poolId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites do membro listados com sucesso'),
            200
        );
    }
}

<?php

namespace App\Services\ActivityServices;

use App\Http\Enums\GuessPoints;
use App\Http\Enums\MatchStatus;
use App\Models\PoolMember;
use App\Repositories\GuessRepositories\GuessRepository;

class ActivityReadService
{
    public function __construct(
        private GuessRepository $guessRepository,
    ) {}

    public function getRecentActivity(int $userId, int $limit = 20): array
    {
        $poolIds = PoolMember::where('user_id', $userId)
            ->pluck('pool_id')
            ->toArray();

        if (empty($poolIds)) {
            return [];
        }

        return $this->guessRepository
            ->recentActivityForPools($poolIds, $limit)
            ->map(fn($guess) => $this->mapGuess($guess, $userId))
            ->values()
            ->toArray();
    }

    private function mapGuess(mixed $guess, int $userId): array
    {
        $match = $guess->match;
        $isPending = $match->status !== MatchStatus::FINISHED;

        if ($isPending) {
            $action = 'palpitou';
            $subject = $match->homeTeam->code . ' ' . $guess->home_score . '×' . $guess->away_score . ' ' . $match->awayTeam->code;
            $points = null;
        } else {
            $points = $guess->points ?? 0;

            if ($points >= GuessPoints::EXACT->value) {
                $action = 'cravou';
                $subject = $match->homeTeam->code . ' ' . $match->home_score . '×' . $match->away_score . ' ' . $match->awayTeam->code;
            } elseif ($points >= GuessPoints::RESULT->value) {
                $action = 'acertou';
                $subject = 'vencedor';
            } else {
                $action = 'errou';
                $subject = $match->homeTeam->code . ' ' . $guess->home_score . '×' . $guess->away_score . ' ' . $match->awayTeam->code;
            }
        }

        return [
            'id'         => $guess->id,
            'pool_id'    => 0,
            'pool_name'  => '',
            'created_at' => $guess->created_at->toIso8601String(),
            'actor'      => $guess->user->name,
            'is_me'      => $guess->user_id === $userId,
            'action'     => $action,
            'subject'    => $subject,
            'points'     => $points,
        ];
    }
}

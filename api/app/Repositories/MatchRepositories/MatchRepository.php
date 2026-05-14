<?php

namespace App\Repositories\MatchRepositories;

use App\Http\Enums\MatchStatus;
use App\Models\Matches;
use Illuminate\Database\Eloquent\Collection;

class MatchRepository
{
    public function findAll(): Collection
    {
        return Matches::with(['homeTeam', 'awayTeam', 'group'])->get();
    }

    public function findById(int $id): ?Matches
    {
        return Matches::with(['homeTeam', 'awayTeam', 'group'])->find($id);
    }

    public function findByStage(string $stage): Collection
    {
        return Matches::query()
          ->select([
              'matches.id',
              'matches.kickoff_at',
              'matches.stage',
              'matches.group_id',
              'matches.home_team_id',
              'matches.away_team_id',
              'matches.status',
              'matches.home_score',
              'matches.away_score',
          ])
          ->with([
              'homeTeam:id,name,code',
              'awayTeam:id,name,code',
              'group:id,name',
          ])
          ->where('stage', $stage)
          ->orderBy('kickoff_at')
          ->get();
    }

    public function findByGroup(int $groupId): Collection
    {
        return Matches::query()
          ->select([
              'matches.id',
              'matches.kickoff_at',
              'matches.stage',
              'matches.group_id',
              'matches.home_team_id',
              'matches.away_team_id',
              'matches.status',
              'matches.home_score',
              'matches.away_score',
          ])
          ->with(['homeTeam', 'awayTeam'])
          ->where('group_id', $groupId)
          ->orderBy('kickoff_at')
          ->get();
    }

    public function create(array $data): Matches
    {
        return Matches::create($data);
    }

    public function matchAlreadyExists(int $homeId, int $awayId, string $stage): bool
    {
        return Matches::query()->where('home_team_id', $homeId)
          ->where('away_team_id', $awayId)
          ->where('stage', $stage)
          ->exists();
    }

    public function update(Matches $match, array $data): Matches
    {
        $match->update($data);

        return $match->fresh(['homeTeam', 'awayTeam', 'group']);
    }

    public function delete(Matches $match): bool
    {
        try {
            $match->delete();
        } catch (\Exception) {
            throw new \Exception('Erro ao deletar a partida');
        }

        return true;
    }

    public function getStatusById(int $id): ?MatchStatus
    {
        $match = $this->findById($id);
        return $match ? $match->status : null;
    }

    public function assertScheduled(int $matchId): void
    {
        if ($this->getStatusById($matchId) !== MatchStatus::SCHEDULED) {
            throw new \Exception('Não é possível realizar esta ação em uma partida que não está agendada.', 400);
        }
    }

    public function assertFinished(int $matchId): void
    {
        if ($this->getStatusById($matchId) !== MatchStatus::FINISHED) {
            throw new \Exception('A partida ainda não foi finalizada.', 400);
        }
    }
}

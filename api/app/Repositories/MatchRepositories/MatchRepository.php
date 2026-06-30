<?php

namespace App\Repositories\MatchRepositories;

use App\Http\Enums\MatchStatus;
use App\Models\Matches;
use App\Models\Team;
use Carbon\Carbon;
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
              'matches.home_penalties',
              'matches.away_penalties',
              'matches.winner_team_id',
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
              'matches.game_day',
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

    public function findNextGameDayMatches(): Collection
    {
        $nextGameDay = Matches::query()
            ->where('status', '!=', MatchStatus::FINISHED->value)
            ->min('game_day');

        if ($nextGameDay === null) {
            return collect();
        }

        return Matches::query()
            ->select([
                'matches.id',
                'matches.game_day',
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
                'homeTeam:id,name,code,flag_code,group_id',
                'homeTeam.group:id,name',
                'awayTeam:id,name,code,flag_code,group_id',
                'awayTeam.group:id,name',
                'group:id,name',
            ])
            ->where('game_day', $nextGameDay)
            ->where('status', MatchStatus::SCHEDULED->value)
            ->orderBy('kickoff_at')
            ->get();
    }

    public function findTodayMatches(): Collection
    {
        $timezone = config('app.display_timezone');
        $start = Carbon::today($timezone)->startOfDay()->utc();
        $end = Carbon::today($timezone)->endOfDay()->utc();

        return Matches::query()
            ->select([
                'matches.id',
                'matches.game_day',
                'matches.kickoff_at',
                'matches.stage',
                'matches.group_id',
                'matches.home_team_id',
                'matches.away_team_id',
                'matches.status',
                'matches.home_score',
                'matches.away_score',
                'matches.home_penalties',
                'matches.away_penalties',
                'matches.winner_team_id',
            ])
            ->with([
                'homeTeam:id,name,code,flag_code,group_id',
                'homeTeam.group:id,name',
                'awayTeam:id,name,code,flag_code,group_id',
                'awayTeam.group:id,name',
                'group:id,name',
            ])
            ->whereBetween('kickoff_at', [$start, $end])
            ->orderBy('kickoff_at')
            ->get();
    }

    public function getStatusById(int $id): ?MatchStatus
    {
        $match = $this->findById($id);
        return $match ? $match->status : null;
    }

    public function assertScheduled(int $matchId): void
    {
        $match = $this->findById($matchId);

        if (!$match || $match->status !== MatchStatus::SCHEDULED) {
            throw new \Exception('Esta partida não está mais aberta para palpites.', 400);
        }

        if ($match->kickoff_at !== null && now()->gte($match->kickoff_at)) {
            throw new \Exception('Os palpites para esta partida já foram encerrados.', 400);
        }
    }

    public function assertTeamsDefined(int $matchId): void
    {
        $match = $this->findById($matchId);

        if (!$match || $match->homeTeam?->code === Team::TBD_CODE || $match->awayTeam?->code === Team::TBD_CODE) {
            throw new \Exception('As seleções desta partida ainda não foram definidas.', 400);
        }
    }

    public function assertFinished(int $matchId): void
    {
        if ($this->getStatusById($matchId) !== MatchStatus::FINISHED) {
            throw new \Exception('A partida ainda não foi finalizada.', 400);
        }
    }
}

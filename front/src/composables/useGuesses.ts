import api from '@/services/api';
import type { GuessEntry } from '@/types';
import { ref } from 'vue';

const guesses = ref<GuessEntry[]>([]);

function mapGuess(g: any): GuessEntry {
    return {
        id:           g.id,
        matchId:      g.match_id,
        homeScore:    g.home_score,
        awayScore:    g.away_score,
        winnerTeamId: g.winner_team_id ?? null,
        points:       g.points ?? null,
        match: {
            id:            g.match.id,
            stage:         g.match.stage,
            group:         g.match.group ?? null,
            status:        g.match.status,
            kickoffAt:     g.match.kickoff_at ?? null,
            homeScore:     g.match.home_score ?? null,
            awayScore:     g.match.away_score ?? null,
            homePenalties: g.match.home_penalties ?? null,
            awayPenalties: g.match.away_penalties ?? null,
            winnerTeamId:  g.match.winner_team_id ?? null,
            homeTeam:  { id: g.match.home_team.id, code: g.match.home_team.code, flagUrl: g.match.home_team.flag_url ?? null },
            awayTeam:  { id: g.match.away_team.id, code: g.match.away_team.code, flagUrl: g.match.away_team.flag_url ?? null },
        },
    };
}

export function useGuesses() {
    const fetchMyGuesses = async () => {
        const res = await api.get('guesses');
        const raw: any[] = res.data.data ?? res.data;
        guesses.value = raw.map(mapGuess);
    };

    const createGuess = async (matchId: number, homeScore: number, awayScore: number, winnerTeamId: number | null = null): Promise<GuessEntry> => {
        const res = await api.post('guesses', {
            match_id:       matchId,
            home_score:     homeScore,
            away_score:     awayScore,
            winner_team_id: winnerTeamId,
        });
        const created = mapGuess(res.data.data ?? res.data);
        guesses.value = [...guesses.value, created];
        return created;
    };

    const updateGuess = async (guessId: number, homeScore: number, awayScore: number, winnerTeamId: number | null = null): Promise<void> => {
        await api.put(`guesses/${guessId}`, {
            home_score:     homeScore,
            away_score:     awayScore,
            winner_team_id: winnerTeamId,
        });
        const idx = guesses.value.findIndex(g => g.id === guessId);
        if (idx !== -1) {
            guesses.value[idx] = { ...guesses.value[idx], homeScore, awayScore, winnerTeamId };
        }
    };

    return { guesses, fetchMyGuesses, createGuess, updateGuess };
}

import api from '@/services/api';
import type { GuessEntry } from '@/types';
import { ref } from 'vue';

const guesses = ref<GuessEntry[]>([]);

function mapGuess(g: any): GuessEntry {
    return {
        id:         g.id,
        matchId:    g.match_id,
        homeScore:  g.home_score,
        awayScore:  g.away_score,
        points:     g.points ?? null,
        match: {
            id:        g.match.id,
            stage:     g.match.stage,
            group:     g.match.group ?? null,
            status:    g.match.status,
            kickoffAt: g.match.kickoff_at ?? null,
            homeScore: g.match.home_score ?? null,
            awayScore: g.match.away_score ?? null,
            homeTeam:  { code: g.match.home_team.code, flagUrl: g.match.home_team.flag_url ?? null },
            awayTeam:  { code: g.match.away_team.code, flagUrl: g.match.away_team.flag_url ?? null },
        },
    };
}

export function useGuesses() {
    const fetchMyGuesses = async (poolId: string) => {
        const res = await api.get(`pools/${poolId}/guesses`);
        const raw: any[] = res.data.data ?? res.data;
        guesses.value = raw.map(mapGuess);
    };

    return { guesses, fetchMyGuesses };
}

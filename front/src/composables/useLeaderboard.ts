import api from "@/services/api";
import type { LeaderboardEntry } from "@/types";
import { ref } from "vue";
import { useAuth } from "./useAuth";

export interface MyPoolEntry {
    rank: number;
    points: number;
    exactHits: number;
    resultHits: number;
    guessesCount: number;
    lastResults: number[];
}

export interface MyStats {
    poolsCount: number;
    totalPoints: number;
    totalExactHits: number;
    totalResultHits: number;
    totalGuesses: number;
    bestRank: number | null;
}

const myStats = ref<MyStats | null>(null);

const leaderboard = ref<LeaderboardEntry[]>([]);
const myEntry = ref<LeaderboardEntry | null>(null);
const myPoolEntry = ref<MyPoolEntry | null>(null);

function mapEntry(e: any, userId?: number): LeaderboardEntry {
    return {
        rank: e.rank,
        userId: e.user.id,
        name: e.user.name,
        points: e.points,
        exactHits: e.exact_hits,
        resultHits: e.result_hits,
        guessesCount: e.guesses_count,
        isMe: e.user.id === userId,
    };
}

export function useLeaderboard() {
    const fetchLeaderboard = async (poolId: string | number, limit = 10) => {
        const { user } = useAuth();
        const uid = user.value?.id;

        const [topRes, meRes] = await Promise.allSettled([
            api.get(`pools/${poolId}/leaderboard/top`, { params: { limit } }),
            api.get(`pools/${poolId}/leaderboard/me`),
        ]);

        const rawTop = topRes.status === 'fulfilled'
            ? (topRes.value.data.data ?? topRes.value.data)
            : [];

        leaderboard.value = rawTop.map((e: any) => mapEntry(e, uid));

        if (meRes.status === 'fulfilled') {
            const raw = meRes.value.data.data ?? meRes.value.data;
            myEntry.value = mapEntry(raw, uid);
            myPoolEntry.value = {
                rank:         raw.rank,
                points:       raw.points,
                exactHits:    raw.exact_hits,
                resultHits:   raw.result_hits,
                guessesCount: raw.guesses_count,
                lastResults:  raw.last_results ?? [],
            };

            const inTop = leaderboard.value.find(e => e.userId === uid);
            if (inTop) inTop.isMe = true;
        } else {
            myEntry.value = null;
            myPoolEntry.value = null;
        }
    };

    const fetchMyStats = async () => {
        const response = await api.get('me/stats');
        const raw = response.data.data ?? response.data;
        myStats.value = {
            poolsCount:      raw.pools_count,
            totalPoints:     raw.total_points,
            totalExactHits:  raw.total_exact_hits,
            totalResultHits: raw.total_result_hits,
            totalGuesses:    raw.total_guesses,
            bestRank:        raw.best_rank,
        };
    };

    return { leaderboard, myEntry, myPoolEntry, fetchLeaderboard, myStats, fetchMyStats };
}

import api from "@/services/api";
import type { LeaderboardEntry } from "@/types";
import { ref } from "vue";
import { useAuth } from "./useAuth";

const leaderboard = ref<LeaderboardEntry[]>([]);
const myEntry = ref<LeaderboardEntry | null>(null);

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

            // ensure isMe is flagged in the top list too
            const inTop = leaderboard.value.find(e => e.userId === uid);
            if (inTop) inTop.isMe = true;
        } else {
            myEntry.value = null;
        }
    };

    return { leaderboard, myEntry, fetchLeaderboard };
}

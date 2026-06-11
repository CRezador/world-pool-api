import api from "@/services/api";
import type { ApiMatch } from "@/types";
import { ref } from "vue";

const upcomingMatches = ref<ApiMatch[]>([]);

export function useMatch() {
    const fetchUpcomingMatches = async () => {
        const response = await api.get('matches/upcoming');
        const raw = response.data.data ?? response.data;
        upcomingMatches.value = raw.map((m: any) => ({
            id: m.id,
            gameDay: m.game_day,
            kickoff: m.kickoff_at ?? null,
            stage: m.stage,
            group: m.group ?? null,
            groupId: m.group_id ?? null,
            status: m.status,
            home: {
                id: m.home_team.id,
                name: m.home_team.name,
                code: m.home_team.code,
                flag_code: m.home_team.flag_code,
                flag_url: m.home_team.flag_url,
                group: m.home_team.group,
            },
            away: {
                id: m.away_team.id,
                name: m.away_team.name,
                code: m.away_team.code,
                flag_code: m.away_team.flag_code,
                flag_url: m.away_team.flag_url,
                group: m.away_team.group,
            },
            homeScore: m.home_score,
            awayScore: m.away_score,
        } satisfies ApiMatch));
    };

    return { upcomingMatches, fetchUpcomingMatches };
}

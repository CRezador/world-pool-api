import api from "@/services/api";
import type { GroupStanding } from "@/types";
import { ref } from "vue";

const standings = ref<GroupStanding[]>([]);

export function useStandings() {
    const fetchStandings = async () => {
        const response = await api.get('standings');
        const raw = response.data.data ?? response.data;
        standings.value = raw.map((g: any): GroupStanding => ({
            group: g.group,
            table: g.table.map((r: any) => ({
                position: r.position,
                team: r.team,
                code: r.code,
                crest: r.crest,
                played: r.played,
                won: r.won,
                draw: r.draw,
                lost: r.lost,
                points: r.points,
            })),
        }));
    };

    return { standings, fetchStandings };
}

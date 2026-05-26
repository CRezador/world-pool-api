import api from "@/services/api";
import type { Team } from "@/types";
import { ref } from "vue";

const teams = ref<Team[]>([]);

export function useTeams(id: number) {
    const fetchTeams = async () => {
        const response = await api.get('teams/' + id);
        const raw = response.data.data ?? response.data;
        teams.value = raw.map((t: any) => ({
            id: t.id,
            name: t.name,
            code: t.code,
            flag_code: t.flag_code,
            flag_url: t.flag_url,
        }));
    };
    return { teams, fetchTeams };
}  
import api from "@/services/api";
import type { Pool, Tone } from "@/types";
import { ref } from "vue";

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];

export function deriveAccent(id: number | string): Tone {
    return TONES[Number(id) % TONES.length];
}

const pools = ref<Pool[]>([]);
const currentPool = ref<Pool | null>(null);

function mapPool(p: any): Pool {
    return {
        id: String(p.id),
        name: p.name,
        code: p.join_code,
        members: p.members_count ?? 0,
        isPublic: p.is_public ?? false,
        myRank: p.my_rank ?? 0,
        myPoints: p.my_points ?? 0,
        leader: p.leader?.name ?? 'TBD',
        leaderPoints: p.leader?.points ?? 0,
        lastResults: p.last_results ?? [],
        accent: deriveAccent(p.id),
    };
}

export function usePools() {
    const fetchMyPools = async () => {
        const response = await api.get('me/pools');
        const raw = response.data.data ?? response.data;
        pools.value = raw.map(mapPool);
    };

    const fetchPoolById = async (id: string | number) => {
        const response = await api.get(`pools/${id}`);
        const raw = response.data.data ?? response.data;
        currentPool.value = mapPool(raw);
    };

    return { pools, currentPool, fetchMyPools, fetchPoolById };
}
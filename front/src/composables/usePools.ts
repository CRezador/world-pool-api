import api from "@/services/api";
import type { Pool, Tone } from "@/types";
import { ref } from "vue";

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];

function deriveAccent(id: number | string): Tone {
    return TONES[Number(id) % TONES.length];
}

const pools = ref<Pool[]>([]);

export function usePools() {
    const fetchMyPools = async () => {
        const response = await api.get('me/pools');
        const raw: Omit<Pool, 'accent'>[] = response.data.data ?? response.data;
        pools.value = raw.map(p => ({ ...p, accent: deriveAccent(p.id) }));
    };

    return { pools, fetchMyPools };
}
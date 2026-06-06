import api from "@/services/api";
import type { Pool, Tone } from "@/types";
import { ref } from "vue";

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];

export function deriveAccent(id: number | string): Tone {
    return TONES[Number(id) % TONES.length];
}

const pools = ref<Pool[]>([]);
const publicPools = ref<Pool[]>([]);
const currentPool = ref<Pool | null>(null);

function mapPool(p: any): Pool {
    return {
        id: String(p.id),
        name: p.name,
        code: p.join_code,
        members: p.members_count ?? 0,
        isPublic: p.is_public ?? false,
        isMember: p.is_member ?? false,
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

    const fetchPublicPools = async (page = 1, perPage = 10) => {
        const response = await api.get('pools', { params: { page, per_page: perPage } });
        const raw = response.data.data ?? response.data;
        publicPools.value = raw.map(mapPool);
        return response.data.meta as { current_page: number; per_page: number; total: number; last_page: number };
    };

    return { pools, publicPools, currentPool, fetchMyPools, fetchPoolById, fetchPublicPools };
}

export function joinPool(code: string) {
    return api.post('pools/join', { join_code: code });
}

export function joinPublicPool(poolId: string) {
    return api.post(`pools/${poolId}/join-public`);
}

export async function createPool(name: string, isPublic: boolean) {
    return api.post('pools', { name, is_public: isPublic });
}

export interface PoolMemberItem {
    memberId: number;
    userId: number;
    name: string;
    role: 'OWNER' | 'ADMIN' | 'MEMBER';
    joinedAt: string;
}

export async function fetchPoolMembers(poolId: string): Promise<PoolMemberItem[]> {
    const res = await api.get(`pools/${poolId}/members`);
    const raw: any[] = res.data.data ?? res.data;
    return raw.map(m => ({
        memberId: m.id,
        userId:   m.user_id,
        name:     m.user_name,
        role:     m.role,
        joinedAt: m.joined_at,
    }));
}

export function updateMemberRole(poolId: string, memberId: number, role: 'ADMIN' | 'MEMBER') {
    return api.patch(`pools/${poolId}/members/${memberId}/role`, { role });
}

export async function fetchMyMembership(poolId: string): Promise<{ role: 'OWNER' | 'ADMIN' | 'MEMBER'; joinedAt: string }> {
    const res = await api.get(`pools/${poolId}/members/me`);
    const raw = res.data.data ?? res.data;
    return { role: raw.role, joinedAt: raw.joined_at };
}

export function updatePool(poolId: string, data: { name?: string; is_public?: boolean }) {
    return api.put(`pools/${poolId}`, data);
}

export function regenerateCode(poolId: string) {
    return api.post(`pools/${poolId}/regenerate-code`);
}

export function leavePool(poolId: string) {
    return api.post(`pools/${poolId}/leave`);
}

export function deletePool(poolId: string) {
    return api.delete(`pools/${poolId}`);
}
import type { Pool } from '../types/Pool';
import { useAuth } from './useAuth';
import api from '../services/api';
import { ref } from 'vue';



export function usePool() {
    const { user } = useAuth();
    const publicPools = ref<Pool[]>([]);
    const userPools = ref<Pool[]>([]);
    const loading = ref(false);

    async function getPublicPools(): Promise<void> {
        if (!user.value) return;
        loading.value = true;
        try {
            const response = await api.get('/pools');
            publicPools.value = response.data.data;
        } finally {
            loading.value = false;
        }
    }

    async function getMyPools(): Promise<void> {
        if (!user.value) return;
        loading.value = true;
        try {
            const response = await api.get('/me/pools');
            userPools.value = response.data.data;
        } finally {
            loading.value = false;
        }
    }

    return { publicPools, userPools, loading, getPublicPools, getMyPools };
}

import { ref } from 'vue';
import type { Group } from '../types/Group';
import { useAuth } from './useAuth';
import api from '../services/api';

export function useGroup() {
    const { user } = useAuth();
    const groups = ref<Group[]>([]);

    async function getGroups(): Promise<void> {
        if (!user.value) return;
        const response = await api.get('/groups');
        groups.value = response.data.data;
    }

    return { groups, getGroups };
}
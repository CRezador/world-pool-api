import api from "@/services/api";
import type { ActivityItem } from "@/types";
import { ref } from "vue";
const activity = ref<ActivityItem[]>([]);

function mapActivity(a: any): ActivityItem {
    return {
        id: a.id,
        poolId: a.pool_id,
        poolName: a.pool_name,
        createdAt: a.created_at,
        actor: a.actor,
        isMe: a.is_me,
        action: a.action,
        subject: a.subject,
        points: a.points,
    };
}

export function useActivity() {
    const fetchActivity = async () => {
        const response = await api.get('me/activity');
        const raw = response.data.data ?? response.data;
        activity.value = (raw as any[]).map(mapActivity);
    };

    return { activity, fetchActivity };
}

import { ref, computed } from 'vue';
import type { User } from '../types/User';
import api from '../services/api';

const user = ref<User | null>(null);
const checked = ref(false);

export function useAuth() {
  async function checkAuth() {
    if (checked.value) return;
    try {
      const response = await api.get('/me');
      user.value = response.data.data;
    } catch {
      user.value = null;
    } finally {
      checked.value = true;
    }
  }

  function clearUser() {
    user.value = null;
    checked.value = false;
  }

  const isLoggedIn = computed(() => !!user.value);

  return { user, isLoggedIn, checkAuth, clearUser };
}

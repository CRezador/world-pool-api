import { ref, computed } from 'vue';
import type { User } from '../types/index';
import api from '../services/api';

const user = ref<User | null>(null);
const checked = ref(false);

export function useAuth() {
  async function checkAuth() {
    if (checked.value) return;
    try {
      const response = await api.get('/me');
      user.value = response.data.data;
      checked.value = true;
    } catch (error: any) {
      if (error.response) {
        user.value = null;
        checked.value = true;
      }
      // Erro de rede: não marca checked para tentar novamente na próxima navegação
    }
  }

  function clearUser() {
    user.value = null;
    checked.value = false;
  }

  const isLoggedIn = computed(() => !!user.value);

  return { user, isLoggedIn, checkAuth, clearUser };
}
import { ref } from 'vue';
import type { ApiMatch } from '@/types';

const match = ref<ApiMatch | null>(null);

export function useGuessModal() {
  return {
    match,
    show(m: ApiMatch) { match.value = m; },
    hide() { match.value = null; },
  };
}

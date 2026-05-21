import { ref } from 'vue';
import type { Match } from '@/types';

const match = ref<Match | null>(null);

export function useGuessModal() {
  return {
    match,
    show(m: Match) { match.value = m; },
    hide() { match.value = null; },
  };
}

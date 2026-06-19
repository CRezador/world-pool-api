import { ref } from 'vue';
import type { GuessHistoryEntry } from '@/types';

// Contexto = a linha do extrato clicada (tem times, placar e status para o cabeçalho).
const context = ref<GuessHistoryEntry | null>(null);

export function useAdversaryModal() {
  return {
    context,
    show(g: GuessHistoryEntry) { context.value = g; },
    hide() { context.value = null; },
  };
}

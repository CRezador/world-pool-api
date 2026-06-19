import type { GuessHistoryEntry } from '@/types';

// Veredito de um palpite no extrato. EM JOGO cobre partidas não finalizadas
// (agendadas ou em andamento); os demais derivam dos pontos já pontuados.
export type GuessVerdict = 'EM JOGO' | 'CRAVOU' | 'ACERTOU' | 'ERROU';

// Veredito a partir do status bruto da partida + pontos. Mesma regra do extrato:
// só vira resultado quando a partida terminou (FINISHED).
export function verdictFromMatch(matchStatus: string, points: number): GuessVerdict {
  if (matchStatus !== 'FINISHED') return 'EM JOGO';
  if (points === 3) return 'CRAVOU';
  if (points === 1) return 'ACERTOU';
  return 'ERROU';
}

export function guessVerdict(g: GuessHistoryEntry): GuessVerdict {
  return verdictFromMatch(g.status === 'pending' ? 'IN_PROGRESS' : 'FINISHED', g.pts);
}

export function verdictTone(v: GuessVerdict): string {
  if (v === 'EM JOGO') return 'magenta';
  if (v === 'CRAVOU') return 'lime';
  if (v === 'ACERTOU') return 'cobalt';
  return 'coral';
}

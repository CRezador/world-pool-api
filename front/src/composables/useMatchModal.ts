import { useGuessModal } from '@/composables/useGuessModal';
import { useAdversaryModal } from '@/composables/useAdversaryModal';
import { useGuesses } from '@/composables/useGuesses';
import type { ApiMatch, GuessHistoryEntry } from '@/types';

// Decide qual modal abrir ao clicar num card de partida:
// - SCHEDULED → modal de palpite (criar/editar o meu)
// - IN_PROGRESS / FINISHED → modal com os palpites dos adversários
export function useMatchModal() {
  const guessModal = useGuessModal();
  const adversaries = useAdversaryModal();
  const { guesses } = useGuesses();

  function toHistoryEntry(m: ApiMatch): GuessHistoryEntry {
    const my = guesses.value.find(g => g.matchId === m.id) ?? null;
    return {
      matchId: m.id,
      home: m.home.code,
      away: m.away.code,
      myHome: my?.homeScore ?? 0,
      myAway: my?.awayScore ?? 0,
      hasMyGuess: !!my,
      realHome: m.homeScore ?? 0,
      realAway: m.awayScore ?? 0,
      pts: my?.points ?? 0,
      status: m.status === 'FINISHED' ? 'scored' : 'pending',
      matchStatus: m.status,
      pool: '',
      date: m.kickoff ?? '',
    };
  }

  function open(m: ApiMatch) {
    if (m.status === 'SCHEDULED') guessModal.show(m);
    else adversaries.show(toHistoryEntry(m));
  }

  return { open };
}

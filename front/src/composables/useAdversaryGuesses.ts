import { ref } from 'vue';
import api from '@/services/api';
import type { AdversaryGuess } from '@/types';

function mapAdversary(g: any): AdversaryGuess {
  return {
    id:        g.id,
    homeScore: g.home_score,
    awayScore: g.away_score,
    points:    g.points ?? 0,
    user: {
      id:       g.user.id,
      name:     g.user.name,
      initials: g.user.initials,
    },
    pools: (g.pools ?? []).map((p: any) => ({ id: p.id, name: p.name })),
    match: {
      id:        g.match.id,
      status:    g.match.status,
      homeScore: g.match.home_score ?? null,
      awayScore: g.match.away_score ?? null,
      homeCode:  g.match.home_team.code,
      awayCode:  g.match.away_team.code,
    },
  };
}

export function useAdversaryGuesses() {
  const guesses = ref<AdversaryGuess[]>([]);
  const loading = ref(false);
  // Mensagem de bloqueio (ex: 403 antes do jogo começar) ou erro.
  const gateMessage = ref<string | null>(null);

  async function load(matchId: number) {
    loading.value = true;
    gateMessage.value = null;
    guesses.value = [];
    try {
      const res = await api.get(`matches/${matchId}/guesses/adversaries`);
      guesses.value = (res.data.data ?? []).map(mapAdversary);
    } catch (e: any) {
      gateMessage.value = e?.response?.data?.message ?? 'Erro ao carregar palpites dos adversários.';
    } finally {
      loading.value = false;
    }
  }

  return { guesses, loading, gateMessage, load };
}

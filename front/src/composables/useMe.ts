import { computed, ref } from 'vue';
import type { PlayerProfile, GuessHistoryEntry } from '@/types';
import { useAuth } from './useAuth';
import { useLeaderboard } from './useLeaderboard';
import { useGuesses } from './useGuesses';

const MONTHS = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];

const STAGE_LABELS: Record<string, string> = {
  GROUP_STAGE:    'FASE DE GRUPOS',
  SECOND_ROUND:   '2ª FASE',
  ROUND_OF_16:    'OITAVAS',
  QUARTER_FINALS: 'QUARTAS',
  SEMI_FINALS:    'SEMIFINAL',
  FINAL:          'FINAL',
};

function initials(name: string): string {
  const parts = name.trim().split(/\s+/).filter(Boolean);
  if (!parts.length) return '?';
  const first = parts[0][0] ?? '';
  const last = parts.length > 1 ? (parts[parts.length - 1][0] ?? '') : '';
  return (first + last).toUpperCase();
}

function handleFromName(name: string): string {
  const first = name.trim().split(/\s+/)[0] ?? '';
  return '@' + first.toLowerCase();
}

// kickoff_at chega como 'd/m/Y H:i' (ex: '11/06/2026 18:00') → '11/JUN'
function shortDate(s: string | null): string {
  if (!s) return '';
  const m = s.match(/^(\d{2})\/(\d{2})/);
  if (!m) return s;
  const mon = MONTHS[parseInt(m[2], 10) - 1] ?? '';
  return `${m[1]}/${mon}`;
}

function stageLabel(stage: string, group: string | null): string {
  if (group) return `GRUPO ${group}`;
  return STAGE_LABELS[stage] ?? stage.replace(/_/g, ' ');
}

export function useMe() {
  const { user } = useAuth();
  const { myStats, fetchMyStats } = useLeaderboard();
  const { guesses, fetchMyGuesses } = useGuesses();

  const loading = ref(false);
  const error = ref(false);

  // Extrato — mais recente primeiro (palpites vêm ordenados por match_id asc da API).
  const history = computed<GuessHistoryEntry[]>(() =>
    [...guesses.value]
      .sort((a, b) => b.matchId - a.matchId)
      .map((g) => {
        // Um palpite só conta como pontuado quando a partida terminou. A coluna
        // `points` tem default 0 no banco, então `points !== null` é sempre true:
        // palpites de jogos não realizados apareceriam como ERROU em vez de pendentes.
        const scored = g.match.status === 'FINISHED';
        return {
          matchId:  g.matchId,
          home:     g.match.homeTeam.code,
          away:     g.match.awayTeam.code,
          myHome:   g.homeScore,
          myAway:   g.awayScore,
          realHome: g.match.homeScore ?? 0,
          realAway: g.match.awayScore ?? 0,
          pts:      g.points ?? 0,
          status:   scored ? 'scored' : 'pending',
          matchStatus: g.match.status,
          pool:     stageLabel(g.match.stage, g.match.group),
          date:     shortDate(g.match.kickoffAt),
        } satisfies GuessHistoryEntry;
      })
  );

  // Sequência atual: palpites pontuados consecutivos (a partir do mais recente já pontuado).
  const streak = computed(() => {
    let s = 0;
    for (const h of history.value) {
      if (h.status !== 'scored') continue;
      if (h.pts > 0) s++;
      else break;
    }
    return s;
  });

  const poolsCount = computed(() => myStats.value?.poolsCount ?? 0);
  const resultCount = computed(() => myStats.value?.totalResultHits ?? 0);

  const profile = computed<PlayerProfile>(() => {
    const st = myStats.value;
    const name = user.value?.name ?? 'Você';
    const totalGuesses = st?.totalGuesses ?? 0;
    const scoredHits = (st?.totalExactHits ?? 0) + (st?.totalResultHits ?? 0);
    const hitRate = totalGuesses > 0 ? Math.round((scoredHits / totalGuesses) * 100) : 0;

    return {
      name,
      handle:       handleFromName(name),
      avatar:       initials(name),
      tone:         'lime',
      seasonPoints: st?.totalPoints ?? 0,
      hitRate,
      streak:       streak.value,
      bestRank:     st?.bestRank ?? 0,
      bestPool:     '',
      totalGuesses,
      exactCount:   st?.totalExactHits ?? 0,
    };
  });

  async function loadMe() {
    loading.value = true;
    error.value = false;
    try {
      await Promise.all([fetchMyStats(), fetchMyGuesses()]);
    } catch {
      error.value = true;
    } finally {
      loading.value = false;
    }
  }

  return { profile, history, poolsCount, resultCount, loading, error, loadMe };
}

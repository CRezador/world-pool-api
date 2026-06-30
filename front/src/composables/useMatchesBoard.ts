import api from "@/services/api";
import { ref, computed } from "vue";
import type { ApiMatch, GroupFull, KnockoutStage, MatchStatus } from "@/types";

export const KNOCKOUT_STAGES: KnockoutStage[] = [
  { id: 'SECOND_ROUND',   label: 'Segunda fase',     short: '2ª FASE', accent: 'cobalt',  note: '32 SELEÇÕES · PRIMEIRO CORTE' },
  { id: 'ROUND_OF_16',    label: 'Oitavas de final', short: 'OITAVAS', accent: 'magenta', note: '16 SELEÇÕES · ELIMINATÓRIO' },
  { id: 'QUARTER_FINALS', label: 'Quartas de final', short: 'QUARTAS', accent: 'coral',   note: '8 SELEÇÕES · MORTE SÚBITA' },
  { id: 'SEMI_FINALS',    label: 'Semifinal',        short: 'SEMI',    accent: 'cobalt',  note: '4 SELEÇÕES · VALENDO A VAGA' },
  { id: 'THIRD_PLACE',   label: '3º Lugar',         short: '3º LUGAR', accent: 'coral',  note: '2 SELEÇÕES · DISPUTA DO TERCEIRO LUGAR' },
  { id: 'FINAL',          label: 'Final',            short: 'FINAL',   accent: 'lime',    note: '2 SELEÇÕES · A TAÇA' },
];

function mapApiMatch(m: any): ApiMatch {
  return {
    id: m.id,
    gameDay: m.game_day,
    kickoff: m.kickoff_at ?? null,
    stage: m.stage,
    group: m.group ?? null,
    groupId: m.group_id ?? null,
    status: m.status as MatchStatus,
    home: {
      id: m.home_team.id,
      name: m.home_team.name,
      code: m.home_team.code,
      flag_code: m.home_team.flag_code,
      flag_url: m.home_team.flag_url,
      group: m.home_team.group,
    },
    away: {
      id: m.away_team.id,
      name: m.away_team.name,
      code: m.away_team.code,
      flag_code: m.away_team.flag_code,
      flag_url: m.away_team.flag_url,
      group: m.away_team.group,
    },
    homeScore: m.home_score ?? undefined,
    awayScore: m.away_score ?? undefined,
    homePenalties: m.home_penalties ?? null,
    awayPenalties: m.away_penalties ?? null,
    winnerTeamId: m.winner_team_id ?? null,
  };
}

function mapGroup(g: any): GroupFull {
  return {
    id: g.id,
    g: g.name,
    rows: (g.teams ?? []).map((t: any) => ({
      code: t.code,
      name: t.name,
      iso: t.flag_code ?? '',
      P: 0, V: 0, E: 0, D: 0, GP: 0, GC: 0, pts: 0, form: [],
    })),
  };
}

export function useMatchesBoard() {
  const groups = ref<GroupFull[]>([]);
  const matches = ref<ApiMatch[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  async function load() {
    loading.value = true;
    error.value = null;
    try {
      const res = await api.get('groups');
      const raw = res.data.data ?? res.data;
      groups.value = raw.map(mapGroup);
    } catch (e: any) {
      error.value = e?.message ?? 'Erro ao carregar grupos';
    } finally {
      loading.value = false;
    }
  }

  // Carrega apenas o grupo do id informado (rota /matches/:id), sem buscar todos
  // os grupos. Mescla em `groups.value` para os computeds que dependem do array.
  async function loadGroup(groupId: number | string): Promise<GroupFull | null> {
    loading.value = true;
    error.value = null;
    try {
      const res = await api.get(`groups/${groupId}`);
      const raw = res.data.data ?? res.data;
      const group = mapGroup(raw);
      const idx = groups.value.findIndex(g => g.id === group.id);
      if (idx >= 0) groups.value[idx] = group;
      else groups.value = [...groups.value, group];
      return group;
    } catch (e: any) {
      error.value = e?.message ?? 'Erro ao carregar grupo';
      return null;
    } finally {
      loading.value = false;
    }
  }

  async function loadGroupMatches(groupId: number) {
    loading.value = true;
    error.value = null;
    matches.value = [];
    try {
      const res = await api.get(`group/${groupId}/matches`);
      const raw = res.data.Matches?.data ?? res.data.Matches ?? res.data.data ?? res.data;
      matches.value = (raw as any[]).map(mapApiMatch);
    } catch (e: any) {
      error.value = e?.message ?? 'Erro ao carregar partidas do grupo';
    } finally {
      loading.value = false;
    }
  }

  async function loadKnockoutMatches() {
    loading.value = true;
    error.value = null;
    matches.value = [];
    try {
      const res = await api.get('matches');
      const raw = res.data.data ?? res.data;
      matches.value = (raw as any[]).filter(m => m.stage !== 'GROUP_STAGE').map(mapApiMatch);
    } catch (e: any) {
      error.value = e?.message ?? 'Erro ao carregar partidas';
    } finally {
      loading.value = false;
    }
  }

  const knockoutBlocks = computed(() =>
    KNOCKOUT_STAGES
      .map(stg => ({ stg, list: matches.value.filter(m => m.stage === stg.id) }))
      .filter(b => b.list.length > 0),
  );

  function getGroupMatches(groupLetter: string): ApiMatch[] {
    return matches.value.filter(m => m.stage === 'GROUP_STAGE' && m.group === groupLetter);
  }

  function groupByStatus(groupLetter: string, status: MatchStatus): ApiMatch[] {
    return getGroupMatches(groupLetter).filter(m => m.status === status);
  }

  function currentRodada(groupLetter: string): number | null {
    const all = getGroupMatches(groupLetter);
    if (all.length === 0) return null;
    const sortedDays = [...new Set(all.map(m => m.gameDay))].sort((a, b) => a - b);
    const active = sortedDays.find(day =>
      all.filter(m => m.gameDay === day).some(m => m.status !== 'FINISHED'),
    );
    return active ?? sortedDays[sortedDays.length - 1] ?? null;
  }

  return {
    groups, matches, loading, error,
    load, loadGroup, loadGroupMatches, loadKnockoutMatches,
    knockoutBlocks, getGroupMatches, groupByStatus, currentRodada,
  };
}

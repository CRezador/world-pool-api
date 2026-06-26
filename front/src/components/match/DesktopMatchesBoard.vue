<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import DesktopGroupCard from '@/components/match/DesktopGroupCard.vue';
import DesktopMatchCard from '@/components/match/DesktopMatchCard.vue';
import { useMatchesBoard, KNOCKOUT_STAGES } from '@/composables/useMatchesBoard';
import { useMatchModal } from '@/composables/useMatchModal';
import { useGuesses } from '@/composables/useGuesses';
import { toneVar, toneFg } from '@/utils/tone';
import type { ApiMatch, GroupFull, MatchStatus } from '@/types';

const router = useRouter();
const route = useRoute();

const matchModal = useMatchModal();
const { groups, matches, loading, error, load, loadGroup, loadGroupMatches, loadKnockoutMatches, groupByStatus, currentRodada } = useMatchesBoard();
const { fetchMyGuesses } = useGuesses();

const knockoutMatchesByStage = computed(() => {
  const map: Record<string, ApiMatch[]> = {};
  for (const stg of KNOCKOUT_STAGES) {
    map[stg.id] = matches.value.filter(m => m.stage === stg.id);
  }
  return map;
});

// Cada fase do mata-mata é um accordion — começa fechada para a tela não ficar cheia.
const openStages = ref<Record<string, boolean>>({});
function toggleStage(id: string) {
  openStages.value[id] = !openStages.value[id];
}

// Padrão pós-fase de grupos: ao acessar /matches sem ?phase, abre o mata-mata.
// A fase de grupos continua acessível pela aba e por ?phase=groups.
const phase = ref<'groups' | 'knockout'>('knockout');
const group = ref<string | null>(null);

async function initGroup(groupId: string | undefined) {
  if (!groupId) return;
  // Uma rota /matches/:id é sempre contexto de grupos — força a fase mesmo
  // quando o padrão de /matches é o mata-mata.
  phase.value = 'groups';
  const found = await loadGroup(groupId);
  if (found) {
    group.value = found.g;
    loadGroupMatches(found.id);
  }
}

function applyPhaseFromQuery() {
  group.value = null;
  if (route.query.phase === 'groups') {
    phase.value = 'groups';
    load(); // a lista de grupos precisa de todos os grupos, não só do último aberto
  } else {
    phase.value = 'knockout';
    loadKnockoutMatches();
  }
}

onMounted(async () => {
  fetchMyGuesses();
  const groupId = route.params.groupId as string | undefined;
  if (groupId) {
    await initGroup(groupId);
  } else {
    applyPhaseFromQuery();
  }
});

watch(() => route.params.groupId, (groupId) => {
  if (!groupId) {
    applyPhaseFromQuery();
  } else {
    initGroup(groupId as string);
  }
});

watch(() => route.query.phase, () => {
  if (!route.params.groupId) applyPhaseFromQuery();
});

const phases = [
  { id: 'groups', label: 'Fase de grupos' },
  { id: 'knockout', label: 'Mata-mata' },
] as const;

function switchPhase(p: 'groups' | 'knockout') {
  phase.value = p;
  group.value = null;
  if (p === 'knockout') loadKnockoutMatches();
  else load(); // grupos não são carregados no mount quando o padrão é o mata-mata
}

function selectGroup(g: GroupFull) {
  router.push(`/matches/${g.id}`);
}

const statusCols: MatchStatus[] = ['SCHEDULED', 'IN_PROGRESS', 'FINISHED'];
const statusTitles: Record<MatchStatus, string> = {
  SCHEDULED: 'A PALPITAR', IN_PROGRESS: 'EM PROGRESSO', FINISHED: 'ENCERRADAS',
};
const statusAccents: Record<MatchStatus, string> = {
  SCHEDULED: 'magenta', IN_PROGRESS: 'coral', FINISHED: 'cobalt',
};

const selectedGroup = computed(() =>
  group.value ? groups.value.find(g => g.g === group.value) ?? null : null,
);

const selectedGroupRodada = computed(() =>
  group.value ? currentRodada(group.value) : null,
);

function palpitar(m: ApiMatch) {
  matchModal.open(m);
}
</script>

<template>
  <div :style="{ minHeight: '100%' }">
    <!-- Header band -->
    <div :style="{
      padding: '24px 28px 20px', borderBottom: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', gap: '24px',
    }">
      <div :style="{ flex: 1 }">
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700 }">
          CALENDÁRIO · 64 PARTIDAS · 16 SEDES
        </div>
        <div
          class="font-display misprint-cobalt"
          :style="{ fontSize: '64px', lineHeight: 0.9, marginTop: '4px', textTransform: 'uppercase' }"
        >Tabela de jogos</div>
      </div>

      <!-- Phase toggle -->
      <div :style="{
        display: 'flex', border: '1.5px solid var(--ink)', borderRadius: '4px',
        overflow: 'hidden', boxShadow: '4px 4px 0 var(--ink)', flexShrink: 0,
      }">
        <button
          v-for="(p, i) in phases"
          :key="p.id"
          class="press font-display"
          :style="{
            padding: '12px 24px', fontSize: '20px', letterSpacing: '0.03em', textTransform: 'uppercase',
            border: 'none', borderRight: i === 0 ? '1.5px solid var(--ink)' : 'none',
            background: phase === p.id ? 'var(--ink)' : 'var(--paper-2)',
            color: phase === p.id ? 'var(--lime)' : 'var(--ink)',
            cursor: 'pointer', transition: 'background 0.15s, color 0.15s',
          }"
          @click="switchPhase(p.id)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- Loading / error state -->
    <div v-if="loading" :style="{ padding: '48px 28px', textAlign: 'center' }">
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', opacity: 0.6 }">CARREGANDO...</span>
    </div>
    <div v-else-if="error" :style="{ padding: '48px 28px', textAlign: 'center' }">
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', color: 'var(--coral)' }">{{ error }}</span>
    </div>

    <!-- GROUPS · grid -->
    <div v-else-if="phase === 'groups' && !group" :style="{ padding: '22px 28px 30px' }">
      <div :style="{ minWidth: 0 }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          A CLASSIFICAÇÃO · {{ groups.length }} CHAVES
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Os grupos
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Toque num grupo pra abrir os jogos e palpitar · ● classificado direto
        </div>
      </div>
      <div :style="{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: '16px', marginTop: '18px' }">
        <DesktopGroupCard
          v-for="g in groups"
          :key="g.g"
          :group="g"
          @click="selectGroup(g)"
        />
      </div>
    </div>

    <!-- GROUPS · games of one group -->
    <div v-else-if="phase === 'groups' && group && selectedGroup" :style="{ padding: '22px 28px 30px' }">
      <!-- Group hero -->
      <div :style="{
        display: 'flex', alignItems: 'stretch',
        border: '1.5px solid var(--ink)', boxShadow: '4px 4px 0 var(--ink)',
        borderRadius: '3px', overflow: 'hidden', marginBottom: '26px', background: 'var(--paper-2)',
      }">
        <div :style="{
          background: 'var(--cobalt)', color: 'var(--paper)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          padding: '0 26px', borderRight: '1.5px solid var(--ink)',
        }">
          <span class="font-display" :style="{ fontSize: '92px', lineHeight: 0.8, letterSpacing: '0.02em', textShadow: '4px 4px 0 var(--ink)' }">
            {{ group }}
          </span>
        </div>
        <div :style="{ flex: 1, padding: '16px 22px', display: 'flex', flexDirection: 'column', justifyContent: 'center' }">
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, opacity: 0.7 }">
            FASE DE GRUPOS · CHAVE {{ group }}
          </div>
          <div class="font-display" :style="{ fontSize: '30px', lineHeight: 1, margin: '4px 0 12px', textTransform: 'uppercase' }">
            Grupo {{ group }}
          </div>
          <div :style="{ display: 'flex', gap: '18px', flexWrap: 'wrap' }">
            <div
              v-for="row in selectedGroup.rows"
              :key="row.code"
              :style="{ display: 'flex', alignItems: 'center', gap: '8px' }"
            >
              <img
                :src="`https://flagcdn.com/${row.iso}.svg`"
                alt=""
                :style="{ width: '24px', height: '16px', border: '1px solid var(--ink)', borderRadius: '2px', objectFit: 'cover', flexShrink: 0 }"
              />
              <span class="font-display" :style="{ fontSize: '16px' }">{{ row.code }}</span>
            </div>
          </div>
        </div>
        <button
          class="font-mono press"
          :style="{
            alignSelf: 'flex-start', margin: '14px', flexShrink: 0,
            padding: '9px 14px', fontSize: '11px', letterSpacing: '0.12em', fontWeight: 700,
            background: 'var(--ink)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--magenta)',
            cursor: 'pointer', borderRadius: '3px',
          }"
          @click="router.push({ path: '/matches', query: { phase: 'groups' } })"
        >← GRUPOS</button>
      </div>

      <!-- Section rule -->
      <div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          OS PALPITES · GRUPO {{ group }}
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Jogos pra palpitar<template v-if="selectedGroupRodada"> · Rodada {{ selectedGroupRodada }}</template>
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Acertou o vencedor +1 · cravou o placar +3
        </div>
      </div>

      <!-- Status board · 3 columns -->
      <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '20px', marginTop: '18px' }">
        <div v-for="status in statusCols" :key="status">
          <div
            class="font-mono"
            :style="{
              display: 'flex', justifyContent: 'space-between', alignItems: 'center',
              fontSize: '11px', letterSpacing: '0.18em', fontWeight: 700, padding: '10px 14px',
              background: toneVar(statusAccents[status]), color: toneFg(statusAccents[status]),
              border: '1.5px solid var(--ink)',
            }"
          >
            <span>{{ statusTitles[status] }}</span>
            <span>{{ groupByStatus(group!, status).length }}</span>
          </div>
          <div :style="{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '12px' }">
            <div
              v-if="groupByStatus(group!, status).length === 0"
              class="font-mono"
              :style="{ padding: '16px', opacity: 0.5, fontSize: '11px', border: '1.5px dashed var(--ink)', textAlign: 'center' }"
            >nenhum jogo</div>
            <DesktopMatchCard
              v-for="m in groupByStatus(group!, status)"
              :key="m.id"
              :match="m"
              :accent="statusAccents[status]"
              @palpitar="palpitar(m)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- KNOCKOUT -->
    <div v-else-if="phase === 'knockout'" :style="{ padding: '22px 28px 30px' }">
      <div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          ELIMINATÓRIAS · A FORCA
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Mata-mata
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Cada fase, um corte. Palpite jogo a jogo.
        </div>
      </div>

      <div :style="{ display: 'flex', flexDirection: 'column', gap: '26px', marginTop: '20px' }">
        <div v-for="stg in KNOCKOUT_STAGES" :key="stg.id">
          <!-- Phase header (accordion) -->
          <div
            :style="{
              display: 'flex', alignItems: 'stretch', cursor: 'pointer',
              border: '1.5px solid var(--ink)', boxShadow: '3px 3px 0 var(--ink)',
              borderRadius: '3px', overflow: 'hidden',
              marginBottom: openStages[stg.id] ? '16px' : '0',
            }"
            @click="toggleStage(stg.id)"
          >
            <div :style="{
              background: toneVar(stg.accent), color: toneFg(stg.accent),
              padding: '12px 20px', display: 'flex', alignItems: 'center', gap: '14px',
              borderRight: '1.5px solid var(--ink)',
            }">
              <span class="font-display" :style="{ fontSize: '34px', lineHeight: 1, letterSpacing: '0.03em', textTransform: 'uppercase', whiteSpace: 'nowrap' }">
                {{ stg.label }}
              </span>
            </div>
            <div :style="{
              flex: 1, background: 'var(--paper-2)',
              display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '0 18px',
            }">
              <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.75 }">
                {{ stg.note }}
              </span>
              <div :style="{ display: 'flex', alignItems: 'center', gap: '14px' }">
                <span
                  class="font-mono"
                  :style="{
                    fontSize: '11px', letterSpacing: '0.14em', fontWeight: 700,
                    padding: '5px 12px', background: 'var(--ink)', color: 'var(--paper)', borderRadius: '999px',
                  }"
                >
                  {{ knockoutMatchesByStage[stg.id].length || 'TBD' }}
                  <template v-if="knockoutMatchesByStage[stg.id].length">
                    {{ knockoutMatchesByStage[stg.id].length === 1 ? 'JOGO' : 'JOGOS' }}
                  </template>
                </span>
                <span
                  class="font-display"
                  :style="{
                    fontSize: '20px', lineHeight: 1,
                    transition: 'transform 0.18s ease',
                    transform: openStages[stg.id] ? 'rotate(90deg)' : 'rotate(0deg)',
                  }"
                >›</span>
              </div>
            </div>
          </div>

          <!-- Jogos (expandidos) -->
          <div v-show="openStages[stg.id]" :style="{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '16px' }">
            <DesktopMatchCard
              v-for="m in knockoutMatchesByStage[stg.id]"
              :key="m.id"
              :match="m"
              :accent="stg.accent"
              @palpitar="palpitar(m)"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

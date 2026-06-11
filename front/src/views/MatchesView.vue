<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import SectionHead from '@/components/SectionHead.vue';
import MatchListCard from '@/components/match/MatchListCard.vue';
import MatchGroupCard from '@/components/match/MatchGroupCard.vue';
import DesktopMatchesBoard from '@/components/match/DesktopMatchesBoard.vue';
import GuessModal from '@/components/modals/GuessModal.vue';
import { useMatchesBoard, KNOCKOUT_STAGES } from '@/composables/useMatchesBoard';
import { useGuessModal } from '@/composables/useGuessModal';
import { useGuesses } from '@/composables/useGuesses';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { toneVar } from '@/utils/tone';
import type { ApiMatch, GroupFull } from '@/types';

const router = useRouter();
const route = useRoute();
const { isDesktop } = useBreakpoint();

const guessModal = useGuessModal();
const { groups, matches, loading, error, load, loadGroup, loadGroupMatches, loadKnockoutMatches, groupByStatus, currentRodada } = useMatchesBoard();
const { fetchMyGuesses } = useGuesses();

const phase = ref<'groups' | 'knockout'>('groups');
const group = ref<string | null>(null);

const phases = [
  { id: 'groups', label: 'Grupos' },
  { id: 'knockout', label: 'Mata-mata' },
] as const;

const selectedGroup = computed(() =>
  group.value ? groups.value.find(g => g.g === group.value) ?? null : null,
);

const selectedGroupRodada = computed(() =>
  group.value ? currentRodada(group.value) : null,
);

const knockoutMatchesByStage = computed(() => {
  const map: Record<string, ApiMatch[]> = {};
  for (const stg of KNOCKOUT_STAGES) {
    map[stg.id] = matches.value.filter(m => m.stage === stg.id);
  }
  return map;
});

async function initGroup(groupId: string) {
  const found = await loadGroup(groupId);
  if (found) {
    group.value = found.g;
    loadGroupMatches(found.id);
  }
}

function selectGroup(g: GroupFull) {
  router.push(`/matches/${g.id}`);
}

function switchPhase(p: 'groups' | 'knockout') {
  phase.value = p;
  group.value = null;
  router.push('/matches');
  if (p === 'knockout') loadKnockoutMatches();
}

function goBack() {
  group.value = null;
  router.push('/matches');
}

function palpitar(m: ApiMatch) {
  guessModal.show(m);
}

function applyPhaseFromQuery() {
  group.value = null;
  if (route.query.phase === 'knockout') {
    phase.value = 'knockout';
    loadKnockoutMatches();
  } else {
    phase.value = 'groups';
    load(); // a lista de grupos precisa de todos os grupos, não só do último aberto
  }
}

function loadForRoute() {
  fetchMyGuesses();
  const groupId = route.params.groupId as string | undefined;
  if (groupId) {
    initGroup(groupId);
  } else {
    applyPhaseFromQuery();
  }
}

onMounted(() => {
  // No desktop quem carrega os dados é o DesktopMatchesBoard (que tem estado
  // próprio); o pai só busca quando está renderizando o layout mobile, evitando
  // a chamada duplicada de groups/guesses.
  if (!isDesktop.value) loadForRoute();
});

// Ao redimensionar de volta para o layout mobile sem dados, carrega.
watch(isDesktop, (desk) => {
  if (!desk && !groups.value.length) loadForRoute();
});

watch(() => route.params.groupId, (groupId) => {
  if (isDesktop.value) return; // no desktop, o DesktopMatchesBoard trata a navegação
  if (!groupId) {
    applyPhaseFromQuery();
  } else {
    initGroup(groupId as string);
  }
});

watch(() => route.query.phase, () => {
  if (isDesktop.value) return;
  if (!route.params.groupId) applyPhaseFromQuery();
});
</script>

<template>
  <DesktopMatchesBoard v-if="isDesktop" />

  <div v-else :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 12px', borderBottom: '1.5px solid var(--ink)' }">
      <SectionHead kicker="CALENDÁRIO · COPA 26" title="Tabela de jogos" />
    </div>

    <!-- Phase toggle -->
    <div :style="{ padding: '12px 18px', borderBottom: '1.5px solid var(--ink)' }">
      <div :style="{
        display: 'flex', width: '100%',
        border: '1.5px solid var(--ink)', borderRadius: '3px', overflow: 'hidden',
        boxShadow: '3px 3px 0 var(--ink)',
      }">
        <button
          v-for="(p, i) in phases"
          :key="p.id"
          class="press font-display"
          :style="{
            flex: 1, padding: '10px 8px', border: 'none',
            borderRight: i === 0 ? '1.5px solid var(--ink)' : 'none',
            fontSize: '17px', letterSpacing: '0.03em', textTransform: 'uppercase',
            background: phase === p.id ? 'var(--ink)' : 'var(--paper-2)',
            color: phase === p.id ? 'var(--lime)' : 'var(--ink)', cursor: 'pointer',
          }"
          @click="switchPhase(p.id)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- Loading / error -->
    <div v-if="loading" :style="{ padding: '40px 18px', textAlign: 'center' }">
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', opacity: 0.5 }">CARREGANDO...</span>
    </div>
    <div v-else-if="error" :style="{ padding: '40px 18px', textAlign: 'center' }">
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em', color: 'var(--coral)' }">{{ error }}</span>
    </div>

    <!-- GRUPOS · lista de grupos -->
    <div v-else-if="phase === 'groups' && !group" :style="{ padding: '14px 18px 20px' }">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--magenta)' }">
        A CLASSIFICAÇÃO · {{ groups.length }} CHAVES
      </div>
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.04em', opacity: 0.7, marginTop: '4px', marginBottom: '14px' }">
        Toque num grupo pra abrir os jogos e palpitar.
      </div>
      <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }">
        <MatchGroupCard
          v-for="g in groups"
          :key="g.g"
          :group="g"
          @click="selectGroup(g)"
        />
      </div>
    </div>

    <!-- GRUPOS · jogos do grupo selecionado -->
    <div v-else-if="phase === 'groups' && group && selectedGroup">
      <div :style="{ padding: '12px 18px', borderBottom: '1.5px solid var(--ink)', background: 'var(--paper-2)' }">
        <button
          class="font-mono press"
          :style="{
            padding: '7px 12px', fontSize: '10px', letterSpacing: '0.12em', fontWeight: 700,
            background: 'var(--ink)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--magenta)',
            cursor: 'pointer', borderRadius: '3px', marginBottom: '12px',
          }"
          @click="goBack"
        >← GRUPOS</button>
        <div :style="{ display: 'flex', alignItems: 'center', gap: '14px' }">
          <div
            class="font-display"
            :style="{ fontSize: '60px', lineHeight: 0.78, color: 'var(--cobalt)', textShadow: '3px 3px 0 var(--ink)', flexShrink: 0 }"
          >{{ group }}</div>
          <div :style="{ minWidth: 0 }">
            <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }">
              FASE DE GRUPOS · CHAVE {{ group }}
              <template v-if="selectedGroupRodada"> · RODADA {{ selectedGroupRodada }}</template>
            </div>
            <div class="font-display" :style="{ fontSize: '26px', textTransform: 'uppercase', lineHeight: 1, marginTop: '2px' }">
              Grupo {{ group }}
            </div>
          </div>
        </div>
        <div :style="{ display: 'flex', gap: '12px', marginTop: '12px', flexWrap: 'wrap' }">
          <div
            v-for="row in selectedGroup.rows"
            :key="row.code"
            :style="{ display: 'flex', alignItems: 'center', gap: '6px' }"
          >
            <img
              :src="`https://flagcdn.com/40x30/${row.iso}.png`"
              alt=""
              :style="{ width: '20px', height: '13px', border: '1px solid var(--ink)', borderRadius: '2px', objectFit: 'cover' }"
            />
            <span class="font-display" :style="{ fontSize: '14px' }">{{ row.code }}</span>
          </div>
        </div>
      </div>

      <div :style="{ padding: '12px 18px 6px' }">
        <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }">
          OS PALPITES · GRUPO {{ group }}
        </span>
      </div>
      <div :style="{ padding: '6px 18px 20px', display: 'flex', flexDirection: 'column', gap: '12px' }">
        <MatchListCard
          v-for="m in groupByStatus(group, 'SCHEDULED').concat(groupByStatus(group, 'IN_PROGRESS')).concat(groupByStatus(group, 'FINISHED'))"
          :key="m.id"
          :match="m"
          @click="palpitar(m)"
        />
      </div>
    </div>

    <!-- MATA-MATA -->
    <div v-else-if="phase === 'knockout'" :style="{ padding: '14px 18px 20px' }">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--magenta)' }">
        ELIMINATÓRIAS · A FORCA
      </div>
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.04em', opacity: 0.7, marginTop: '4px' }">
        Cada fase, um corte. Palpite jogo a jogo.
      </div>

      <div
        v-for="stg in KNOCKOUT_STAGES"
        :key="stg.id"
        :style="{ marginTop: '20px' }"
      >
        <div :style="{
          display: 'flex', alignItems: 'center', justifyContent: 'space-between',
          padding: '9px 12px', background: toneVar(stg.accent), color: 'var(--paper)',
          border: '1.5px solid var(--ink)', borderRadius: '3px', boxShadow: '2px 2px 0 var(--ink)',
          marginBottom: '12px',
        }">
          <span class="font-display" :style="{ fontSize: '21px', textTransform: 'uppercase', letterSpacing: '0.03em' }">
            {{ stg.label }}
          </span>
          <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700 }">
            {{ knockoutMatchesByStage[stg.id].length || 'TBD' }}
            <template v-if="knockoutMatchesByStage[stg.id].length">
              {{ knockoutMatchesByStage[stg.id].length === 1 ? 'JOGO' : 'JOGOS' }}
            </template>
          </span>
        </div>

        <div v-if="knockoutMatchesByStage[stg.id].length" :style="{ display: 'flex', flexDirection: 'column', gap: '12px' }">
          <MatchListCard
            v-for="m in knockoutMatchesByStage[stg.id]"
            :key="m.id"
            :match="m"
            @click="palpitar(m)"
          />
        </div>

        <!-- TBD placeholder -->
        <div
          v-else
          class="perf-bottom"
          :style="{
            background: 'var(--paper-2)', border: '1.5px dashed var(--ink)',
            borderRadius: '4px', padding: '18px',
            display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '8px',
          }"
        >
          <div :style="{ display: 'flex', alignItems: 'center', gap: '12px' }">
            <div :style="{ width: '28px', height: '28px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.3 }" />
            <span class="font-display" :style="{ fontSize: '16px', opacity: 0.3 }">×</span>
            <div :style="{ width: '28px', height: '28px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.3 }" />
          </div>
          <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.35 }">PARTIDAS NÃO DEFINIDAS</span>
        </div>
      </div>
    </div>

    <!-- GuessModal (compact) -->
    <GuessModal
      :match="guessModal.match.value"
      variant="compact"
      @close="guessModal.hide()"
    />
  </div>
</template>

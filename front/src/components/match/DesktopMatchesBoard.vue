<script setup lang="ts">
import { ref, computed } from 'vue';
import FlagImg from '@/components/FlagImg.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import StageBadge from '@/components/StageBadge.vue';
import { MATCHES, TEAMS, toneVar } from '@/data/mock';
import type { Match, MatchStatus } from '@/types';
import { useGuessModal } from '@/composables/useGuessModal';

const guess = useGuessModal();

const filters = ['TODOS','GRUPOS','OITAVAS','QUARTAS','SEMI','FINAL'];
const filter = ref('TODOS');

const cols: MatchStatus[] = ['SCHEDULED', 'IN_PROGRESS', 'FINISHED'];
const titles: Record<MatchStatus, string> = {
  SCHEDULED: 'A PALPITAR',
  IN_PROGRESS: 'AO VIVO',
  FINISHED: 'ENCERRADAS',
};
const accents: Record<MatchStatus, string> = {
  SCHEDULED: 'magenta',
  IN_PROGRESS: 'coral',
  FINISHED: 'cobalt',
};

const byStatus = computed(() => {
  const out: Record<MatchStatus, typeof MATCHES> = {
    SCHEDULED: [], IN_PROGRESS: [], FINISHED: [],
  };
  for (const m of MATCHES) out[m.status].push(m);
  return out;
});

function myGuessLabel(m: Match) {
  if (m.status === 'SCHEDULED') return 'SEM PALPITE AINDA';
  if (m.status === 'IN_PROGRESS') return 'PALPITE · 2-0';
  return 'MEU PALPITE · ' + (m.id === 1 ? '2-1 ✓+3' : '1-1 · 0');
}
</script>

<template>
  <div :style="{ minHeight: '100%' }">
    <div :style="{ padding: '24px 28px 8px', display: 'flex', alignItems: 'flex-end', gap: '18px' }">
      <div :style="{ flex: 1 }">
        <div
          class="font-mono"
          :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700 }"
        >CALENDÁRIO · 64 PARTIDAS · 16 SEDES</div>
        <div
          class="font-display misprint-cobalt"
          :style="{
            fontSize: '64px', lineHeight: 0.9, marginTop: '4px', textTransform: 'uppercase',
          }"
        >Tabela de jogos</div>
      </div>
      <div :style="{ display: 'flex', gap: '8px' }">
        <ChipToggle
          v-for="f in filters"
          :key="f"
          :active="filter === f"
          @click="filter = f"
        >{{ f }}</ChipToggle>
      </div>
    </div>

    <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '20px', padding: '20px 28px 28px' }">
      <div v-for="status in cols" :key="status">
        <div
          class="font-mono"
          :style="{
            display: 'flex', justifyContent: 'space-between', alignItems: 'center',
            fontSize: '11px', letterSpacing: '0.18em', fontWeight: 700,
            padding: '10px 14px',
            background: toneVar(accents[status]),
            color: accents[status] === 'lime' ? 'var(--ink)' : 'var(--paper)',
            border: '1.5px solid var(--ink)',
          }"
        >
          <span>{{ titles[status] }}</span>
          <span>{{ byStatus[status].length }}</span>
        </div>
        <div :style="{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '12px' }">
          <div
            v-if="byStatus[status].length === 0"
            class="font-mono"
            :style="{
              padding: '16px', opacity: 0.5, fontSize: '11px',
              border: '1.5px dashed var(--ink)', textAlign: 'center',
            }"
          >nenhum jogo</div>
          <div
            v-for="m in byStatus[status]"
            :key="m.id"
            class="perf-bottom"
            :style="{
              background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
              boxShadow: `4px 4px 0 ${toneVar(accents[status])}, 4px 4px 0 1px var(--ink)`,
            }"
          >
            <div :style="{
              display: 'flex', justifyContent: 'space-between', alignItems: 'center',
              padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
            }">
              <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">
                {{ m.day.toUpperCase() }} · {{ m.kickoff }}
              </span>
              <StageBadge :stage="m.stage" :group="m.group" :tone="accents[status]" />
            </div>
            <div :style="{ padding: '12px' }">
              <div :style="{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '8px' }">
                <FlagImg :team="m.home" :size="20" :radius="2" />
                <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ TEAMS[m.home].name }}</span>
                <span
                  v-if="m.status !== 'SCHEDULED'"
                  class="font-display"
                  :style="{ fontSize: '22px' }"
                >{{ m.homeScore }}</span>
              </div>
              <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
                <FlagImg :team="m.away" :size="20" :radius="2" />
                <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ TEAMS[m.away].name }}</span>
                <span
                  v-if="m.status !== 'SCHEDULED'"
                  class="font-display"
                  :style="{ fontSize: '22px' }"
                >{{ m.awayScore }}</span>
              </div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', opacity: 0.6, marginTop: '10px', letterSpacing: '0.08em' }"
              >📍 {{ m.venue.toUpperCase() }}</div>
            </div>
            <div :style="{
              padding: '8px 12px',
              borderTop: '1px dashed var(--ink)',
              display: 'flex', justifyContent: 'space-between', alignItems: 'center',
              gap: '8px',
            }">
              <span
                class="font-mono"
                :style="{
                  fontSize: '10px', letterSpacing: '0.1em',
                  opacity: m.status === 'SCHEDULED' ? 0.7 : 1,
                }"
              >{{ myGuessLabel(m) }}</span>
              <button
                v-if="m.status === 'SCHEDULED'"
                class="font-display press"
                :style="{
                  background: 'var(--magenta)', color: 'var(--paper)',
                  border: '1.5px solid var(--ink)',
                  boxShadow: '2px 2px 0 var(--ink)',
                  padding: '5px 12px', fontSize: '12px', letterSpacing: '0.06em',
                  textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
                  display: 'inline-flex', alignItems: 'center', gap: '6px',
                }"
                @click.stop="guess.show(m)"
              >
                <span :style="{ fontSize: '13px' }">✎</span>
                Palpitar
              </button>
              <span
                v-else
                class="font-display"
                :style="{ fontSize: '12px', color: toneVar(accents[status]) }"
              >→</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

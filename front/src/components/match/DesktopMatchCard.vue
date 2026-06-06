<script setup lang="ts">
import { computed } from 'vue';
import FlagImg from '@/components/FlagImg.vue';
import StageBadge from '@/components/StageBadge.vue';
import { TEAMS, toneVar } from '@/data/mock';
import type { Match } from '@/types';

const props = defineProps<{ match: Match; accent: string }>();
defineEmits<{ (e: 'palpitar'): void }>();

const home = computed(() => (props.match.home ? TEAMS[props.match.home as string] : undefined));
const away = computed(() => (props.match.away ? TEAMS[props.match.away as string] : undefined));
const isTbd = computed(() => !home.value || !away.value);
const accentVar = computed(() => toneVar(props.accent));

const myGuessLabel = computed(() => {
  if (props.match.status === 'SCHEDULED') return 'SEM PALPITE AINDA';
  if (props.match.status === 'IN_PROGRESS') return 'PALPITE · 2-0';
  return 'MEU PALPITE · ' + (props.match.id === 1 ? '2-1 ✓+3' : '1-1 · 0');
});
</script>

<template>
  <!-- TBD — bracket slot still open -->
  <div
    v-if="isTbd"
    :style="{
      background: 'var(--paper-2)', border: '1.5px dashed var(--ink)',
      boxShadow: `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
    }"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.85 }">
        {{ match.day ? `${match.day.toUpperCase()} · ${match.kickoff}` : 'DATA A DEFINIR' }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group" :tone="accent" />
    </div>
    <div :style="{ padding: '12px', display: 'flex', flexDirection: 'column', gap: '8px' }">
      <div
        v-for="slot in [match.homeSlot || 'A definir', match.awaySlot || 'A definir']"
        :key="slot"
        :style="{ display: 'flex', alignItems: 'center', gap: '10px' }"
      >
        <span
          class="font-display"
          :style="{
            width: '22px', height: '16px', flexShrink: 0, borderRadius: '2px',
            border: '1px solid var(--ink)',
            background: 'repeating-linear-gradient(45deg, var(--paper), var(--paper) 3px, rgba(0,0,0,0.12) 3px, rgba(0,0,0,0.12) 6px)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
          }"
        ><span :style="{ fontSize: '11px', opacity: 0.55 }">?</span></span>
        <span class="font-display" :style="{ fontSize: '15px', opacity: 0.85, whiteSpace: 'nowrap' }">{{ slot }}</span>
      </div>
      <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, marginTop: '2px', letterSpacing: '0.08em' }">
        📍 {{ match.venue ? match.venue.toUpperCase() : 'SEDE A DEFINIR' }}
      </div>
    </div>
    <div :style="{
      padding: '8px 12px', borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: '8px',
    }">
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.7 }">
        AGUARDANDO CLASSIFICADOS
      </span>
      <span
        class="font-mono"
        :style="{
          fontSize: '10px', letterSpacing: '0.1em', fontWeight: 700,
          display: 'inline-flex', alignItems: 'center', gap: '5px',
          padding: '4px 10px', borderRadius: '3px',
          background: 'var(--paper)', border: '1.5px solid var(--ink)', opacity: 0.75,
        }"
      ><span :style="{ fontSize: '11px' }">🔒</span> TBD</span>
    </div>
  </div>

  <!-- Regular match card -->
  <div
    v-else
    class="perf-bottom"
    :style="{
      background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
      boxShadow: `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
    }"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">
        {{ match.day.toUpperCase() }} · {{ match.kickoff }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group" :tone="accent" />
    </div>
    <div :style="{ padding: '12px' }">
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '8px' }">
        <FlagImg :team="(match.home as string)" :size="20" :radius="2" />
        <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ home!.name }}</span>
        <span
          v-if="match.status !== 'SCHEDULED'"
          class="font-display"
          :style="{ fontSize: '22px' }"
        >{{ match.homeScore }}</span>
      </div>
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <FlagImg :team="(match.away as string)" :size="20" :radius="2" />
        <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ away!.name }}</span>
        <span
          v-if="match.status !== 'SCHEDULED'"
          class="font-display"
          :style="{ fontSize: '22px' }"
        >{{ match.awayScore }}</span>
      </div>
      <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, marginTop: '10px', letterSpacing: '0.08em' }">
        📍 {{ match.venue.toUpperCase() }}
      </div>
    </div>
    <div :style="{
      padding: '8px 12px', borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: '8px',
    }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: match.status === 'SCHEDULED' ? 0.7 : 1 }"
      >{{ myGuessLabel }}</span>
      <button
        v-if="match.status === 'SCHEDULED'"
        class="font-display press"
        :style="{
          background: 'var(--magenta)', color: 'var(--paper)',
          border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
          padding: '5px 12px', fontSize: '12px', letterSpacing: '0.06em',
          textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          display: 'inline-flex', alignItems: 'center', gap: '6px',
        }"
        @click.stop="$emit('palpitar')"
      >
        <span :style="{ fontSize: '13px' }">✎</span>
        Palpitar
      </button>
      <span
        v-else
        class="font-display"
        :style="{ fontSize: '12px', color: accentVar }"
      >→</span>
    </div>
  </div>
</template>

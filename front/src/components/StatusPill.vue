<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';
import type { MatchStatus } from '@/types';

const props = defineProps<{
  status: MatchStatus;
  points?: number;
}>();

const finishedTone = computed(() =>
  props.points === 3 ? 'lime' : props.points === 1 ? 'cobalt' : 'ink',
);
const finishedLabel = computed(() =>
  props.points === 3 ? '+3 EXATO' : props.points === 1 ? '+1 RESULTADO' : 'ENCERRADA',
);

const finishedStyle = computed(() => ({
  fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em',
  padding: '3px 7px',
  background: toneVar(finishedTone.value),
  color: finishedTone.value === 'lime' ? 'var(--ink)' : 'var(--paper)',
  borderRadius: '2px',
}));
</script>

<template>
  <span
    v-if="status === 'IN_PROGRESS'"
    class="font-mono"
    :style="{
      fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em',
      display: 'inline-flex', alignItems: 'center', gap: '5px',
      padding: '3px 7px', background: 'var(--coral)', color: 'var(--paper)',
      borderRadius: '999px',
    }"
  >
    <span
      class="live-dot"
      :style="{
        width: '6px', height: '6px', borderRadius: '50%', background: 'var(--paper)',
      }"
    />
    AO VIVO
  </span>
  <span
    v-else-if="status === 'FINISHED'"
    class="font-mono"
    :style="finishedStyle"
  >{{ finishedLabel }}</span>
  <span
    v-else
    class="font-mono"
    :style="{
      fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em',
      padding: '3px 7px',
      border: '1.5px solid var(--ink)',
      color: 'var(--ink)', background: 'transparent',
      borderRadius: '2px',
    }"
  >AGENDADA</span>
</template>

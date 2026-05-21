<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';
import type { Stage } from '@/types';

const props = withDefaults(defineProps<{
  stage: Stage;
  group?: string;
  tone?: string;
}>(), {
  tone: 'cobalt',
});

const STAGE_LABELS: Record<Stage, string> = {
  GROUP_STAGE: 'GRUPO',
  ROUND_OF_16: 'OITAVAS',
  QUARTER_FINALS: 'QUARTAS',
  SEMI_FINALS: 'SEMI',
  THIRD_PLACE: '3º LUGAR',
  FINAL: 'FINAL',
};

const label = computed(() => {
  if (props.stage === 'GROUP_STAGE' && props.group) return `GRUPO ${props.group}`;
  return STAGE_LABELS[props.stage] || props.stage;
});

const style = computed(() => ({
  fontSize: '10px', fontWeight: 700, letterSpacing: '0.12em',
  padding: '3px 7px',
  background: toneVar(props.tone),
  color: props.tone === 'lime' ? 'var(--ink)' : 'var(--paper)',
  borderRadius: '2px',
}));
</script>

<template>
  <span class="font-mono" :style="style">{{ label }}</span>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  accent?: string;
  perf?: boolean;
  clickable?: boolean;
}>(), {
  accent: 'ink',
  perf: false,
  clickable: false,
});

const cardStyle = computed(() => ({
  position: 'relative' as const,
  background: 'var(--paper-2)',
  border: '1.5px solid var(--ink)',
  boxShadow: `4px 4px 0 ${toneVar(props.accent)}, 4px 4px 0 1px var(--ink)`,
  borderRadius: '6px',
  cursor: props.clickable ? 'pointer' : 'default',
}));
</script>

<template>
  <div :class="{ 'perf-bottom': perf }" :style="cardStyle">
    <slot />
  </div>
</template>

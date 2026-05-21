<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  kicker?: string;
  title: string;
  tone?: string;
}>(), {
  tone: 'ink',
});

const kickerStyle = computed(() => ({
  fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700,
  color: toneVar(props.tone), textTransform: 'uppercase' as const,
  marginBottom: '4px',
}));
</script>

<template>
  <div :style="{ display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', gap: '12px' }">
    <div>
      <div v-if="kicker" class="font-mono" :style="kickerStyle">{{ kicker }}</div>
      <div
        class="font-display"
        :style="{ fontSize: '28px', lineHeight: 0.95, letterSpacing: '0.01em', textTransform: 'uppercase' }"
      >{{ title }}</div>
    </div>
    <slot name="action" />
  </div>
</template>

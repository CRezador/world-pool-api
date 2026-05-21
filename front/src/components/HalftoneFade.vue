<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  tone?: string;
  height?: number;
}>(), {
  tone: 'ink',
  height: 60,
});

const wrapStyle = computed(() => ({
  height: `${props.height}px`,
  position: 'relative' as const,
  overflow: 'hidden' as const,
  maskImage: 'linear-gradient(to bottom, black, transparent)',
  WebkitMaskImage: 'linear-gradient(to bottom, black, transparent)',
  color: toneVar(props.tone),
}));
</script>

<template>
  <div :style="wrapStyle">
    <div class="halftone-lg" :style="{ position: 'absolute', inset: 0 }" />
  </div>
</template>

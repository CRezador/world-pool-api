<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  tone?: string;
  rotate?: number;
}>(), {
  tone: 'magenta',
  rotate: -6,
});

const style = computed(() => ({
  color: toneVar(props.tone),
  transform: `rotate(${props.rotate}deg)`,
}));
</script>

<template>
  <span class="stamp" :style="style"><slot /></span>
</template>

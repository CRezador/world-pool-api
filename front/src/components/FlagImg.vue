<script setup lang="ts">
import { computed } from 'vue';
import { TEAMS } from '@/data/mock';

const props = withDefaults(defineProps<{
  team: string;
  size?: number;
  shape?: 'rect' | 'circle';
  radius?: number;
}>(), {
  size: 28,
  shape: 'rect',
  radius: 2,
});

const team = computed(() => TEAMS[props.team]);
const widthRect = computed(() => Math.round(props.size * 1.5));
const widthValue = computed(() => props.shape === 'circle' ? props.size : widthRect.value);
const borderRadius = computed(() => props.shape === 'circle' ? '50%' : `${props.radius}px`);
</script>

<template>
  <img
    v-if="team"
    :src="`https://flagcdn.com/w160/${team.iso}.png`"
    :srcset="`https://flagcdn.com/w160/${team.iso}.png 1x, https://flagcdn.com/w320/${team.iso}.png 2x`"
    :alt="team.name"
    :style="{
      width: `${widthValue}px`,
      height: `${size}px`,
      objectFit: 'cover',
      borderRadius,
      display: 'inline-block',
    }"
  />
</template>

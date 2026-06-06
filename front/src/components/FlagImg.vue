<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(defineProps<{
  flagCode?: string;
  teamName?: string;
  size?: number;
  shape?: 'rect' | 'circle';
  radius?: number;
}>(), {
  size: 28,
  shape: 'rect',
  radius: 2,
});

const iso = computed(() => props.flagCode);
const name = computed(() => props.teamName ?? '');
const widthRect = computed(() => Math.round(props.size * 1.5));
const widthValue = computed(() => props.shape === 'circle' ? props.size : widthRect.value);
const borderRadius = computed(() => props.shape === 'circle' ? '50%' : `${props.radius}px`);
</script>

<template>
  <img
    v-if="iso"
    :src="`https://flagcdn.com/w160/${iso}.png`"
    :srcset="`https://flagcdn.com/w160/${iso}.png 1x, https://flagcdn.com/w320/${iso}.png 2x`"
    :alt="name"
    :style="{
      width: `${widthValue}px`,
      height: `${size}px`,
      objectFit: 'cover',
      borderRadius,
      display: 'inline-block',
    }"
  />
</template>

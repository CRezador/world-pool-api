<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  value: number;
  accent?: string;
}>(), {
  accent: 'magenta',
});

const emit = defineEmits<{ (e: 'update:value', v: number): void }>();

const accentVar = computed(() => toneVar(props.accent));

function inc() { emit('update:value', Math.min(props.value + 1, 19)); }
function dec() { emit('update:value', Math.max(props.value - 1, 0)); }

const btnStyle = computed(() => ({
  width: '56px', height: '36px',
  background: 'var(--paper)', color: 'var(--ink)',
  border: '1.5px solid var(--ink)',
  boxShadow: `2px 2px 0 ${accentVar.value}`,
  fontSize: '22px', cursor: 'pointer', borderRadius: '3px',
}));

const numStyle = computed(() => ({
  width: '72px', height: '88px',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  background: 'var(--paper)', border: '2px solid var(--ink)',
  boxShadow: `4px 4px 0 ${accentVar.value}`,
  fontSize: '64px', lineHeight: 1, color: 'var(--ink)',
}));
</script>

<template>
  <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '8px' }">
    <button class="font-display press" :style="btnStyle" @click="inc">+</button>
    <div :key="value" class="font-display score-pulse" :style="numStyle">{{ value }}</div>
    <button class="font-display press" :style="btnStyle" @click="dec">−</button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  active: boolean;
}>();

defineEmits<{ (e: 'click'): void }>();

const style = computed(() => ({
  padding: '8px 12px',
  background: props.active ? 'var(--ink)' : 'var(--paper-2)',
  color: props.active ? 'var(--paper)' : 'var(--ink)',
  border: '1.5px solid var(--ink)',
  fontSize: '11px', letterSpacing: '0.12em', fontWeight: 700,
  cursor: 'pointer', borderRadius: '3px',
  whiteSpace: 'nowrap' as const,
  flexShrink: 0,
}));
</script>

<template>
  <button class="font-mono" :style="style" @click="$emit('click')"><slot /></button>
</template>

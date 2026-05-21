<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';

const props = withDefaults(defineProps<{
  tone?: string;
  full?: boolean;
  size?: 'sm' | 'md' | 'lg';
  disabled?: boolean;
}>(), {
  tone: 'ink',
  full: false,
  size: 'md',
  disabled: false,
});

const SIZES = {
  sm: { fs: '13px', py: '8px', px: '14px' },
  md: { fs: '16px', py: '14px', px: '22px' },
  lg: { fs: '20px', py: '18px', px: '26px' },
};

const style = computed(() => {
  const sz = SIZES[props.size];
  const fg = props.tone === 'lime' || props.tone === 'paper' ? 'var(--ink)' : 'var(--paper)';
  return {
    display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
    gap: '8px',
    fontSize: sz.fs, padding: `${sz.py} ${sz.px}`,
    width: props.full ? '100%' : undefined,
    background: toneVar(props.tone), color: fg,
    border: '1.5px solid var(--ink)',
    borderRadius: '4px',
    boxShadow: '4px 4px 0 var(--ink)',
    cursor: 'pointer', letterSpacing: '0.04em',
    textTransform: 'uppercase' as const,
    opacity: props.disabled ? 0.6 : 1,
  };
});

defineEmits<{ (e: 'click', event: MouseEvent): void }>();
</script>

<template>
  <button
    class="font-display press"
    :disabled="disabled"
    :style="style"
    @click="(e) => $emit('click', e)"
  >
    <slot />
  </button>
</template>

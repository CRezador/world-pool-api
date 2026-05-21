<script setup lang="ts">
import { useRouter } from 'vue-router';

withDefaults(defineProps<{
  title?: string | null;
  kicker?: string;
  compact?: boolean;
  fallback?: string;
}>(), {
  title: null,
  fallback: '/',
});

const router = useRouter();

function goBack(fallback: string) {
  if (window.history.length > 1) router.back();
  else router.push(fallback);
}
</script>

<template>
  <div :style="{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between' }">
    <button
      class="font-display press"
      :style="{
        background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
        boxShadow: '2px 2px 0 var(--ink)', cursor: 'pointer',
        padding: '6px 10px', fontSize: '14px', borderRadius: '3px',
      }"
      @click="goBack(fallback)"
    >← VOLTAR</button>
    <div :style="{ textAlign: 'right' }">
      <div
        v-if="kicker"
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }"
      >{{ kicker }}</div>
      <div
        v-if="title"
        class="font-display"
        :style="{
          fontSize: compact ? '18px' : '22px', lineHeight: 1, marginTop: '2px',
          textTransform: 'uppercase',
        }"
      >{{ title }}</div>
    </div>
  </div>
</template>

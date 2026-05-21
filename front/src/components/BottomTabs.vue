<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const tabs = [
  { id: 'home', icon: '◧', label: 'Início', path: '/' },
  { id: 'matches', icon: '⌖', label: 'Jogos', path: '/matches' },
  { id: 'pool', icon: '◉', label: 'Bolão', path: '/pools' },
  { id: 'standings', icon: '⊞', label: 'Tabela', path: '/standings' },
  { id: 'me', icon: '◐', label: 'Eu', path: '/' },
];

const active = computed(() => {
  const m = route.matched[0]?.name;
  if (m === 'home') return 'home';
  if (m === 'matches' || m === 'guess' || m === 'match-detail') return 'matches';
  if (m === 'my-pools' || m === 'pool' || m === 'explore') return 'pool';
  if (m === 'standings') return 'standings';
  return '';
});
</script>

<template>
  <div :style="{
    display: 'flex', justifyContent: 'space-around',
    padding: '8px 4px 22px',
    background: 'var(--paper)',
    borderTop: '1.5px solid var(--ink)',
    position: 'relative',
  }">
    <button
      v-for="t in tabs"
      :key="t.id"
      :style="{
        border: 'none', background: 'transparent', cursor: 'pointer',
        display: 'flex', flexDirection: 'column', alignItems: 'center',
        gap: '2px', padding: '6px 8px', minWidth: '56px',
        color: active === t.id ? 'var(--magenta)' : 'var(--ink)',
        position: 'relative',
      }"
      @click="router.push(t.path)"
    >
      <div class="font-display" :style="{ fontSize: '22px', lineHeight: 1 }">{{ t.icon }}</div>
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.1em', fontWeight: 700, textTransform: 'uppercase' }"
      >{{ t.label }}</div>
      <div
        v-if="active === t.id"
        :style="{
          position: 'absolute', bottom: '2px', height: '3px', width: '22px',
          background: 'var(--magenta)',
        }"
      />
    </button>
  </div>
</template>

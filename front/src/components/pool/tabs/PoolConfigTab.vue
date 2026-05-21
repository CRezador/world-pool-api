<script setup lang="ts">
import Stamp from '@/components/Stamp.vue';
import { toneVar } from '@/data/mock';

interface Row { icon: string; title: string; sub?: string; tone?: string; danger?: boolean }

const rows: Row[] = [
  { icon: '↻', title: 'Regenerar código de convite', sub: 'Atual: BARRES' },
  { icon: '✎', title: 'Editar nome e descrição' },
  { icon: '◐', title: 'Tornar bolão público', sub: 'Aparecerá na busca' },
  { icon: '⊘', title: 'Banir sócio' },
  { icon: '↺', title: 'Recalcular pontuação', sub: 'Admin do sistema', tone: 'cobalt' },
  { icon: '✕', title: 'Encerrar bolão', tone: 'magenta', danger: true },
];
</script>

<template>
  <div class="fade-up" :style="{ padding: '14px 18px 18px' }">
    <Stamp tone="magenta" :rotate="-3">ADMIN · APENAS DONOS</Stamp>

    <div :style="{ display: 'flex', flexDirection: 'column', gap: '10px', marginTop: '14px' }">
      <div
        v-for="(r, i) in rows"
        :key="i"
        :style="{
          display: 'flex', alignItems: 'center', gap: '14px',
          padding: '14px 14px',
          background: 'var(--paper-2)',
          border: '1.5px solid var(--ink)',
          borderRadius: '4px',
        }"
      >
        <div
          class="font-display"
          :style="{
            width: '36px', height: '36px',
            background: toneVar(r.tone || 'ink'),
            color: r.tone === 'lime' || r.tone === 'paper' ? 'var(--ink)' : 'var(--paper)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '18px',
          }"
        >{{ r.icon }}</div>
        <div :style="{ flex: 1 }">
          <div
            class="font-display"
            :style="{ fontSize: '14px', color: r.danger ? 'var(--magenta)' : 'var(--ink)' }"
          >{{ r.title }}</div>
          <div
            v-if="r.sub"
            class="font-mono"
            :style="{ fontSize: '10px', opacity: 0.7, marginTop: '2px' }"
          >{{ r.sub }}</div>
        </div>
        <span :style="{ opacity: 0.4 }">→</span>
      </div>
    </div>
  </div>
</template>

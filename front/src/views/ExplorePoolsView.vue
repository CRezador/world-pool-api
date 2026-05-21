<script setup lang="ts">
import { useRouter } from 'vue-router';
import BackBar from '@/components/BackBar.vue';
import { toneVar } from '@/data/mock';
import type { Tone } from '@/types';

const router = useRouter();

interface PublicPool { id: string; name: string; code: string; members: number; accent: Tone; tag: string }
const list: PublicPool[] = [
  { id: 'a', name: 'Geral Brasil 🇧🇷', code: 'BRASIL', members: 8431, accent: 'lime', tag: 'POPULAR' },
  { id: 'b', name: 'Hexa ou Choro', code: 'HEXACL', members: 2103, accent: 'magenta', tag: 'NOVO' },
  { id: 'c', name: 'Bar do Geninho', code: 'BARGEN', members: 412, accent: 'cobalt', tag: 'LOCAL' },
  { id: 'd', name: 'Engenheiros da Bola', code: 'ENGBOL', members: 184, accent: 'coral', tag: '' },
  { id: 'e', name: 'Mães de Copa', code: 'MAESCO', members: 698, accent: 'magenta', tag: 'NOVO' },
];

const fmt = new Intl.NumberFormat('pt-BR');
</script>

<template>
  <div :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '18px', paddingBottom: '8px' }">
      <BackBar title="Bolões públicos" kicker="EXPLORAR · 8.241 ABERTOS" fallback="/" />
      <div :style="{
        marginTop: '14px', padding: '10px 14px',
        background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
        display: 'flex', alignItems: 'center', gap: '8px',
      }">
        <span class="font-mono" :style="{ opacity: 0.6 }">⌕</span>
        <span class="font-mono" :style="{ fontSize: '13px', opacity: 0.6 }">buscar por nome ou código…</span>
      </div>
    </div>
    <div :style="{ padding: '6px 18px 18px', display: 'flex', flexDirection: 'column', gap: '12px' }">
      <div
        v-for="p in list"
        :key="p.id"
        :style="{
          display: 'flex', alignItems: 'center', gap: '12px',
          padding: '12px 14px',
          background: 'var(--paper-2)',
          border: '1.5px solid var(--ink)',
          boxShadow: `3px 3px 0 ${toneVar(p.accent)}`,
          borderRadius: '4px', cursor: 'pointer',
        }"
        @click="router.push('/pool/pool-resenha')"
      >
        <div
          class="font-display"
          :style="{
            width: '40px', height: '40px', background: toneVar(p.accent),
            border: '1.5px solid var(--ink)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
          }"
        >
          <span :style="{
            color: p.accent === 'lime' ? 'var(--ink)' : 'var(--paper)',
            fontSize: '18px', transform: 'rotate(-6deg)',
          }">{{ p.code.slice(0, 2) }}</span>
        </div>
        <div :style="{ flex: 1 }">
          <div class="font-display" :style="{ fontSize: '18px', lineHeight: 1 }">{{ p.name }}</div>
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.7, marginTop: '3px' }"
          >
            {{ p.code }} · {{ fmt.format(p.members) }} sócios
            <span
              v-if="p.tag"
              :style="{ marginLeft: '8px', padding: '1px 5px', background: 'var(--ink)', color: 'var(--paper)' }"
            >{{ p.tag }}</span>
          </div>
        </div>
        <span class="font-display" :style="{ fontSize: '14px' }">→</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { toneVar } from '@/data/mock';
import type { Pool } from '@/types';

const props = defineProps<{ pool: Pool }>();

const router = useRouter();

function goBack() {
  if (window.history.length > 1) router.back();
  else router.push('/pools');
}

const accentBg  = computed(() => toneVar(props.pool.accent));
const textColor = computed(() => props.pool.accent === 'lime' ? 'var(--ink)' : 'var(--paper)');
const btnBg     = computed(() => props.pool.accent === 'lime' ? 'rgba(0,0,0,0.18)' : 'rgba(255,255,255,0.18)');

const fmt = new Intl.NumberFormat('pt-BR');
</script>

<template>
  <div :style="{
    background: accentBg,
    borderBottom: '2px solid var(--ink)',
    padding: '12px 18px 16px',
    position: 'relative', overflow: 'hidden',
  }">
    <!-- halftone overlay -->
    <div
      class="halftone"
      :style="{
        position: 'absolute', top: 0, right: 0, width: '96px', height: '100%',
        color: 'var(--ink)', opacity: 0.12, pointerEvents: 'none',
      }"
    />

    <!-- back button -->
    <button
      class="font-display press"
      :style="{
        background: btnBg, color: textColor,
        border: 'none', cursor: 'pointer',
        padding: '5px 10px', fontSize: '13px', borderRadius: '3px',
        marginBottom: '10px', display: 'block',
      }"
      @click="goBack"
    >← VOLTAR</button>

    <!-- code / privacy / members -->
    <div
      class="font-mono"
      :style="{ fontSize: '9px', letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75, color: textColor }"
    >
      {{ pool.code }} · {{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }} · {{ fmt.format(pool.members) }} SÓCIOS
    </div>

    <!-- name -->
    <div
      class="font-display"
      :style="{ fontSize: '32px', lineHeight: 0.95, marginTop: '4px', textTransform: 'uppercase', color: textColor }"
    >{{ pool.name }}</div>

    <!-- mini stats -->
    <div :style="{ display: 'flex', gap: '20px', marginTop: '12px' }">
      <div v-for="s in [
        { l: 'SUA POS.', v: pool.myRank + 'º' },
        { l: 'PONTOS',   v: String(pool.myPoints) },
        { l: 'LÍDER',    v: pool.leader },
      ]" :key="s.l">
        <div class="font-mono" :style="{ fontSize: '8px', letterSpacing: '0.14em', opacity: 0.7, color: textColor }">{{ s.l }}</div>
        <div class="font-display" :style="{ fontSize: '18px', lineHeight: 1, color: textColor }">{{ s.v }}</div>
      </div>
    </div>
  </div>
</template>

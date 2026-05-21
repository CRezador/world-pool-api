<script setup lang="ts">
import BackBar from '@/components/BackBar.vue';
import type { Pool } from '@/types';

defineProps<{ pool: Pool }>();
</script>

<template>
  <div :style="{ padding: '14px 18px 10px', background: 'var(--paper)', borderBottom: '1.5px solid var(--ink)' }">
    <BackBar kicker="MEU BOLÃO" compact fallback="/pools" />

    <div :style="{ display: 'flex', alignItems: 'flex-end', gap: '12px', marginTop: '6px' }">
      <div :style="{ flex: 1 }">
        <div
          class="font-display"
          :style="{ fontSize: '32px', lineHeight: 0.95, textTransform: 'uppercase' }"
        >{{ pool.name }}</div>
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.14em', marginTop: '6px', opacity: 0.8 }"
        >
          {{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }} · {{ pool.members }} sócios
        </div>
      </div>
      <div
        class="font-mono"
        :style="{
          padding: '4px 8px', background: 'var(--ink)', color: 'var(--paper)',
          display: 'flex', alignItems: 'center', gap: '4px',
        }"
      >
        <span :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">CÓD</span>
        <span :style="{ fontSize: '13px', letterSpacing: '0.12em', fontWeight: 700 }">{{ pool.code }}</span>
        <span :style="{ marginLeft: '4px', fontSize: '11px' }">⎘</span>
      </div>
    </div>

    <div :style="{
      marginTop: '12px', padding: '10px 12px',
      background: 'var(--ink)', color: 'var(--paper)',
      display: 'flex', alignItems: 'center', gap: '10px', borderRadius: '3px',
    }">
      <div
        class="font-display"
        :style="{ fontSize: '24px', color: 'var(--lime)', minWidth: '36px', textAlign: 'center' }"
      >{{ pool.myRank }}º</div>
      <div :style="{ flex: 1 }">
        <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">SUA POSIÇÃO</div>
        <div class="font-display" :style="{ fontSize: '16px' }">
          {{ pool.myPoints }} <span :style="{ opacity: 0.5, fontSize: '11px' }">PTS / LÍDER {{ pool.leaderPoints }}</span>
        </div>
      </div>
      <div class="font-mono" :style="{ fontSize: '10px', color: 'var(--lime)' }">
        ▲ +2 hj
      </div>
    </div>
  </div>
</template>

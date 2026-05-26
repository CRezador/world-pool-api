<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useMatch } from '@/composables/useMatch';

const router = useRouter();
const { upcomingMatches, fetchUpcomingMatches } = useMatch();
onMounted(fetchUpcomingMatches);

const gameDay = computed(() => upcomingMatches.value[0]?.gameDay);
</script>

<template>
  <div :style="{
    padding: '14px', background: 'var(--ink)', color: 'var(--paper)',
    position: 'relative', overflow: 'hidden', marginBottom: '18px',
  }">
    <div :style="{ position: 'absolute', top: 0, right: 0, width: '90px', height: '28px', color: 'var(--coral)' }">
      <div class="halftone" :style="{ height: '100%' }" />
    </div>

    <div
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--coral)' }"
    >· RODADA {{ gameDay }} · {{ upcomingMatches.length }} JOGOS</div>
    <div
      class="font-display"
      :style="{ fontSize: '22px', lineHeight: 1, marginTop: '6px', textTransform: 'uppercase' }"
    >Falta apitar</div>

    <div :style="{ marginTop: '12px', display: 'flex', flexDirection: 'column', gap: '8px' }">
      <div
        v-for="m in upcomingMatches"
        :key="m.id"
        :style="{
          display: 'flex', alignItems: 'center', gap: '8px',
          padding: '8px 10px',
          background: 'rgba(242, 233, 210, 0.08)',
          border: '1px dashed rgba(242, 233, 210, 0.3)',
          cursor: 'pointer',
        }"
        @click="router.push(`/guess/${m.id}`)"
      >
        <img
          v-if="m.home.flag_url"
          :src="m.home.flag_url"
          :alt="m.home.code"
          :style="{ width: '24px', height: '16px', objectFit: 'cover', borderRadius: '2px' }"
        />
        <span class="font-display" :style="{ fontSize: '14px' }">
          {{ m.home.code }} × {{ m.away.code }}
        </span>
        <img
          v-if="m.away.flag_url"
          :src="m.away.flag_url"
          :alt="m.away.code"
          :style="{ width: '24px', height: '16px', objectFit: 'cover', borderRadius: '2px' }"
        />
        <span
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.7, marginLeft: '4px' }"
        >{{ m.kickoff ?? '—' }}</span>
        <span
          class="font-display"
          :style="{ fontSize: '12px', color: 'var(--lime)', marginLeft: 'auto' }"
        >APITAR →</span>
      </div>

      <div
        v-if="upcomingMatches.length === 0"
        class="font-mono"
        :style="{ fontSize: '11px', letterSpacing: '0.12em', opacity: 0.6, padding: '8px 0' }"
      >NENHUM JOGO PENDENTE</div>
    </div>
  </div>
</template>

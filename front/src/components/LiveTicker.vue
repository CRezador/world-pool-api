<script setup lang="ts">
import { useRouter } from 'vue-router';
import FlagImg from './FlagImg.vue';
import { TEAMS } from '@/data/mock';
import type { Match } from '@/types';

const props = defineProps<{ match: Match }>();
const router = useRouter();

const home = TEAMS[props.match.home];
const away = TEAMS[props.match.away];
</script>

<template>
  <div
    :style="{
      background: 'var(--ink)', color: 'var(--paper)',
      padding: '10px 18px',
      display: 'flex', alignItems: 'center', gap: '10px',
      cursor: 'pointer',
    }"
    @click="router.push(`/match/${match.id}`)"
  >
    <span
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, color: 'var(--coral)' }"
    >
      <span
        class="live-dot"
        :style="{
          display: 'inline-block', width: '6px', height: '6px', borderRadius: '50%',
          background: 'var(--coral)', marginRight: '6px',
        }"
      />AO VIVO
    </span>
    <FlagImg :team="match.home" :size="20" :radius="2" />
    <span class="font-display" :style="{ fontSize: '18px' }">
      {{ home.code }} {{ match.homeScore }} × {{ match.awayScore }} {{ away.code }}
    </span>
    <FlagImg :team="match.away" :size="20" :radius="2" />
    <span class="font-mono" :style="{ fontSize: '11px', marginLeft: 'auto' }">{{ match.kickoff }}</span>
  </div>
</template>

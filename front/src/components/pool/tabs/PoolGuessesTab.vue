<script setup lang="ts">
import { useRouter } from 'vue-router';
import FlagImg from '@/components/FlagImg.vue';
import StageBadge from '@/components/StageBadge.vue';
import StatusPill from '@/components/StatusPill.vue';
import { MATCHES, TEAMS } from '@/data/mock';
import type { Match } from '@/types';

const router = useRouter();

function myGuess(m: Match) {
  return m.id === 1 ? { home: 2, away: 1, points: 3 }
    : m.id === 2 ? { home: 1, away: 1, points: 0 }
    : m.id === 3 ? { home: 2, away: 0, points: 1 }
    : { home: 2, away: 0, points: undefined as number | undefined };
}

function goTo(m: Match) {
  router.push(m.status === 'SCHEDULED' ? `/guess/${m.id}` : `/match/${m.id}`);
}
</script>

<template>
  <div
    class="fade-up"
    :style="{ padding: '14px 18px 18px', display: 'flex', flexDirection: 'column', gap: '12px' }"
  >
    <span
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700 }"
    >SEUS PALPITES · ÚLTIMOS 4 JOGOS</span>

    <div
      v-for="m in MATCHES.slice(0, 4)"
      :key="m.id"
      :style="{
        background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
        padding: '12px', borderRadius: '4px', cursor: 'pointer',
        boxShadow: m.status === 'SCHEDULED' ? '3px 3px 0 var(--ink)' : 'none',
      }"
      @click="goTo(m)"
    >
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' }">
        <StageBadge
          :stage="m.stage"
          :group="m.group"
          :tone="m.status === 'IN_PROGRESS' ? 'coral' : 'cobalt'"
        />
        <StatusPill :status="m.status" :points="myGuess(m).points" />
      </div>
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <FlagImg :team="m.home" :size="20" :radius="2" />
        <div class="font-display" :style="{ fontSize: '16px', flex: 1 }">
          {{ TEAMS[m.home].code }} × {{ TEAMS[m.away].code }}
        </div>
        <FlagImg :team="m.away" :size="20" :radius="2" />
      </div>
      <div :style="{
        display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end',
        marginTop: '10px', paddingTop: '10px', borderTop: '1px dashed var(--ink)',
      }">
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">
            MEU PALPITE
          </div>
          <div class="font-display" :style="{ fontSize: '22px' }">
            {{ myGuess(m).home }} × {{ myGuess(m).away }}
          </div>
        </div>
        <div v-if="m.status !== 'SCHEDULED'" :style="{ textAlign: 'right' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">
            RESULTADO REAL
          </div>
          <div class="font-display" :style="{ fontSize: '22px' }">
            {{ m.homeScore }} × {{ m.awayScore }}
          </div>
        </div>
        <span
          v-else
          class="font-mono"
          :style="{
            fontSize: '11px', padding: '4px 8px',
            border: '1.5px solid var(--ink)', borderRadius: '3px', letterSpacing: '0.1em',
          }"
        >EDITAR ↗</span>
      </div>
    </div>
  </div>
</template>

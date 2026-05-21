<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import SectionHead from '@/components/SectionHead.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import MatchListCard from '@/components/match/MatchListCard.vue';
import DesktopMatchesBoard from '@/components/match/DesktopMatchesBoard.vue';
import { MATCHES } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';

const router = useRouter();
const { isDesktop } = useBreakpoint();

const filters = ['TODOS', 'GRUPOS', 'OITAVAS', 'QUARTAS', 'SEMI', 'FINAL'];
const groups = ['A','B','C','D','E','F','G'];
const filter = ref('TODOS');
const group = ref<string | null>(null);

const visibleMatches = computed(() =>
  MATCHES.filter(m => !group.value || m.group === group.value),
);

function open(m: typeof MATCHES[number]) {
  router.push(m.status === 'SCHEDULED' ? `/guess/${m.id}` : `/match/${m.id}`);
}
</script>

<template>
  <DesktopMatchesBoard v-if="isDesktop" />
  <div v-else :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 12px', borderBottom: '1.5px solid var(--ink)' }">
      <SectionHead kicker="CALENDÁRIO · COPA 26" title="Tabela de jogos" />
    </div>

    <div
      class="no-scrollbar"
      :style="{
        display: 'flex', gap: '6px', padding: '12px 18px',
        overflowX: 'auto', borderBottom: '1.5px solid var(--ink)',
      }"
    >
      <ChipToggle
        v-for="f in filters"
        :key="f"
        :active="filter === f"
        @click="filter = f"
      >{{ f }}</ChipToggle>
    </div>
    <div
      v-if="filter === 'GRUPOS'"
      class="no-scrollbar"
      :style="{
        display: 'flex', gap: '6px', padding: '8px 18px',
        overflowX: 'auto', borderBottom: '1.5px solid var(--ink)',
      }"
    >
      <button
        v-for="g in groups"
        :key="g"
        class="font-display"
        :style="{
          minWidth: '36px', height: '36px',
          background: group === g ? 'var(--cobalt)' : 'var(--paper-2)',
          color: group === g ? 'var(--paper)' : 'var(--ink)',
          border: '1.5px solid var(--ink)',
          fontSize: '16px', cursor: 'pointer', borderRadius: '3px',
        }"
        @click="group = g === group ? null : g"
      >{{ g }}</button>
    </div>

    <div :style="{ padding: '14px 18px', display: 'flex', flexDirection: 'column', gap: '14px' }">
      <MatchListCard
        v-for="m in visibleMatches"
        :key="m.id"
        :match="m"
        @click="open(m)"
      />
    </div>
  </div>
</template>

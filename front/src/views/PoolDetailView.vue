<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import PoolHeader from '@/components/pool/PoolHeader.vue';
import PoolRankTab from '@/components/pool/tabs/PoolRankTab.vue';
import PoolMembersTab from '@/components/pool/tabs/PoolMembersTab.vue';
import PoolGuessesTab from '@/components/pool/tabs/PoolGuessesTab.vue';
import PoolConfigTab from '@/components/pool/tabs/PoolConfigTab.vue';
import RefreshIndicator from '@/components/RefreshIndicator.vue';
import DesktopPoolDashboard from '@/components/pool/DesktopPoolDashboard.vue';
import { usePools } from '@/composables/usePools';
import { useBreakpoint } from '@/composables/useBreakpoint';

const route = useRoute();
const { isDesktop } = useBreakpoint();
const { currentPool: pool, fetchPoolById } = usePools();

onMounted(() => fetchPoolById(route.params.id as string));

const tab = ref<'rank' | 'members' | 'guesses' | 'config'>('rank');
const refreshing = ref(false);

const tabs = [
  { id: 'rank', label: 'RANKING' },
  { id: 'members', label: 'SÓCIOS' },
  { id: 'guesses', label: 'PALPITES' },
  { id: 'config', label: 'CONFIG' },
] as const;

function onRefresh() {
  refreshing.value = true;
  setTimeout(() => { refreshing.value = false; }, 1200);
}
</script>

<template>
  <DesktopPoolDashboard v-if="isDesktop && pool" :pool="pool" />
  <div v-else-if="pool" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <PoolHeader :pool="pool" />

    <div :style="{
      display: 'flex', borderBottom: '1.5px solid var(--ink)',
      background: 'var(--paper)',
      position: 'sticky', top: 0, zIndex: 2,
    }">
      <button
        v-for="t in tabs"
        :key="t.id"
        class="font-display"
        :style="{
          flex: 1, padding: '12px 4px', border: 'none', background: 'transparent',
          fontSize: '12px', letterSpacing: '0.08em', cursor: 'pointer',
          borderBottom: tab === t.id ? '3px solid var(--magenta)' : '3px solid transparent',
          color: tab === t.id ? 'var(--magenta)' : 'var(--ink)',
          transition: 'all 0.18s ease',
        }"
        @click="tab = t.id"
      >{{ t.label }}</button>
    </div>

    <RefreshIndicator :visible="refreshing" />

    <PoolRankTab v-if="tab === 'rank'" @refresh="onRefresh" />
    <PoolMembersTab v-else-if="tab === 'members'" />
    <PoolGuessesTab v-else-if="tab === 'guesses'" />
    <PoolConfigTab v-else-if="tab === 'config'" />
  </div>
</template>

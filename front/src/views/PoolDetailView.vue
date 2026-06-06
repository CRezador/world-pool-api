<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import PoolHeader from '@/components/pool/PoolHeader.vue';
import PoolRankTab from '@/components/pool/tabs/PoolRankTab.vue';
import PoolMembersTab from '@/components/pool/tabs/PoolMembersTab.vue';
import PoolConfigTab from '@/components/pool/tabs/PoolConfigTab.vue';
import RefreshIndicator from '@/components/RefreshIndicator.vue';
import DesktopPoolDashboard from '@/components/pool/DesktopPoolDashboard.vue';
import PoolLoadingSkeleton from '@/components/pool/PoolLoadingSkeleton.vue';
import { usePools, fetchMyMembership } from '@/composables/usePools';
import { useBreakpoint } from '@/composables/useBreakpoint';

type Role = 'OWNER' | 'ADMIN' | 'MEMBER';

const route = useRoute();
const { isDesktop } = useBreakpoint();
const { currentPool: pool, fetchPoolById } = usePools();

const loading  = ref(true);
const myRole   = ref<Role>('MEMBER');
const joinedAt = ref('');

async function loadPool(id: string) {
  loading.value = true;
  try {
    const [, membership] = await Promise.all([
      fetchPoolById(id),
      fetchMyMembership(id),
    ]);
    myRole.value   = membership.role;
    joinedAt.value = membership.joinedAt;
  } finally {
    loading.value = false;
  }
}

onMounted(() => loadPool(route.params.id as string));
watch(() => route.params.id, (id) => loadPool(id as string));

type TabId = 'rank' | 'members' | 'config';

const tabs = computed<{ id: TabId; label: string }[]>(() => [
  { id: 'rank',    label: 'RANKING' },
  { id: 'members', label: 'SÓCIOS'  },
  { id: 'config',  label: 'CONFIG'  },
]);

const tab = ref<TabId>('rank');
const refreshing = ref(false);

function onRefresh() {
  refreshing.value = true;
  setTimeout(() => { refreshing.value = false; }, 1200);
}
</script>

<template>
  <!-- Desktop -->
  <DesktopPoolDashboard v-if="isDesktop && pool" :pool="pool" :my-role="myRole" :joined-at="joinedAt" />

  <!-- Mobile skeleton -->
  <PoolLoadingSkeleton v-else-if="loading" />

  <!-- Mobile -->
  <div v-else-if="pool" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <PoolHeader :pool="pool" />

    <div :style="{
      display: 'flex', borderBottom: '1.5px solid var(--ink)',
      background: 'var(--paper-2)',
      position: 'sticky', top: 0, zIndex: 2,
    }">
      <button
        v-for="t in tabs"
        :key="t.id"
        class="font-mono press"
        :style="{
          flex: 1, padding: '11px 4px',
          border: 'none', borderRight: '1px dashed var(--ink)',
          fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, cursor: 'pointer',
          background: tab === t.id ? 'var(--ink)' : 'transparent',
          color: tab === t.id ? 'var(--paper)' : 'var(--ink)',
          borderBottom: tab === t.id ? '2px solid var(--magenta)' : '2px solid transparent',
          transition: 'background 0.14s, color 0.14s',
        }"
        @click="tab = t.id"
      >{{ t.label }}</button>
    </div>

    <RefreshIndicator :visible="refreshing" />

    <PoolRankTab    v-if="tab === 'rank'"    :pool-id="pool.id" @refresh="onRefresh" />
    <PoolMembersTab v-else-if="tab === 'members'" :pool-id="pool.id" />
    <PoolConfigTab  v-else-if="tab === 'config'"
      :pool="pool"
      :my-role="myRole"
      :joined-at="joinedAt"
    />
  </div>
</template>

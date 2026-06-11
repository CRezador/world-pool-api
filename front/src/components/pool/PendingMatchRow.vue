<script setup lang="ts">
import { useRouter } from 'vue-router';
import type { ApiMatch } from '@/types';

const props = defineProps<{
  match: ApiMatch;
  badge: string;
}>();

const router = useRouter();

function open() {
  if (props.match.groupId != null) {
    router.push(`/matches/${props.match.groupId}`);
  } else {
    router.push({ path: '/matches', query: { phase: 'knockout' } });
  }
}
</script>

<template>
  <div
    :style="{
      display: 'flex', alignItems: 'center', gap: '8px',
      padding: '8px 10px',
      background: 'rgba(242, 233, 210, 0.08)',
      border: '1px dashed rgba(242, 233, 210, 0.3)',
      cursor: 'pointer',
    }"
    @click="open()"
  >
    <span
      class="font-mono"
      :style="{
        fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700,
        padding: '2px 5px',
        background: 'var(--coral)', color: 'var(--paper)',
        borderRadius: '2px', flexShrink: 0,
      }"
    >{{ badge }}</span>

    <img
      v-if="match.home.flag_url"
      :src="match.home.flag_url"
      :alt="match.home.code"
      :style="{ width: '20px', height: '14px', objectFit: 'cover', borderRadius: '2px', flexShrink: 0 }"
    />

    <span class="font-display" :style="{ fontSize: '13px', whiteSpace: 'nowrap' }">
      {{ match.home.code }} × {{ match.away.code }}
    </span>

    <img
      v-if="match.away.flag_url"
      :src="match.away.flag_url"
      :alt="match.away.code"
      :style="{ width: '20px', height: '14px', objectFit: 'cover', borderRadius: '2px', flexShrink: 0 }"
    />

    <span
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.7, marginLeft: '4px' }"
    >{{ match.kickoff ?? '—' }}</span>

    <span
      class="font-display"
      :style="{ fontSize: '12px', color: 'var(--lime)', marginLeft: 'auto' }"
    >APITAR →</span>
  </div>
</template>

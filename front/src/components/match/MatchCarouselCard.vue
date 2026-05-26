<script setup lang="ts">
import { useRouter } from 'vue-router';
import StageBadge from '@/components/StageBadge.vue';
import type { ApiMatch } from '@/types';

const props = defineProps<{ match: ApiMatch }>();
const router = useRouter();
</script>

<template>
  <div
    :style="{
      flexShrink: 0,
      width: 'calc(100vw - 54px)',
      maxWidth: '360px',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: '4px 4px 0 var(--cobalt), 4px 4px 0 1px var(--ink)',
      borderRadius: '6px',
      overflow: 'hidden',
      cursor: 'pointer',
      scrollSnapAlign: 'start',
    }"
    @click="router.push(`/guess/${match.id}`)"
  >
    <div :style="{
      padding: '8px 12px',
      background: 'var(--ink)', color: 'var(--paper)',
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
    }">
      <StageBadge :stage="match.stage" :group="match.group ?? undefined" tone="cobalt" />
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
        {{ match.kickoff ?? '—' }}
      </span>
    </div>

    <div :style="{ padding: '20px 16px', display: 'flex', alignItems: 'center', gap: '12px' }">
      <div :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '8px' }">
        <img
          v-if="match.home.flag_url"
          :src="match.home.flag_url"
          :alt="match.home.name"
          :style="{
            width: '72px', height: '48px',
            objectFit: 'cover', borderRadius: '4px',
            border: '1.5px solid var(--ink)',
          }"
        />
        <span class="font-display" :style="{ fontSize: '20px' }">{{ match.home.code }}</span>
      </div>

      <span class="font-display" :style="{ fontSize: '24px', opacity: 0.4, flexShrink: 0 }">×</span>

      <div :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '8px' }">
        <img
          v-if="match.away.flag_url"
          :src="match.away.flag_url"
          :alt="match.away.name"
          :style="{
            width: '72px', height: '48px',
            objectFit: 'cover', borderRadius: '4px',
            border: '1.5px solid var(--ink)',
          }"
        />
        <span class="font-display" :style="{ fontSize: '20px' }">{{ match.away.code }}</span>
      </div>
    </div>

    <div :style="{
      padding: '10px 12px',
      background: 'var(--ink)', color: 'var(--paper)',
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
    }">
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">SEU PALPITE · —</span>
      <span class="font-display" :style="{ fontSize: '12px', color: 'var(--lime)' }">PALPITAR →</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import FlagChip from '@/components/FlagChip.vue';
import StageBadge from '@/components/StageBadge.vue';
import { TEAMS } from '@/data/mock';
import type { Match } from '@/types';

const props = defineProps<{ match: Match }>();
const router = useRouter();

const home = computed(() => TEAMS[props.match.home]);
const away = computed(() => TEAMS[props.match.away]);
</script>

<template>
  <div
    :style="{
      position: 'relative', cursor: 'pointer',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: '4px 4px 0 var(--cobalt), 4px 4px 0 1px var(--ink)',
      borderRadius: '6px', padding: '16px',
    }"
    @click="router.push(`/guess/${match.id}`)"
  >
    <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '14px' }">
      <StageBadge :stage="match.stage" :group="match.group" tone="cobalt" />
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
        {{ match.day.toUpperCase() }} · {{ match.kickoff }}
      </span>
    </div>
    <div :style="{ display: 'flex', alignItems: 'center', gap: '14px' }">
      <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
        <FlagChip :team="match.home" :size="48" tone="cobalt" />
        <div class="font-display" :style="{ fontSize: '18px', marginTop: '6px' }">{{ home.code }}</div>
        <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.7 }">
          {{ home.name.toUpperCase() }}
        </div>
      </div>
      <div class="font-display" :style="{ fontSize: '28px', opacity: 0.6 }">×</div>
      <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
        <FlagChip :team="match.away" :size="48" tone="coral" />
        <div class="font-display" :style="{ fontSize: '18px', marginTop: '6px' }">{{ away.code }}</div>
        <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.7 }">
          {{ away.name.toUpperCase() }}
        </div>
      </div>
    </div>
    <div :style="{
      marginTop: '14px', padding: '10px 12px',
      background: 'var(--ink)', color: 'var(--paper)',
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      borderRadius: '3px',
    }">
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
        PALPITAR ANTES DAS {{ match.kickoff }}
      </span>
      <span class="font-display" :style="{ fontSize: '14px', color: 'var(--lime)' }">→ APITAR</span>
    </div>
  </div>
</template>

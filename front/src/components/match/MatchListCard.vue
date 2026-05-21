<script setup lang="ts">
import { computed } from 'vue';
import FlagChip from '@/components/FlagChip.vue';
import StatusPill from '@/components/StatusPill.vue';
import { TEAMS, toneVar } from '@/data/mock';
import type { Match } from '@/types';

const props = defineProps<{ match: Match }>();
defineEmits<{ (e: 'click'): void }>();

const home = computed(() => TEAMS[props.match.home]);
const away = computed(() => TEAMS[props.match.away]);

const accent = computed(() =>
  props.match.status === 'IN_PROGRESS' ? 'coral' :
  props.match.status === 'FINISHED' ? 'cobalt' : 'magenta',
);

const accentVar = computed(() => toneVar(accent.value));

const myGuess = computed(() =>
  props.match.id === 1 ? '2-1 · +3' :
  props.match.id === 2 ? '1-1 · 0' :
  props.match.id === 3 ? '2-0 · ao vivo' : null,
);
</script>

<template>
  <div
    class="perf-bottom"
    :style="{
      position: 'relative', cursor: 'pointer',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
      borderRadius: '4px',
    }"
    @click="$emit('click')"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px',
      background: 'var(--ink)', color: 'var(--paper)',
    }">
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }"
      >{{ match.day.toUpperCase() }} · {{ match.kickoff }}</div>
      <StatusPill :status="match.status" />
    </div>
    <div :style="{ padding: '14px 14px 12px' }">
      <div :style="{ display: 'flex', alignItems: 'center', gap: '14px' }">
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px' }">
          <FlagChip :team="match.home" :size="36" :tone="accent" />
          <div>
            <div class="font-display" :style="{ fontSize: '18px', lineHeight: 1 }">{{ home.code }}</div>
            <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.08em' }">
              {{ home.name.toUpperCase() }}
            </div>
          </div>
        </div>
        <div
          class="font-display"
          :style="{
            fontSize: match.status === 'SCHEDULED' ? '18px' : '28px',
            color: match.status === 'SCHEDULED' ? 'var(--ink)' : accentVar,
            minWidth: '60px', textAlign: 'center',
          }"
        >{{ match.status === 'SCHEDULED' ? 'VS' : `${match.homeScore} × ${match.awayScore}` }}</div>
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px', justifyContent: 'flex-end' }">
          <div :style="{ textAlign: 'right' }">
            <div class="font-display" :style="{ fontSize: '18px', lineHeight: 1 }">{{ away.code }}</div>
            <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.08em' }">
              {{ away.name.toUpperCase() }}
            </div>
          </div>
          <FlagChip :team="match.away" :size="36" :tone="accent" />
        </div>
      </div>
      <div
        class="font-mono"
        :style="{ fontSize: '9px', opacity: 0.6, marginTop: '10px', letterSpacing: '0.1em' }"
      >📍 {{ match.venue.toUpperCase() }}</div>
    </div>
    <div :style="{
      padding: '8px 12px',
      borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      background: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
        {{ match.status === 'SCHEDULED' ? 'SEU PALPITE · 2-0' : `MEU PALPITE · ${myGuess || '—'}` }}
      </span>
      <span class="font-display" :style="{ fontSize: '12px', color: accentVar }">
        {{ match.status === 'SCHEDULED' ? 'PALPITAR →' : 'VER PALPITES →' }}
      </span>
    </div>
  </div>
</template>

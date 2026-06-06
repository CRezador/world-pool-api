<script setup lang="ts">
import { computed } from 'vue';
import FlagChip from '@/components/FlagChip.vue';
import StageBadge from '@/components/StageBadge.vue';
import { toneVar } from '@/utils/tone';
import { formatKickoffShort } from '@/utils/date';
import { useGuesses } from '@/composables/useGuesses';
import type { ApiMatch } from '@/types';

const props = defineProps<{ match: ApiMatch }>();
defineEmits<{ (e: 'click'): void }>();

const { guesses } = useGuesses();

const isTbd = computed(() => !props.match.home?.code || !props.match.away?.code);

const accent = computed(() =>
  props.match.status === 'IN_PROGRESS' ? 'coral' :
  props.match.status === 'FINISHED'    ? 'cobalt' : 'magenta',
);

const accentVar = computed(() => toneVar(accent.value));

const myGuess = computed(() => guesses.value.find(g => g.matchId === props.match.id) ?? null);

const kickoffLabel = computed(() => formatKickoffShort(props.match.kickoff));

const guessLabel = computed(() => {
  if (props.match.status !== 'SCHEDULED') return myGuess.value ? `MEU PALPITE · ${myGuess.value.homeScore}×${myGuess.value.awayScore}` : '—';
  return myGuess.value ? `MEU PALPITE: ${myGuess.value.homeScore}×${myGuess.value.awayScore}` : 'SEM PALPITE';
});
</script>

<template>
  <!-- TBD — times não definidos -->
  <div
    v-if="isTbd"
    class="perf-bottom"
    :style="{
      background: 'var(--paper-2)', border: '1.5px dashed var(--ink)',
      boxShadow: '3px 3px 0 rgba(0,0,0,0.1), 3px 3px 0 1px var(--ink)', borderRadius: '4px',
    }"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }">
        {{ kickoffLabel }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group ?? undefined" tone="cobalt" />
    </div>
    <div :style="{ padding: '16px', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '16px' }">
      <div :style="{ width: '32px', height: '32px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.3 }" />
      <span class="font-display" :style="{ fontSize: '18px', opacity: 0.3 }">×</span>
      <div :style="{ width: '32px', height: '32px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.3 }" />
    </div>
    <div :style="{
      padding: '7px 12px', borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.55 }">AGUARDANDO CLASSIFICADOS</span>
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', fontWeight: 700 }">TBD</span>
    </div>
  </div>

  <!-- Partida normal -->
  <div
    v-else
    class="perf-bottom"
    :style="{
      cursor: 'pointer', background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
      borderRadius: '4px',
    }"
    @click="$emit('click')"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }">
        {{ kickoffLabel }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group ?? undefined" :tone="accent" />
    </div>

    <div :style="{ padding: '12px 14px' }">
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <!-- Home -->
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px' }">
          <FlagChip :flagCode="match.home.flag_code" :teamName="match.home.name" :teamCode="match.home.code" :size="36" :tone="accent" />
          <div>
            <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">{{ match.home.code }}</div>
            <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.06em' }">
              {{ match.home.name.toUpperCase() }}
            </div>
          </div>
        </div>

        <!-- Placar / VS -->
        <div
          class="font-display"
          :style="{
            fontSize: match.status === 'SCHEDULED' ? '16px' : '26px',
            color: match.status === 'SCHEDULED' ? 'var(--ink)' : accentVar,
            minWidth: '52px', textAlign: 'center', opacity: match.status === 'SCHEDULED' ? 0.4 : 1,
          }"
        >{{ match.status === 'SCHEDULED' ? 'VS' : `${match.homeScore ?? 0} × ${match.awayScore ?? 0}` }}</div>

        <!-- Away -->
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px', justifyContent: 'flex-end' }">
          <div :style="{ textAlign: 'right' }">
            <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">{{ match.away.code }}</div>
            <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.06em' }">
              {{ match.away.name.toUpperCase() }}
            </div>
          </div>
          <FlagChip :flagCode="match.away.flag_code" :teamName="match.away.name" :teamCode="match.away.code" :size="36" :tone="accent" />
        </div>
      </div>
    </div>

    <div :style="{
      padding: '7px 12px', borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      background: 'var(--paper)',
    }">
      <span
        class="font-mono"
        :style="{
          fontSize: '10px', letterSpacing: '0.1em',
          color: myGuess && match.status === 'SCHEDULED' ? accentVar : 'inherit',
          opacity: myGuess ? 1 : 0.55,
        }"
      >{{ guessLabel }}</span>
      <span class="font-display" :style="{ fontSize: '11px', color: accentVar }">
        {{ match.status === 'SCHEDULED' ? (myGuess ? 'EDITAR →' : 'PALPITAR →') : 'VER →' }}
      </span>
    </div>
  </div>
</template>

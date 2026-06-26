<script setup lang="ts">
import { computed } from 'vue';
import FlagImg from '@/components/FlagImg.vue';
import StageBadge from '@/components/StageBadge.vue';
import { toneVar } from '@/utils/tone';
import { useGuesses } from '@/composables/useGuesses';
import { TBD_TEAM_CODE, type ApiMatch } from '@/types';

const props = defineProps<{ match: ApiMatch; accent: string }>();
defineEmits<{ (e: 'palpitar'): void }>();

const { guesses } = useGuesses();

const accentVar = computed(() => toneVar(props.accent));

const homeTbd = computed(() => props.match.home?.code === TBD_TEAM_CODE);
const awayTbd = computed(() => props.match.away?.code === TBD_TEAM_CODE);
// Enquanto qualquer lado não estiver definido, a partida não aceita palpite.
const isTbd = computed(() => homeTbd.value || awayTbd.value);

const myGuess = computed(() => guesses.value.find(g => g.matchId === props.match.id) ?? null);

const myGuessLabel = computed(() => {
  if (props.match.status === 'IN_PROGRESS') return 'EM PROGRESSO';
  if (props.match.status === 'FINISHED') return 'ENCERRADO';
  if (myGuess.value) return `MEU PALPITE: ${myGuess.value.homeScore} × ${myGuess.value.awayScore}`;
  return 'SEM PALPITE AINDA';
});
</script>

<template>
  <div
    class="perf-bottom"
    :style="{
      background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
      boxShadow: `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
    }"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">
        {{ match.kickoff ? match.kickoff.toUpperCase() : 'DATA A DEFINIR' }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group ?? undefined" :tone="accent" />
    </div>
    <div :style="{ padding: '12px' }">
      <!-- Mandante -->
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '8px' }">
        <template v-if="homeTbd">
          <div :style="{ width: '20px', height: '20px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.35 }" />
          <span class="font-display" :style="{ fontSize: '16px', flex: 1, opacity: 0.4 }">A definir</span>
        </template>
        <template v-else>
          <FlagImg :flagCode="match.home.flag_code" :teamName="match.home.name" :size="20" :radius="2" />
          <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ match.home.name }}</span>
          <span
            v-if="match.status !== 'SCHEDULED'"
            class="font-display"
            :style="{ fontSize: '22px' }"
          >{{ match.homeScore ?? 0 }}</span>
        </template>
      </div>
      <!-- Visitante -->
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <template v-if="awayTbd">
          <div :style="{ width: '20px', height: '20px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.35 }" />
          <span class="font-display" :style="{ fontSize: '16px', flex: 1, opacity: 0.4 }">A definir</span>
        </template>
        <template v-else>
          <FlagImg :flagCode="match.away.flag_code" :teamName="match.away.name" :size="20" :radius="2" />
          <span class="font-display" :style="{ fontSize: '16px', flex: 1 }">{{ match.away.name }}</span>
          <span
            v-if="match.status !== 'SCHEDULED'"
            class="font-display"
            :style="{ fontSize: '22px' }"
          >{{ match.awayScore ?? 0 }}</span>
        </template>
      </div>
    </div>
    <div :style="{
      padding: '8px 12px', borderTop: '1px dashed var(--ink)',
      display: 'flex', justifyContent: 'space-between', alignItems: 'center', gap: '8px',
    }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: isTbd ? 0.5 : (match.status === 'SCHEDULED' ? 0.7 : 1) }"
      >{{ isTbd ? 'AGUARDANDO CLASSIFICADOS' : myGuessLabel }}</span>
      <span
        v-if="isTbd"
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.1em', fontWeight: 700, opacity: 0.5 }"
      >TBD</span>
      <button
        v-else-if="match.status === 'SCHEDULED'"
        class="font-display press"
        :style="{
          background: myGuess ? 'var(--paper-2)' : 'var(--magenta)',
          color: myGuess ? 'var(--ink)' : 'var(--paper)',
          border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
          padding: '5px 12px', fontSize: '12px', letterSpacing: '0.06em',
          textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          display: 'inline-flex', alignItems: 'center', gap: '6px',
        }"
        @click.stop="$emit('palpitar')"
      >
        <span :style="{ fontSize: '13px' }">✎</span>
        {{ myGuess ? 'Editar' : 'Palpitar' }}
      </button>
      <span
        v-else
        class="font-display"
        :style="{ fontSize: '12px', color: accentVar }"
      >→</span>
    </div>
  </div>
</template>

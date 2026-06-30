<script setup lang="ts">
import { computed } from 'vue';
import FlagChip from '@/components/FlagChip.vue';
import StageBadge from '@/components/StageBadge.vue';
import { toneVar } from '@/utils/tone';
import { formatKickoffShort } from '@/utils/date';
import { useGuesses } from '@/composables/useGuesses';
import { formatScoreWithPenalties } from '@/utils/score';
import { TBD_TEAM_CODE, type ApiMatch } from '@/types';

const props = defineProps<{ match: ApiMatch }>();
defineEmits<{ (e: 'click'): void }>();

const { guesses } = useGuesses();

const homeTbd = computed(() => props.match.home?.code === TBD_TEAM_CODE);
const awayTbd = computed(() => props.match.away?.code === TBD_TEAM_CODE);
// Enquanto qualquer lado não estiver definido, a partida não aceita palpite.
const isTbd = computed(() => homeTbd.value || awayTbd.value);

const accent = computed(() =>
  props.match.status === 'IN_PROGRESS' ? 'coral' :
  props.match.status === 'FINISHED'    ? 'cobalt' : 'magenta',
);

const accentVar = computed(() => toneVar(accent.value));

const myGuess = computed(() => guesses.value.find(g => g.matchId === props.match.id) ?? null);

const hasPenalties = computed(() =>
  props.match.homePenalties != null && props.match.awayPenalties != null,
);
const scoreLabel = computed(() =>
  props.match.status === 'SCHEDULED'
    ? 'VS'
    : formatScoreWithPenalties(props.match.homeScore, props.match.awayScore, props.match.homePenalties, props.match.awayPenalties),
);
const isHomeWinner = computed(() => props.match.winnerTeamId != null && props.match.winnerTeamId === props.match.home?.id);
const isAwayWinner = computed(() => props.match.winnerTeamId != null && props.match.winnerTeamId === props.match.away?.id);

const kickoffLabel = computed(() => formatKickoffShort(props.match.kickoff));

const guessLabel = computed(() => {
  if (props.match.status !== 'SCHEDULED') return myGuess.value ? `MEU PALPITE · ${myGuess.value.homeScore}×${myGuess.value.awayScore}` : '—';
  return myGuess.value ? `MEU PALPITE: ${myGuess.value.homeScore}×${myGuess.value.awayScore}` : 'SEM PALPITE';
});
</script>

<template>
  <div
    class="perf-bottom"
    :style="{
      cursor: isTbd ? 'default' : 'pointer',
      background: 'var(--paper-2)',
      border: isTbd ? '1.5px dashed var(--ink)' : '1.5px solid var(--ink)',
      boxShadow: isTbd
        ? '3px 3px 0 rgba(0,0,0,0.1), 3px 3px 0 1px var(--ink)'
        : `4px 4px 0 ${accentVar}, 4px 4px 0 1px var(--ink)`,
      borderRadius: '4px',
    }"
    @click="isTbd ? undefined : $emit('click')"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      padding: '8px 12px', background: 'var(--ink)', color: 'var(--paper)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }">
        {{ kickoffLabel }}
      </span>
      <StageBadge :stage="match.stage" :group="match.group ?? undefined" :tone="isTbd ? 'cobalt' : accent" />
    </div>

    <div :style="{ padding: '12px 14px' }">
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <!-- Home -->
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px' }">
          <template v-if="homeTbd">
            <div :style="{ width: '36px', height: '36px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.35, flexShrink: 0 }" />
            <div :style="{ opacity: 0.45 }">
              <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">A definir</div>
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.06em' }">AGUARDANDO</div>
            </div>
          </template>
          <template v-else>
            <FlagChip :flagCode="match.home.flag_code" :teamName="match.home.name" :teamCode="match.home.code" :size="36" :tone="accent" />
            <div>
              <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">
                {{ match.home.code }}<span v-if="isHomeWinner" :title="'Classificado'" :style="{ color: accentVar }"> ✓</span>
              </div>
              <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.06em' }">
                {{ match.home.name.toUpperCase() }}
              </div>
            </div>
          </template>
        </div>

        <!-- Placar / VS (pênaltis entre parênteses, ex.: 1(4) × (5)1) -->
        <div
          class="font-display"
          :style="{
            fontSize: match.status === 'SCHEDULED' ? '16px' : (hasPenalties ? '18px' : '26px'),
            color: match.status === 'SCHEDULED' ? 'var(--ink)' : accentVar,
            minWidth: '52px', textAlign: 'center', whiteSpace: 'nowrap',
            opacity: match.status === 'SCHEDULED' ? 0.4 : 1,
          }"
        >{{ scoreLabel }}</div>

        <!-- Away -->
        <div :style="{ flex: 1, display: 'flex', alignItems: 'center', gap: '8px', justifyContent: 'flex-end' }">
          <template v-if="awayTbd">
            <div :style="{ textAlign: 'right', opacity: 0.45 }">
              <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">A definir</div>
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.06em' }">AGUARDANDO</div>
            </div>
            <div :style="{ width: '36px', height: '36px', borderRadius: '50%', border: '1.5px dashed var(--ink)', opacity: 0.35, flexShrink: 0 }" />
          </template>
          <template v-else>
            <div :style="{ textAlign: 'right' }">
              <div class="font-display" :style="{ fontSize: '17px', lineHeight: 1 }">
                <span v-if="isAwayWinner" :title="'Classificado'" :style="{ color: accentVar }">✓ </span>{{ match.away.code }}
              </div>
              <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, letterSpacing: '0.06em' }">
                {{ match.away.name.toUpperCase() }}
              </div>
            </div>
            <FlagChip :flagCode="match.away.flag_code" :teamName="match.away.name" :teamCode="match.away.code" :size="36" :tone="accent" />
          </template>
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
          color: !isTbd && myGuess && match.status === 'SCHEDULED' ? accentVar : 'inherit',
          opacity: isTbd ? 0.55 : (myGuess ? 1 : 0.55),
        }"
      >{{ isTbd ? 'AGUARDANDO CLASSIFICADOS' : guessLabel }}</span>
      <span
        v-if="isTbd"
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.1em', fontWeight: 700, opacity: 0.55 }"
      >TBD</span>
      <span v-else class="font-display" :style="{ fontSize: '11px', color: accentVar }">
        {{ match.status === 'SCHEDULED' ? (myGuess ? 'EDITAR →' : 'PALPITAR →') : 'VER →' }}
      </span>
    </div>
  </div>
</template>

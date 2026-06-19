<script setup lang="ts">
import { computed } from 'vue';
import FlagImg from '@/components/FlagImg.vue';
import { TEAMS, toneVar, toneFg } from '@/data/mock';
import { guessVerdict, verdictTone } from '@/utils/guess';
import type { GuessHistoryEntry } from '@/types';

const props = defineProps<{ g: GuessHistoryEntry }>();
defineEmits<{ (e: 'click'): void }>();

const pending = computed(() => props.g.status === 'pending');
const verdict = computed(() => guessVerdict(props.g));
const tone = computed(() => verdictTone(verdict.value));
const ptsLabel = computed(() => (pending.value ? '—' : `+${props.g.pts}`));
const homeCode = computed(() => TEAMS[props.g.home]?.code ?? props.g.home);
const awayCode = computed(() => TEAMS[props.g.away]?.code ?? props.g.away);
</script>

<template>
  <div
    :style="{
      cursor: g.matchId ? 'pointer' : 'default',
      background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
      boxShadow: '3px 3px 0 var(--ink)', borderRadius: '6px',
      display: 'flex', alignItems: 'stretch', overflow: 'hidden',
    }"
    @click="g.matchId && $emit('click')"
  >
    <!-- points chip -->
    <div
      class="font-display"
      :style="{
        width: '52px', flexShrink: 0,
        background: toneVar(tone),
        color: toneFg(tone),
        borderRight: '1.5px solid var(--ink)',
        display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      }"
    >
      <div :style="{ fontSize: '26px', lineHeight: 0.9 }">{{ ptsLabel }}</div>
      <div class="font-mono" :style="{ fontSize: '7px', letterSpacing: '0.1em', marginTop: '2px' }">PTS</div>
    </div>

    <!-- match + guess -->
    <div :style="{ flex: 1, minWidth: 0, padding: '9px 12px' }">
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '6px' }">
        <span class="font-mono" :style="{ fontSize: '8px', letterSpacing: '0.12em', fontWeight: 700, opacity: 0.6 }">
          {{ g.pool.toUpperCase() }}
        </span>
        <span class="font-mono" :style="{ fontSize: '8px', letterSpacing: '0.12em', opacity: 0.6 }">
          {{ g.date }}
        </span>
      </div>
      <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
        <!-- teams -->
        <div :style="{ display: 'flex', alignItems: 'center', gap: '6px' }">
          <FlagImg :team="g.home" :size="18" :radius="2" />
          <span class="font-display" :style="{ fontSize: '17px' }">{{ homeCode }}</span>
          <span class="font-display" :style="{ fontSize: '13px', opacity: 0.4 }">×</span>
          <span class="font-display" :style="{ fontSize: '17px' }">{{ awayCode }}</span>
          <FlagImg :team="g.away" :size="18" :radius="2" />
        </div>
        <!-- guess vs real -->
        <div :style="{ marginLeft: 'auto', display: 'flex', alignItems: 'center', gap: '8px' }">
          <div :style="{ textAlign: 'center' }">
            <div class="font-mono" :style="{ fontSize: '7px', letterSpacing: '0.1em', opacity: 0.55 }">VOCÊ</div>
            <div class="font-display" :style="{ fontSize: '16px', lineHeight: 1 }">{{ g.myHome }}-{{ g.myAway }}</div>
          </div>
          <div :style="{ textAlign: 'center' }">
            <div class="font-mono" :style="{ fontSize: '7px', letterSpacing: '0.1em', opacity: 0.55 }">
              {{ pending ? 'PARCIAL' : 'REAL' }}
            </div>
            <div
              class="font-display"
              :style="{ fontSize: '16px', lineHeight: 1, color: toneVar(tone), WebkitTextStroke: '0.5px var(--ink)' }"
            >{{ g.realHome }}-{{ g.realAway }}</div>
          </div>
          <span
            class="font-mono"
            :style="{
              fontSize: '8px', letterSpacing: '0.1em', fontWeight: 700,
              padding: '3px 6px', borderRadius: '3px', alignSelf: 'center',
              background: 'var(--ink)', color: 'var(--paper)',
            }"
          >{{ verdict }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

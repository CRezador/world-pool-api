<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import BackBar from '@/components/BackBar.vue';
import TicketCard from '@/components/TicketCard.vue';
import StageBadge from '@/components/StageBadge.vue';
import FlagChip from '@/components/FlagChip.vue';
import ScoreStepper from '@/components/ScoreStepper.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';
import { findMatch, TEAMS } from '@/data/mock';

const route = useRoute();
const router = useRouter();
const matchId = computed(() => Number(route.params.matchId));
const match = computed(() => findMatch(matchId.value));
const home = computed(() => TEAMS[match.value.home]);
const away = computed(() => TEAMS[match.value.away]);

const homeScore = ref(2);
const awayScore = ref(0);
const saved = ref(false);

const popularGuesses = [
  { score: '2-1', pct: 28, isMine: false },
  { score: '1-1', pct: 19, isMine: false },
  { score: '2-0', pct: 15, isMine: true },
  { score: '1-2', pct: 12, isMine: false },
  { score: '3-1', pct: 9, isMine: false },
];

function commit() {
  saved.value = true;
  setTimeout(() => router.push('/'), 800);
}
</script>

<template>
  <div :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 8px' }">
      <BackBar title="Apitar" :kicker="`PALPITE · ${match.day.toUpperCase()}`" fallback="/matches" />
    </div>

    <div :style="{ padding: '8px 18px 18px', position: 'relative' }">
      <div
        class="halftone"
        :style="{
          position: 'absolute', top: 0, left: '18px', right: '18px',
          color: 'var(--magenta)', height: '30px',
        }"
      />

      <TicketCard accent="magenta" :style="{ marginTop: '14px' }">
        <div :style="{ padding: '18px 14px' }">
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '6px' }">
            <StageBadge :stage="match.stage" :group="match.group" tone="magenta" />
            <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">{{ match.kickoff }}</span>
          </div>
          <div
            class="font-mono"
            :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, marginBottom: '14px' }"
          >📍 {{ match.venue.toUpperCase() }}</div>

          <div :style="{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '10px' }">
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip :team="match.home" :size="58" tone="magenta" />
              <div class="font-display" :style="{ fontSize: '20px', marginTop: '8px' }">{{ home.code }}</div>
              <ScoreStepper v-model:value="homeScore" accent="magenta" />
            </div>
            <div
              class="font-display"
              :style="{ fontSize: '36px', opacity: 0.4, transform: 'translateY(-4px)' }"
            >×</div>
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip :team="match.away" :size="58" tone="cobalt" />
              <div class="font-display" :style="{ fontSize: '20px', marginTop: '8px' }">{{ away.code }}</div>
              <ScoreStepper v-model:value="awayScore" accent="cobalt" />
            </div>
          </div>

          <PerfDivider />

          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }">
            <span
              class="font-mono"
              :style="{ fontSize: '10px', letterSpacing: '0.14em', opacity: 0.7 }"
            >
              SE CRAVAR <b :style="{ color: 'var(--magenta)' }">+3</b> · SÓ VENCEDOR <b :style="{ color: 'var(--cobalt)' }">+1</b>
            </span>
            <span
              class="font-mono"
              :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.5 }"
            >fecha {{ match.day }} {{ match.kickoff }}</span>
          </div>
          <div :style="{ marginTop: '14px' }">
            <PrintButton :tone="saved ? 'lime' : 'magenta'" full @click="commit">
              {{ saved ? '✓ Palpite registrado' : 'Cravar palpite' }}
            </PrintButton>
          </div>
        </div>
      </TicketCard>
    </div>

    <div :style="{ padding: '0 18px 18px' }">
      <div
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, marginBottom: '8px' }"
      >O QUE A GERAL ACHA · 12 SÓCIOS</div>
      <div :style="{ display: 'flex', flexDirection: 'column', gap: '6px' }">
        <div
          v-for="p in popularGuesses"
          :key="p.score"
          :style="{ display: 'flex', alignItems: 'center', gap: '10px' }"
        >
          <div class="font-display" :style="{ fontSize: '16px', minWidth: '44px' }">{{ p.score }}</div>
          <div :style="{
            flex: 1, height: '14px', background: 'var(--paper-2)',
            border: '1.5px solid var(--ink)', position: 'relative',
          }">
            <div :style="{
              height: '100%', width: `${p.pct * 3}%`,
              background: p.isMine ? 'var(--magenta)' : 'var(--ink)',
            }" />
            <div
              class="halftone"
              :style="{
                position: 'absolute', inset: 0,
                color: 'var(--paper)', opacity: 0.4,
                mixBlendMode: 'difference',
              }"
            />
          </div>
          <div class="font-mono" :style="{ fontSize: '11px', minWidth: '32px', textAlign: 'right' }">
            {{ p.pct }}%
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

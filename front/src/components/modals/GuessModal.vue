<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import FlagChip from '@/components/FlagChip.vue';
import StageBadge from '@/components/StageBadge.vue';
import ScoreStepper from '@/components/ScoreStepper.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';
import { TEAMS } from '@/data/mock';
import type { Match } from '@/types';

const props = withDefaults(defineProps<{
  match: Match | null;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'submit', payload: { matchId: number; home: number; away: number }): void;
}>();

const home = ref(2);
const away = ref(0);
const saved = ref(false);

watch(() => props.match?.id, (id) => {
  if (id) { home.value = 2; away.value = 0; saved.value = false; }
});

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.match) emit('close');
}

onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

const homeTeam = computed(() => props.match ? TEAMS[props.match.home] : null);
const awayTeam = computed(() => props.match ? TEAMS[props.match.away] : null);

const popularGuesses = [
  { score: '2-1', pct: 28 },
  { score: '1-1', pct: 19 },
  { score: '2-0', pct: 15 },
  { score: '1-2', pct: 12 },
  { score: '3-1', pct: 9 },
];

const myScoreKey = computed(() => `${home.value}-${away.value}`);

function accept() {
  if (!props.match) return;
  saved.value = true;
  const payload = { matchId: props.match.id, home: home.value, away: away.value };
  setTimeout(() => {
    emit('submit', payload);
    emit('close');
  }, 700);
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="match"
        class="modal-backdrop"
        :style="{ padding: variant === 'desktop' ? '28px' : '14px' }"
        @click="$emit('close')"
      >
        <div
          class="perf-bottom modal-card"
          :class="variant"
          @click.stop
        >
          <div :style="{
            position: 'absolute', top: 0, right: 0,
            width: variant === 'desktop' ? '120px' : '80px',
            height: variant === 'desktop' ? '28px' : '22px',
            color: 'var(--magenta)', pointerEvents: 'none',
          }">
            <div class="halftone" :style="{ height: '100%' }" />
          </div>

          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
            <div :style="{ flex: 1, minWidth: 0 }">
              <div
                class="font-mono"
                :style="{
                  fontSize: variant === 'desktop' ? '10px' : '9px',
                  letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75,
                }"
              >CRAVE O PLACAR · {{ match.day.toUpperCase() }} · {{ match.kickoff }}</div>
              <div
                class="font-display"
                :style="{
                  fontSize: variant === 'desktop' ? '30px' : '22px',
                  lineHeight: 0.95,
                  marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.01em',
                }"
              >Apitar palpite</div>
              <div :style="{ display: 'flex', alignItems: 'center', gap: '10px', marginTop: '8px' }">
                <StageBadge :stage="match.stage" :group="match.group" tone="magenta" />
                <span
                  class="font-mono"
                  :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.75 }"
                >📍 {{ match.venue.toUpperCase() }}</span>
              </div>
            </div>
            <button
              aria-label="Fechar"
              class="font-display press modal-close"
              @click="$emit('close')"
            >✕</button>
          </div>

          <div
            :style="{
              marginTop: variant === 'desktop' ? '18px' : '14px',
              padding: variant === 'desktop' ? '18px 14px' : '14px 10px',
              background: 'var(--paper-2)',
              border: '1.5px solid var(--ink)',
              borderRadius: '4px',
              display: 'flex', alignItems: 'center', justifyContent: 'space-between',
              gap: '10px',
            }"
          >
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip :team="match.home" :size="variant === 'desktop' ? 64 : 50" tone="magenta" />
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '22px' : '18px', marginTop: '8px' }"
              >{{ homeTeam?.code }}</div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.65, marginBottom: '10px' }"
              >{{ homeTeam?.name.toUpperCase() }}</div>
              <ScoreStepper v-model:value="home" accent="magenta" />
            </div>
            <div
              class="font-display"
              :style="{
                fontSize: variant === 'desktop' ? '44px' : '34px',
                opacity: 0.4, transform: 'translateY(-6px)',
              }"
            >×</div>
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip :team="match.away" :size="variant === 'desktop' ? 64 : 50" tone="cobalt" />
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '22px' : '18px', marginTop: '8px' }"
              >{{ awayTeam?.code }}</div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.65, marginBottom: '10px' }"
              >{{ awayTeam?.name.toUpperCase() }}</div>
              <ScoreStepper v-model:value="away" accent="cobalt" />
            </div>
          </div>

          <div :style="{
            marginTop: '14px', padding: '10px 12px',
            background: 'var(--ink)', color: 'var(--paper)', borderRadius: '3px',
            display: 'flex', alignItems: 'center', justifyContent: 'space-between',
            gap: '8px', flexWrap: 'wrap',
          }">
            <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.14em' }">
              SE CRAVAR <b :style="{ color: 'var(--lime)' }">+3</b>
              <span :style="{ opacity: 0.4, margin: '0 8px' }">·</span>
              SÓ VENCEDOR <b :style="{ color: 'var(--cobalt)' }">+1</b>
            </span>
            <span
              class="font-mono"
              :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.7 }"
            >FECHA {{ match.day }} {{ match.kickoff }}</span>
          </div>

          <div v-if="variant === 'desktop'" :style="{ marginTop: '14px' }">
            <div
              class="font-mono"
              :style="{
                fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700,
                marginBottom: '6px', opacity: 0.85,
              }"
            >O QUE A GERAL ACHA · 12 SÓCIOS</div>
            <div :style="{ display: 'flex', flexDirection: 'column', gap: '4px' }">
              <div
                v-for="p in popularGuesses"
                :key="p.score"
                :style="{ display: 'flex', alignItems: 'center', gap: '10px' }"
              >
                <div class="font-display" :style="{ fontSize: '14px', minWidth: '36px' }">{{ p.score }}</div>
                <div :style="{
                  flex: 1, height: '12px', background: 'var(--paper-2)',
                  border: '1.5px solid var(--ink)', position: 'relative',
                }">
                  <div :style="{
                    height: '100%', width: `${p.pct * 3}%`,
                    background: p.score === myScoreKey ? 'var(--magenta)' : 'var(--ink)',
                  }" />
                  <div class="halftone" :style="{
                    position: 'absolute', inset: 0,
                    color: 'var(--paper)', opacity: 0.35,
                    mixBlendMode: 'difference', pointerEvents: 'none',
                  }" />
                </div>
                <div
                  class="font-mono"
                  :style="{ fontSize: '11px', minWidth: '32px', textAlign: 'right' }"
                >{{ p.pct }}%</div>
              </div>
            </div>
          </div>

          <PerfDivider />

          <div :style="{ display: 'flex', gap: '8px', marginTop: '12px' }">
            <button class="font-display press modal-cancel" @click="$emit('close')">Cancelar</button>
            <PrintButton :tone="saved ? 'lime' : 'magenta'" full @click="accept">
              {{ saved ? '✓ Palpite cravado' : `Cravar ${home} × ${away}` }}
            </PrintButton>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 200;
  background: rgba(20, 17, 14, 0.6);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-card {
  position: relative;
  background: var(--paper);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  border-radius: 4px;
  width: 100%;
}
.modal-card.compact {
  max-width: 360px;
  padding: 16px;
  box-shadow: 5px 5px 0 var(--magenta), 5px 5px 0 1px var(--ink);
}
.modal-card.desktop {
  max-width: 620px;
  padding: 24px;
  box-shadow: 7px 7px 0 var(--magenta), 7px 7px 0 1px var(--ink);
}

.modal-close {
  background: var(--paper-2);
  border: 1.5px solid var(--ink);
  box-shadow: 2px 2px 0 var(--ink);
  cursor: pointer;
  width: 32px;
  height: 32px;
  font-size: 14px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  border-radius: 3px;
  flex-shrink: 0;
  color: var(--ink);
}

.modal-cancel {
  background: var(--paper-2);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  box-shadow: 3px 3px 0 var(--ink);
  padding: 10px 14px;
  font-size: 13px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  cursor: pointer;
  border-radius: 4px;
  flex-shrink: 0;
}
.modal-card.desktop .modal-cancel {
  padding: 12px 18px;
  font-size: 15px;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.18s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import FlagChip from '@/components/FlagChip.vue';
import StageBadge from '@/components/StageBadge.vue';
import ScoreStepper from '@/components/ScoreStepper.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';
import { useGuesses } from '@/composables/useGuesses';
import { formatKickoff } from '@/utils/date';
import type { ApiMatch, GuessEntry } from '@/types';

const props = withDefaults(defineProps<{
  match: ApiMatch | null;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const emit = defineEmits<{ (e: 'close'): void }>();

const { guesses, fetchMyGuesses, createGuess, updateGuess } = useGuesses();

const home = ref(0);
const away = ref(0);
const saved = ref(false);
const loadingGuess = ref(false);
const submitError = ref<string | null>(null);

const existingGuess = computed<GuessEntry | null>(() =>
  props.match ? (guesses.value.find(g => g.matchId === props.match!.id) ?? null) : null,
);

async function loadGuesses(matchId: number) {
  loadingGuess.value = true;
  try {
    await fetchMyGuesses();
    const found = guesses.value.find(g => g.matchId === matchId);
    home.value = found?.homeScore ?? 0;
    away.value = found?.awayScore ?? 0;
  } finally {
    loadingGuess.value = false;
  }
}

watch(() => props.match?.id, async (id) => {
  if (!id) return;
  saved.value = false;
  submitError.value = null;
  home.value = 0;
  away.value = 0;
  await loadGuesses(id);
});

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.match) emit('close');
}
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

const kickoffLabel = computed(() => formatKickoff(props.match?.kickoff));

async function accept() {
  if (!props.match || loadingGuess.value) return;
  submitError.value = null;
  saved.value = true;
  try {
    if (existingGuess.value) {
      await updateGuess(existingGuess.value.id, home.value, away.value);
    } else {
      await createGuess(props.match.id, home.value, away.value);
    }
    setTimeout(() => emit('close'), 700);
  } catch (e: any) {
    saved.value = false;
    submitError.value = e?.response?.data?.message ?? 'Erro ao salvar palpite';
  }
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

          <!-- Header -->
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
            <div :style="{ flex: 1, minWidth: 0 }">
              <div
                class="font-mono"
                :style="{
                  fontSize: variant === 'desktop' ? '10px' : '9px',
                  letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75,
                }"
              >CRAVE O PLACAR · {{ kickoffLabel }}</div>
              <div
                class="font-display"
                :style="{
                  fontSize: variant === 'desktop' ? '30px' : '22px',
                  lineHeight: 0.95,
                  marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.01em',
                }"
              >{{ existingGuess ? 'Atualizar palpite' : 'Apitar palpite' }}</div>
              <div :style="{ display: 'flex', alignItems: 'center', gap: '10px', marginTop: '8px', flexWrap: 'wrap' }">
                <StageBadge :stage="match.stage" :group="match.group ?? undefined" tone="magenta" />
                <span
                  v-if="existingGuess && !loadingGuess"
                  class="font-mono"
                  :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.7 }"
                >palpite atual: {{ existingGuess.homeScore }} × {{ existingGuess.awayScore }}</span>
              </div>
            </div>
            <button
              aria-label="Fechar"
              class="font-display press modal-close"
              @click="$emit('close')"
            >✕</button>
          </div>

          <!-- Teams & steppers -->
          <div
            :style="{
              marginTop: variant === 'desktop' ? '18px' : '14px',
              padding: variant === 'desktop' ? '18px 14px' : '14px 10px',
              background: 'var(--paper-2)',
              border: '1.5px solid var(--ink)',
              borderRadius: '4px',
              display: 'flex', alignItems: 'center', justifyContent: 'space-between',
              gap: '10px',
              opacity: loadingGuess ? 0.5 : 1,
              transition: 'opacity 0.2s',
            }"
          >
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip
                :flagCode="match.home.flag_code"
                :teamName="match.home.name"
                :teamCode="match.home.code"
                :size="variant === 'desktop' ? 64 : 50"
                tone="magenta"
              />
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '22px' : '18px', marginTop: '8px' }"
              >{{ match.home.code }}</div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.65, marginBottom: '10px' }"
              >{{ match.home.name.toUpperCase() }}</div>
              <ScoreStepper v-model:value="home" accent="magenta" :disabled="loadingGuess" />
            </div>
            <div
              class="font-display"
              :style="{
                fontSize: variant === 'desktop' ? '44px' : '34px',
                opacity: 0.4, transform: 'translateY(-6px)',
              }"
            >×</div>
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'center', flex: 1 }">
              <FlagChip
                :flagCode="match.away.flag_code"
                :teamName="match.away.name"
                :teamCode="match.away.code"
                :size="variant === 'desktop' ? 64 : 50"
                tone="cobalt"
              />
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '22px' : '18px', marginTop: '8px' }"
              >{{ match.away.code }}</div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.65, marginBottom: '10px' }"
              >{{ match.away.name.toUpperCase() }}</div>
              <ScoreStepper v-model:value="away" accent="cobalt" :disabled="loadingGuess" />
            </div>
          </div>

          <!-- Points info -->
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
            >FECHA {{ kickoffLabel }}</span>
          </div>

          <!-- Error -->
          <div
            v-if="submitError"
            :style="{ marginTop: '10px', padding: '8px 12px', background: 'var(--coral)', borderRadius: '3px' }"
          >
            <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em', color: 'var(--paper)' }">
              {{ submitError }}
            </span>
          </div>

          <PerfDivider />

          <div :style="{ display: 'flex', gap: '8px', marginTop: '12px' }">
            <button class="font-display press modal-cancel" @click="$emit('close')">Cancelar</button>
            <PrintButton
              :tone="saved ? 'lime' : 'magenta'"
              full
              :disabled="loadingGuess"
              @click="accept"
            >
              <span v-if="loadingGuess">Carregando...</span>
              <span v-else-if="saved">✓ Palpite cravado</span>
              <span v-else>{{ existingGuess ? 'Atualizar' : 'Cravar' }} {{ home }} × {{ away }}</span>
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
  z-index: 300;
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

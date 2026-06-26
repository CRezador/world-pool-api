<script setup lang="ts">
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue';
import { toneVar, toneFg } from '@/data/mock';
import { verdictFromMatch, verdictTone } from '@/utils/guess';
import { useAdversaryGuesses } from '@/composables/useAdversaryGuesses';
import type { GuessHistoryEntry } from '@/types';

const props = withDefaults(defineProps<{
  context: GuessHistoryEntry | null;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const emit = defineEmits<{ (e: 'close'): void }>();

const { guesses, loading, gateMessage, load } = useAdversaryGuesses();

const TODOS = 'TODOS';
const poolFilter = ref<number | typeof TODOS>(TODOS);

watch(() => props.context?.matchId, async (matchId) => {
  poolFilter.value = TODOS;
  if (matchId) await load(matchId);
});

// Bolões distintos presentes na resposta (para os chips de filtro).
const poolOptions = computed(() => {
  const seen = new Map<number, string>();
  for (const g of guesses.value) {
    for (const p of g.pools) if (!seen.has(p.id)) seen.set(p.id, p.name);
  }
  return [...seen.entries()].map(([id, name]) => ({ id, name }));
});

const filtered = computed(() =>
  poolFilter.value === TODOS
    ? guesses.value
    : guesses.value.filter((g) => g.pools.some((p) => p.id === poolFilter.value))
);

function meta(g: (typeof guesses.value)[number]) {
  const verdict = verdictFromMatch(g.match.status, g.points);
  return { verdict, tone: verdictTone(verdict), pending: g.match.status !== 'FINISHED' };
}

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.context) emit('close');
}
onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="context"
        class="adv-backdrop"
        :style="{ padding: variant === 'desktop' ? '28px' : '14px' }"
        @click="$emit('close')"
      >
        <div class="adv-card" :class="variant" @click.stop>
          <!-- Header -->
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
            <div :style="{ flex: 1, minWidth: 0 }">
              <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75 }">
                PALPITES DOS ADVERSÁRIOS
              </div>
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '28px' : '22px', lineHeight: 0.95, marginTop: '4px', textTransform: 'uppercase' }"
              >{{ context.home }} <span :style="{ opacity: 0.4 }">×</span> {{ context.away }}</div>
              <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.1em', opacity: 0.7, marginTop: '6px' }">
                PLACAR {{ context.realHome }}-{{ context.realAway }}
                <template v-if="context.hasMyGuess !== false">
                  <span :style="{ opacity: 0.4, margin: '0 6px' }">·</span>
                  SEU PALPITE {{ context.myHome }}-{{ context.myAway }}
                </template>
              </div>
            </div>
            <button aria-label="Fechar" class="font-display press adv-close" @click="$emit('close')">✕</button>
          </div>

          <!-- Filtros por bolão -->
          <div
            v-if="!loading && !gateMessage && guesses.length"
            :style="{ display: 'flex', gap: '6px', marginTop: '16px', overflowX: 'auto', alignItems: 'center', paddingBottom: '2px' }"
          >
            <button class="font-mono adv-chip" :class="{ on: poolFilter === TODOS }" @click="poolFilter = TODOS">TODOS</button>
            <button
              v-for="p in poolOptions"
              :key="p.id"
              class="font-mono adv-chip"
              :class="{ on: poolFilter === p.id }"
              @click="poolFilter = p.id"
            >{{ p.name.toUpperCase() }}</button>
          </div>

          <!-- Estados -->
          <div
            v-if="loading"
            class="font-mono adv-empty"
          >Carregando palpites…</div>

          <div
            v-else-if="gateMessage"
            class="font-mono adv-empty"
          >{{ gateMessage }}</div>

          <div
            v-else-if="!filtered.length"
            class="font-mono adv-empty"
          >{{ guesses.length ? 'Nenhum adversário nesse bolão.' : 'Nenhum adversário palpitou nessa partida.' }}</div>

          <!-- Lista -->
          <div v-else :style="{ marginTop: '12px', display: 'flex', flexDirection: 'column', gap: '8px', maxHeight: '52vh', overflowY: 'auto' }">
            <div
              v-for="g in filtered"
              :key="g.id"
              :style="{
                display: 'flex', alignItems: 'center', gap: '10px',
                padding: '10px 12px', background: 'var(--paper-2)',
                border: '1.5px solid var(--ink)', borderRadius: '6px',
              }"
            >
              <!-- avatar -->
              <div
                class="font-display"
                :style="{
                  width: '38px', height: '38px', flexShrink: 0, borderRadius: '5px',
                  background: toneVar(meta(g).tone), color: toneFg(meta(g).tone),
                  border: '1.5px solid var(--ink)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '15px',
                }"
              >{{ g.user.initials }}</div>

              <!-- nome + bolões -->
              <div :style="{ flex: 1, minWidth: 0 }">
                <div
                  class="font-display"
                  :style="{ fontSize: '17px', lineHeight: 1, whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }"
                >{{ g.user.name }}</div>
                <div :style="{ display: 'flex', gap: '4px', marginTop: '5px', flexWrap: 'wrap' }">
                  <span
                    v-for="p in g.pools"
                    :key="p.id"
                    class="font-mono"
                    :style="{
                      fontSize: '8px', letterSpacing: '0.08em', fontWeight: 700,
                      padding: '2px 5px', borderRadius: '3px',
                      background: 'var(--paper)', border: '1px solid var(--ink)', opacity: 0.85,
                    }"
                  >{{ p.name }}</span>
                </div>
              </div>

              <!-- palpite -->
              <div :style="{ textAlign: 'center' }">
                <div class="font-mono" :style="{ fontSize: '7px', letterSpacing: '0.1em', opacity: 0.55 }">PALPITE</div>
                <div class="font-display" :style="{ fontSize: '18px', lineHeight: 1 }">{{ g.homeScore }}-{{ g.awayScore }}</div>
              </div>

              <!-- veredito -->
              <span
                class="font-mono"
                :style="{
                  fontSize: '8px', letterSpacing: '0.1em', fontWeight: 700,
                  padding: '4px 7px', borderRadius: '3px', alignSelf: 'center', flexShrink: 0,
                  minWidth: '58px', textAlign: 'center',
                  background: toneVar(meta(g).tone), color: toneFg(meta(g).tone),
                  border: '1.5px solid var(--ink)',
                }"
              >{{ meta(g).verdict }}</span>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.adv-backdrop {
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
.adv-card {
  position: relative;
  background: var(--paper);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  border-radius: 4px;
  width: 100%;
}
.adv-card.compact {
  max-width: 380px;
  padding: 16px;
  box-shadow: 5px 5px 0 var(--magenta), 5px 5px 0 1px var(--ink);
}
.adv-card.desktop {
  max-width: 560px;
  padding: 24px;
  box-shadow: 7px 7px 0 var(--magenta), 7px 7px 0 1px var(--ink);
}
.adv-close {
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
.adv-chip {
  padding: 7px 11px;
  background: var(--paper-2);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  font-size: 10px;
  letter-spacing: 0.1em;
  font-weight: 700;
  cursor: pointer;
  border-radius: 3px;
  white-space: nowrap;
  flex-shrink: 0;
}
.adv-chip.on {
  background: var(--ink);
  color: var(--paper);
}
.adv-empty {
  margin-top: 16px;
  padding: 22px 16px;
  text-align: center;
  font-size: 12px;
  letter-spacing: 0.08em;
  opacity: 0.65;
  background: var(--paper-2);
  border: 1.5px dashed var(--ink);
  border-radius: 6px;
}
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.18s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>

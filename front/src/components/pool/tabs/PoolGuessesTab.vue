<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useGuesses } from '@/composables/useGuesses';
import { formatScoreWithPenalties } from '@/utils/score';
import type { GuessEntry } from '@/types';

defineProps<{ poolId: string }>();

const router  = useRouter();
const loading = ref(true);
const { guesses, fetchMyGuesses } = useGuesses();

onMounted(async () => {
  try {
    await fetchMyGuesses();
  } finally {
    loading.value = false;
  }
});

const scheduled  = computed(() => guesses.value.filter(g => g.match.status === 'SCHEDULED'));
const inProgress = computed(() => guesses.value.filter(g => g.match.status === 'IN_PROGRESS'));
const finished   = computed(() => guesses.value.filter(g => g.match.status === 'FINISHED'));

function ptsBg(pts: number | null) {
  if (pts === null) return 'transparent';
  if (pts >= 3) return 'var(--lime)';
  if (pts >= 1) return 'var(--cobalt)';
  return 'var(--paper-3)';
}
function ptsFg(pts: number | null) {
  if (pts === null || pts === 0) return 'var(--ink)';
  if (pts >= 3) return 'var(--ink)';
  return 'var(--paper)';
}
function ptsLabel(pts: number | null) {
  if (pts === null) return '?';
  return pts > 0 ? `+${pts}` : '0';
}
function ptsBorder(pts: number | null) {
  return pts === null ? '1.5px dashed var(--ink)' : 'none';
}
function stageLabel(g: GuessEntry) {
  return g.match.stage === 'GROUP_STAGE'
    ? `GRUPO ${g.match.group ?? ''}`
    : g.match.stage.replace(/_/g, ' ');
}
function goToGuess(g: GuessEntry) {
  if (g.match.status === 'SCHEDULED') router.push(`/guess/${g.matchId}`);
  else router.push(`/match/${g.matchId}`);
}
</script>

<template>
  <!-- Skeleton -->
  <div v-if="loading" :style="{ padding: '14px 18px 18px', display: 'flex', flexDirection: 'column', gap: '12px' }">
    <div class="skeleton" :style="{ height: '10px', width: '180px' }" />
    <div v-for="n in 4" :key="n" class="skeleton" :style="{ height: '110px' }" />
  </div>

  <div v-else class="fade-up" :style="{ padding: '14px 18px 24px', display: 'flex', flexDirection: 'column', gap: '24px' }">

    <!-- Empty state -->
    <div
      v-if="guesses.length === 0"
      :style="{
        padding: '28px', textAlign: 'center',
        border: '1.5px dashed var(--ink)',
      }"
    >
      <div class="font-display" :style="{ fontSize: '20px', marginBottom: '6px' }">Sem palpites ainda</div>
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.6 }">
        ACESSE OS JOGOS E COMECE A PALPITAR
      </div>
    </div>

    <!-- Em andamento -->
    <section v-if="inProgress.length">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--coral)', marginBottom: '8px' }">
        ● EM PROGRESSO · {{ inProgress.length }} {{ inProgress.length === 1 ? 'JOGO' : 'JOGOS' }}
      </div>
      <div :style="{ display: 'flex', flexDirection: 'column', gap: '10px' }">
        <div
          v-for="g in inProgress" :key="g.id"
          class="press"
          :style="{ background: 'var(--paper-2)', border: '1.5px solid var(--coral)', padding: '12px', cursor: 'pointer', boxShadow: '3px 3px 0 var(--coral)' }"
          @click="goToGuess(g)"
        >
          <template v-if="g.match">
            <GuessCardContent :g="g" :pts-bg="ptsBg(g.points)" :pts-fg="ptsFg(g.points)" :pts-label="ptsLabel(g.points)" :pts-border="ptsBorder(g.points)" :stage-label="stageLabel(g)" />
          </template>
        </div>
      </div>
    </section>

    <!-- Agendados -->
    <section v-if="scheduled.length">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }">
        PALPITES ABERTOS · {{ scheduled.length }} {{ scheduled.length === 1 ? 'JOGO' : 'JOGOS' }}
      </div>
      <div :style="{ display: 'flex', flexDirection: 'column', gap: '10px' }">
        <div
          v-for="g in scheduled" :key="g.id"
          class="press"
          :style="{ background: 'var(--paper-2)', border: '1.5px solid var(--ink)', padding: '12px', cursor: 'pointer', boxShadow: '3px 3px 0 var(--ink)' }"
          @click="goToGuess(g)"
        >
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '10px' }">
            <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }">
              {{ stageLabel(g) }} · {{ g.match.kickoffAt ?? '—' }}
            </div>
            <span class="font-mono" :style="{ fontSize: '10px', padding: '3px 8px', border: '1.5px dashed var(--ink)', borderRadius: '2px', letterSpacing: '0.06em' }">
              ABERTO
            </span>
          </div>
          <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
            <img v-if="g.match.homeTeam.flagUrl" :src="g.match.homeTeam.flagUrl" :alt="g.match.homeTeam.code" :style="{ width: '22px', height: '15px', objectFit: 'cover', borderRadius: '2px', border: '1px solid var(--ink)', flexShrink: 0 }" />
            <div class="font-display" :style="{ fontSize: '18px', flex: 1 }">{{ g.match.homeTeam.code }} × {{ g.match.awayTeam.code }}</div>
            <img v-if="g.match.awayTeam.flagUrl" :src="g.match.awayTeam.flagUrl" :alt="g.match.awayTeam.code" :style="{ width: '22px', height: '15px', objectFit: 'cover', borderRadius: '2px', border: '1px solid var(--ink)', flexShrink: 0 }" />
          </div>
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginTop: '10px', paddingTop: '10px', borderTop: '1px dashed var(--ink)' }">
            <div>
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6 }">MEU PALPITE</div>
              <div class="font-display" :style="{ fontSize: '24px', lineHeight: 1 }">{{ g.homeScore }} × {{ g.awayScore }}</div>
            </div>
            <span class="font-mono" :style="{ fontSize: '10px', padding: '4px 8px', letterSpacing: '0.1em', border: '1.5px solid var(--ink)', borderRadius: '3px' }">EDITAR ↗</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Finalizados -->
    <section v-if="finished.length">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }">
        HISTÓRICO · {{ finished.length }} {{ finished.length === 1 ? 'JOGO' : 'JOGOS' }}
      </div>
      <div :style="{ display: 'flex', flexDirection: 'column', gap: '10px' }">
        <div
          v-for="g in finished" :key="g.id"
          class="press"
          :style="{ background: 'var(--paper-2)', border: '1.5px solid var(--ink)', padding: '12px', cursor: 'pointer' }"
          @click="goToGuess(g)"
        >
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '10px' }">
            <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }">
              {{ stageLabel(g) }} · {{ g.match.kickoffAt ?? '—' }}
            </div>
            <span
              class="font-mono"
              :style="{
                padding: '3px 8px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.04em',
                background: ptsBg(g.points), color: ptsFg(g.points),
                border: ptsBorder(g.points), borderRadius: '2px',
              }"
            >{{ ptsLabel(g.points) }}</span>
          </div>
          <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
            <img v-if="g.match.homeTeam.flagUrl" :src="g.match.homeTeam.flagUrl" :alt="g.match.homeTeam.code" :style="{ width: '22px', height: '15px', objectFit: 'cover', borderRadius: '2px', border: '1px solid var(--ink)', flexShrink: 0 }" />
            <div class="font-display" :style="{ fontSize: '18px', flex: 1 }">{{ g.match.homeTeam.code }} × {{ g.match.awayTeam.code }}</div>
            <img v-if="g.match.awayTeam.flagUrl" :src="g.match.awayTeam.flagUrl" :alt="g.match.awayTeam.code" :style="{ width: '22px', height: '15px', objectFit: 'cover', borderRadius: '2px', border: '1px solid var(--ink)', flexShrink: 0 }" />
          </div>
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginTop: '10px', paddingTop: '10px', borderTop: '1px dashed var(--ink)' }">
            <div>
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6 }">MEU PALPITE</div>
              <div class="font-display" :style="{ fontSize: '24px', lineHeight: 1 }">{{ g.homeScore }} × {{ g.awayScore }}</div>
            </div>
            <div :style="{ textAlign: 'right' }">
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6 }">RESULTADO</div>
              <div class="font-display" :style="{ fontSize: '24px', lineHeight: 1 }">
                {{ g.match.homeScore !== null ? formatScoreWithPenalties(g.match.homeScore, g.match.awayScore, g.match.homePenalties, g.match.awayPenalties) : '— × —' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

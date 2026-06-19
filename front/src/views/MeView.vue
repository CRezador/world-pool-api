<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import Masthead from '@/components/Masthead.vue';
import SectionHead from '@/components/SectionHead.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import GuessHistoryRow from '@/components/pool/GuessHistoryRow.vue';
import DesktopMe from '@/components/pool/DesktopMe.vue';
import { guessVerdict, type GuessVerdict } from '@/utils/guess';
import { toneVar, toneFg } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useAuth } from '@/composables/useAuth';
import { useMe } from '@/composables/useMe';
import { useAdversaryModal } from '@/composables/useAdversaryModal';
import { logout } from '@/services/auth.services';
import type { GuessHistoryEntry } from '@/types';

const router = useRouter();
const { isDesktop } = useBreakpoint();
const { clearUser } = useAuth();
const { profile: me, history, poolsCount, loading, loadMe } = useMe();
const adversaries = useAdversaryModal();

// No desktop quem carrega é o DesktopMe (que também busca pools e tem loading
// próprio); o pai só dispara loadMe no layout mobile, evitando stats/guesses
// duplicados. Os dados são singletons compartilhados, então o resize cobre o resto.
onMounted(() => {
  if (!isDesktop.value) loadMe();
});

const stats = computed(() => [
  { label: 'PONTOS · TEMPORADA', value: me.value.seasonPoints, tone: 'magenta', sub: poolsCount.value === 1 ? 'somados em 1 bolão' : `somados em ${poolsCount.value} bolões` },
  { label: 'APROVEITAMENTO', value: me.value.hitRate + '%', tone: 'cobalt', sub: 'palpites que pontuaram' },
  { label: 'SEQUÊNCIA ATUAL', value: me.value.streak, tone: 'lime', sub: 'palpites seguidos no alvo' },
  { label: 'MELHOR POSIÇÃO', value: me.value.bestRank ? me.value.bestRank + 'º' : '—', tone: 'coral', sub: 'entre seus bolões' },
]);

const FILTERS = ['TODOS', 'EM JOGO', 'EM PROGRESSO', 'CRAVOU', 'ACERTOU', 'ERROU'] as const;
const filter = ref<(typeof FILTERS)[number]>('TODOS');

const filteredHistory = computed(() => {
  if (filter.value === 'TODOS') return history.value;
  // EM PROGRESSO = partida acontecendo agora (status no momento que o painel carregou).
  if (filter.value === 'EM PROGRESSO') return history.value.filter((g) => g.matchStatus === 'IN_PROGRESS');
  return history.value.filter((g) => guessVerdict(g) === (filter.value as GuessVerdict));
});

function openAdversaries(g: GuessHistoryEntry) {
  if (g.matchId) adversaries.show(g);
}

async function handleLogout() {
  try { await logout(); } catch {}
  clearUser();
  router.push('/login');
}
</script>

<template>
  <DesktopMe v-if="isDesktop" />
  <div v-else class="page-narrow" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <Masthead kicker="CARTEIRA DO JOGADOR" title="VOCÊ" sub="PERFIL · EXTRATO" />

    <!-- Player hero -->
    <div :style="{ padding: '18px 18px 6px' }">
      <div :style="{
        position: 'relative',
        background: 'var(--ink)', color: 'var(--paper)',
        border: '1.5px solid var(--ink)',
        boxShadow: `6px 6px 0 ${toneVar(me.tone)}, 6px 6px 0 1px var(--ink)`,
        borderRadius: '6px', overflow: 'hidden',
        display: 'flex', alignItems: 'center', gap: '16px',
        padding: '16px 18px',
      }">
        <div
          class="halftone"
          :style="{
            position: 'absolute', top: 0, right: 0, width: '120px', height: '28px',
            color: toneVar(me.tone), opacity: 0.5, pointerEvents: 'none',
          }"
        />
        <div
          class="font-display"
          :style="{
            width: '64px', height: '64px', flexShrink: 0,
            background: toneVar(me.tone), color: toneFg(me.tone),
            border: '2px solid var(--paper)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '28px', borderRadius: '6px',
          }"
        >{{ me.avatar }}</div>
        <div :style="{ flex: 1, minWidth: 0 }">
          <div class="font-display" :style="{ fontSize: '30px', lineHeight: 0.95, textTransform: 'uppercase' }">
            {{ me.name }}
          </div>
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em', opacity: 0.7, marginTop: '4px' }">
            {{ me.handle }} · {{ me.exactCount }} CRAVADOS · {{ me.totalGuesses }} PALPITES
          </div>
        </div>
      </div>
    </div>

    <!-- Aggregate stats grid · grid centralizado (gutters iguais dos dois lados) -->
    <div :style="{ padding: '14px 18px 6px', display: 'grid', gridTemplateColumns: 'repeat(2, minmax(0, 1fr))', gap: '12px', maxWidth: '340px', margin: '0 auto', justifyContent: 'center' }">
      <div
        v-for="s in stats"
        :key="s.label"
        :style="{
          position: 'relative',
          background: 'var(--paper-2)',
          border: '1.5px solid var(--ink)',
          boxShadow: `4px 4px 0 ${toneVar(s.tone)}, 4px 4px 0 1px var(--ink)`,
          borderRadius: '6px', padding: '12px 13px 11px',
        }"
      >
        <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.13em', fontWeight: 700, opacity: 0.7 }">
          {{ s.label }}
        </div>
        <div
          class="font-display"
          :style="{
            fontSize: '40px', lineHeight: 0.9, marginTop: '6px',
            color: toneVar(s.tone), WebkitTextStroke: '1px var(--ink)',
          }"
        >{{ s.value }}</div>
        <div
          class="font-mono"
          :style="{
            fontSize: '9px', letterSpacing: '0.08em', opacity: 0.6, marginTop: '6px',
            whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
          }"
        >{{ s.sub }}</div>
      </div>
    </div>

    <!-- Guess history (extrato) -->
    <div :style="{ padding: '16px 18px 6px' }">
      <SectionHead :kicker="`EXTRATO · ${history.length} ÚLTIMOS`" title="Seus palpites" />
    </div>
    <div :style="{ display: 'flex', gap: '6px', padding: '4px 18px 4px', overflowX: 'auto', alignItems: 'center' }">
      <ChipToggle
        v-for="f in FILTERS"
        :key="f"
        :active="filter === f"
        @click="filter = f"
      >{{ f }}</ChipToggle>
    </div>
    <div :style="{ padding: '8px 18px 4px', display: 'flex', flexDirection: 'column', gap: '10px' }">
      <GuessHistoryRow
        v-for="(g, i) in filteredHistory"
        :key="i"
        :g="g"
        @click="openAdversaries(g)"
      />
      <div
        v-if="!loading && !filteredHistory.length"
        class="font-mono"
        :style="{
          padding: '18px 16px', textAlign: 'center', fontSize: '12px',
          letterSpacing: '0.08em', opacity: 0.6,
          background: 'var(--paper-2)', border: '1.5px dashed var(--ink)', borderRadius: '6px',
        }"
      >{{ history.length ? 'Nenhum palpite nesse filtro.' : 'Nenhum palpite ainda. Bora cravar o primeiro!' }}</div>
    </div>

    <!-- Scoring rule footer -->
    <div :style="{
      margin: '14px 18px 22px', padding: '14px 16px',
      background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
      borderRadius: '6px', position: 'relative',
    }">
      <div
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--magenta)', marginBottom: '4px' }"
      >COMO PONTUA</div>
      <div :style="{ fontSize: '13px', lineHeight: 1.5 }">
        Placar cravado <b>+3</b> · vencedor/empate <b>+1</b> · errou <b>0</b>.
      </div>
    </div>

    <!-- Logout -->
    <div :style="{ padding: '0 18px 28px' }">
      <button
        class="font-display press"
        :style="{
          width: '100%', padding: '13px 16px',
          background: 'var(--paper-2)', color: 'var(--ink)',
          border: '1.5px solid var(--ink)',
          boxShadow: '4px 4px 0 var(--coral), 4px 4px 0 1px var(--ink)',
          borderRadius: '6px', cursor: 'pointer',
          fontSize: '15px', letterSpacing: '0.06em', textTransform: 'uppercase',
        }"
        @click="handleLogout"
      >Sair da conta →</button>
    </div>
  </div>
</template>

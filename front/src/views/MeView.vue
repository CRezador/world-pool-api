<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import Masthead from '@/components/Masthead.vue';
import SectionHead from '@/components/SectionHead.vue';
import GuessHistoryRow from '@/components/pool/GuessHistoryRow.vue';
import DesktopMe from '@/components/pool/DesktopMe.vue';
import { ME, ME_HISTORY, toneVar, toneFg } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';

const router = useRouter();
const { isDesktop } = useBreakpoint();

const me = ME;
const history = ME_HISTORY;

const stats = [
  { label: 'PONTOS · TEMPORADA', value: me.seasonPoints, tone: 'magenta', sub: 'somados em 3 bolões' },
  { label: 'APROVEITAMENTO', value: me.hitRate + '%', tone: 'cobalt', sub: 'palpites que pontuaram' },
  { label: 'SEQUÊNCIA ATUAL', value: me.streak, tone: 'lime', sub: 'palpites seguidos no alvo' },
  { label: 'MELHOR POSIÇÃO', value: me.bestRank + 'º', tone: 'coral', sub: me.bestPool },
];

function openMatch(matchId: number) {
  if (matchId) router.push(`/match/${matchId}`);
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

    <!-- Aggregate stats grid -->
    <div :style="{ padding: '14px 18px 6px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }">
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
    <div :style="{ padding: '8px 18px 4px', display: 'flex', flexDirection: 'column', gap: '10px' }">
      <GuessHistoryRow
        v-for="(g, i) in history"
        :key="i"
        :g="g"
        @click="openMatch(g.matchId)"
      />
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
  </div>
</template>

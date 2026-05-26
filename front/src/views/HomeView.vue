<script setup lang="ts">
import { useRouter } from 'vue-router';
import Masthead from '@/components/Masthead.vue';
import SectionHead from '@/components/SectionHead.vue';
import PrintButton from '@/components/PrintButton.vue';
import LiveTicker from '@/components/LiveTicker.vue';
import PoolCard from '@/components/pool/PoolCard.vue';
import MatchCarousel from '@/components/match/MatchCarousel.vue';
import { MATCHES, POOLS } from '@/data/mock';
import { useJoinModal } from '@/composables/useJoinModal';

const router = useRouter();
const join = useJoinModal();
const liveMatch = MATCHES.find(m => m.status === 'IN_PROGRESS');
</script>

<template>
  <div class="page-narrow" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <Masthead />
    <LiveTicker v-if="liveMatch" :match="liveMatch" />

    <div :style="{ padding: '18px 18px 8px' }">
      <SectionHead kicker="MEUS BOLÕES · 3 ATIVOS" title="Onde você joga" />
    </div>

    <div :style="{ padding: '6px 18px', display: 'flex', flexDirection: 'column', gap: '18px' }">
      <PoolCard
        v-for="(p, i) in POOLS"
        :key="p.id"
        :pool="p"
        :index="i + 1"
        @click="router.push(`/pool/${p.id}`)"
      />
    </div>

    <div :style="{ padding: '18px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }">
      <PrintButton tone="cobalt" size="sm" @click="join.show()">
        + Entrar c/ código
      </PrintButton>
      <PrintButton tone="lime" size="sm" @click="router.push('/create')">
        + Criar bolão
      </PrintButton>
    </div>

    <MatchCarousel />

    <div :style="{
      padding: '18px',
      background: 'var(--paper-2)',
      borderTop: '1.5px solid var(--ink)',
      position: 'relative',
    }">
      <div :style="{
        position: 'absolute', top: '-1px', right: '18px',
        color: 'var(--magenta)', height: '6px', width: '80px',
      }">
        <div class="halftone" :style="{ height: '100%' }" />
      </div>
      <div
        class="font-mono"
        :style="{
          fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700,
          color: 'var(--magenta)', marginBottom: '4px',
        }"
      >REGRA DA CASA</div>
      <div class="font-display" :style="{ fontSize: '22px', lineHeight: 1, marginBottom: '8px' }">
        PLACAR EXATO PAGA 3.
      </div>
      <div :style="{ fontSize: '13px', lineHeight: 1.4 }">
        Acertou o vencedor ou empate? <b>+1 ponto</b>.<br />
        Acertou o placar cravado? <b>+3 pontos</b>.<br />
        Errou tudo? <b>Próximo jogo, mestre.</b>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Masthead from '@/components/Masthead.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import MyPoolCard from '@/components/pool/MyPoolCard.vue';
import DesktopMyPools from '@/components/pool/DesktopMyPools.vue';
import { toneVar } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';
import { usePools } from '@/composables/usePools';
import { useLeaderboard } from '@/composables/useLeaderboard';

const router = useRouter();
const { isDesktop } = useBreakpoint();
const join   = useJoinModal();
const create = useCreatePoolModal();
const { pools: allPools, fetchMyPools } = usePools();
const { myStats, fetchMyStats } = useLeaderboard();

onMounted(() => Promise.all([fetchMyPools(), fetchMyStats()]).catch(() => {}));

const filters = ['TODOS', 'PRIVADOS', 'PÚBLICOS'];
const filter = ref('TODOS');

const pools = computed(() => allPools.value.filter(p => {
  if (filter.value === 'PRIVADOS') return !p.isPublic;
  if (filter.value === 'PÚBLICOS') return p.isPublic;
  return true;
}));

const stats = computed(() => [
  { l: 'BOLÕES',      v: myStats.value ? String(myStats.value.poolsCount)       : '—', tone: 'magenta' },
  { l: 'PTS TOTAIS',  v: myStats.value ? String(myStats.value.totalPoints)      : '—', tone: 'cobalt' },
  { l: 'CRAVADAS',    v: myStats.value ? String(myStats.value.totalExactHits)   : '—', tone: 'lime' },
  { l: 'MELHOR POS.', v: myStats.value?.bestRank ? myStats.value.bestRank + 'º' : '—', tone: 'coral', small: true },
]);
</script>

<template>
  <DesktopMyPools v-if="isDesktop" />
  <div v-else class="page-narrow" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <Masthead
      kicker="EDIÇÃO Nº 04 · MEUS BOLÕES"
      title="MEUS BOLÕES"
      :sub="`${allPools.length} ATIVOS`"
    />

    <div :style="{
      display: 'flex',
      borderBottom: '1.5px solid var(--ink)',
      background: 'var(--paper-2)',
    }">
      <div
        v-for="(s, i) in stats"
        :key="s.l"
        :style="{
          flex: 1, padding: '14px 8px 12px',
          position: 'relative',
          borderRight: i < stats.length - 1 ? '1px dashed var(--ink)' : 'none',
          textAlign: 'center',
        }"
      >
        <div
          class="font-mono"
          :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }"
        >{{ s.l }}</div>
        <div
          class="font-display"
          :style="{ fontSize: s.small ? '24px' : '30px', lineHeight: 1, marginTop: '4px' }"
        >{{ s.v }}</div>
        <div :style="{
          position: 'absolute', bottom: 0, left: '12px', right: '12px', height: '3px',
          background: toneVar(s.tone),
        }" />
      </div>
    </div>

    <div
      class="no-scrollbar"
      :style="{
        display: 'flex', gap: '6px', padding: '12px 18px 8px',
        overflowX: 'auto', alignItems: 'center',
      }"
    >
      <span
        class="font-mono"
        :style="{
          fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700,
          opacity: 0.6, marginRight: '4px',
        }"
      >FILTRAR</span>
      <ChipToggle
        v-for="f in filters"
        :key="f"
        :active="filter === f"
        @click="filter = f"
      >{{ f }}</ChipToggle>
    </div>

    <div :style="{ padding: '8px 18px 4px', display: 'flex', flexDirection: 'column', gap: '16px' }">
      <MyPoolCard
        v-for="(p, i) in pools"
        :key="p.id"
        :pool="p"
        :index="i + 1"
        @click="router.push(`/pool/${p.id}`)"
      />

      <div
        v-if="pools.length === 0"
        :style="{
          padding: '22px', textAlign: 'center',
          border: '1.5px dashed var(--ink)', borderRadius: '4px',
          margin: '8px 0',
        }"
      >
        <div class="font-display" :style="{ fontSize: '18px', marginBottom: '4px' }">Nada por aqui</div>
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.7 }"
        >SEM BOLÕES {{ filter.toLowerCase() }}</div>
      </div>
    </div>

    <div :style="{
      padding: '14px 18px 8px',
      display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px',
    }">
      <button
        class="font-display press"
        :style="{
          padding: '14px 8px', cursor: 'pointer', width: '100%',
          background: 'var(--cobalt)', color: 'var(--paper)',
          border: '1.5px solid var(--ink)', boxShadow: '4px 4px 0 var(--ink)',
          fontSize: '15px', letterSpacing: '0.04em', borderRadius: '4px',
        }"
        @click="join.show()"
      >+ C/ código</button>
      <button
        class="font-display press"
        :style="{
          padding: '14px 8px', cursor: 'pointer', width: '100%',
          background: 'var(--lime)', color: 'var(--ink)',
          border: '1.5px solid var(--ink)', boxShadow: '4px 4px 0 var(--ink)',
          fontSize: '15px', letterSpacing: '0.04em', borderRadius: '4px',
        }"
        @click="create.show()"
      >+ Criar bolão</button>
    </div>

    <div :style="{
      margin: '14px 18px 18px',
      padding: '16px',
      background: 'var(--ink)', color: 'var(--paper)',
      position: 'relative', overflow: 'hidden',
    }">
      <div :style="{
        position: 'absolute', top: 0, right: 0, width: '100px', height: '36px',
        color: 'var(--coral)',
      }">
        <div class="halftone" :style="{ height: '100%' }" />
      </div>
      <div
        class="font-mono"
        :style="{
          fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--lime)',
        }"
      >OPORTUNIDADE · BOLÕES ABERTOS</div>
      <div
        class="font-display"
        :style="{ fontSize: '22px', lineHeight: 1, marginTop: '6px', textTransform: 'uppercase' }"
      >Sobrando palpite?<br />Entra num público.</div>
      <div :style="{ fontSize: '12px', opacity: 0.75, marginTop: '8px', lineHeight: 1.4 }">
        8.241 bolões abertos · 124k sócios competindo agora.
      </div>
      <div :style="{ marginTop: '12px' }">
        <span
          class="font-display press"
          :style="{
            display: 'inline-block', cursor: 'pointer',
            background: 'var(--lime)', color: 'var(--ink)',
            border: '1.5px solid var(--paper)',
            padding: '8px 14px', fontSize: '13px', letterSpacing: '0.06em',
            textTransform: 'uppercase', borderRadius: '3px',
          }"
          @click="router.push('/explore')"
        >Explorar públicos →</span>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Masthead from '@/components/Masthead.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import PrintButton from '@/components/PrintButton.vue';
import MyPoolCard from '@/components/pool/MyPoolCard.vue';
import DesktopMyPools from '@/components/pool/DesktopMyPools.vue';
import ActivityFeed from '@/components/pool/ActivityFeed.vue';
import { toneVar } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useJoinModal } from '@/composables/useJoinModal';
import { usePools } from '@/composables/usePools';
import { useLeaderboard } from '@/composables/useLeaderboard';
import { useActivity } from '@/composables/useActivity';

const router = useRouter();
const { isDesktop } = useBreakpoint();
const join = useJoinModal();
const { pools: allPools, fetchMyPools } = usePools();
const { myStats, fetchMyStats } = useLeaderboard();
const { activity, fetchActivity } = useActivity();

onMounted(() => Promise.all([fetchMyPools(), fetchMyStats(), fetchActivity()]));

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

const lastActivityByPool = computed(() => {
  const map: Record<string, { result: string; detail: string; tone: string }> = {};
  for (const item of activity.value) {
    const key = String(item.poolId);
    if (!map[key]) {
      const pts = item.points;
      map[key] = {
        result: pts === null ? '?' : pts > 0 ? `+${pts}` : '0',
        detail: `${item.subject} · ${item.action}`,
        tone: pts === 3 ? 'lime' : pts === 1 ? 'cobalt' : 'paper-3',
      };
    }
  }
  return map;
});
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
      <span class="font-mono" :style="{ marginLeft: 'auto' }">
        <span :style="{
          fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700,
          padding: '6px 8px', border: '1.5px solid var(--ink)', borderRadius: '3px',
        }">ATIVIDADE ▾</span>
      </span>
    </div>

    <div :style="{ padding: '8px 18px 4px', display: 'flex', flexDirection: 'column', gap: '16px' }">
      <MyPoolCard
        v-for="(p, i) in pools"
        :key="p.id"
        :pool="p"
        :index="i + 1"
        :activity="lastActivityByPool[p.id]"
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
      <PrintButton tone="cobalt" @click="join.show()">+ C/ código</PrintButton>
      <PrintButton tone="lime" @click="router.push('/create')">+ Criar bolão</PrintButton>
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

    <div :style="{ borderTop: '1.5px solid var(--ink)', marginTop: '18px', paddingTop: '18px' }">
      <ActivityFeed :items="activity" />
    </div>
  </div>
</template>

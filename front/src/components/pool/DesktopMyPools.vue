<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import ChipToggle from '@/components/ChipToggle.vue';
import DesktopHero from '@/components/DesktopHero.vue';
import DesktopPoolCard from './DesktopPoolCard.vue';
import DesktopUpcomingMatches from './DesktopUpcomingMatches.vue';
import { toneVar } from '@/data/mock';
import { usePools } from '@/composables/usePools';
import { useLeaderboard } from '@/composables/useLeaderboard';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';
import DesktopActivityFeed from './DesktopActivityFeed.vue';

const router = useRouter();
const join = useJoinModal();
const create = useCreatePoolModal();

// Os dados (pools + stats) são carregados pelo pai MyPoolsView no onMounted;
// aqui só lemos os refs compartilhados do composable para evitar fetch duplicado.
const { pools: allPools } = usePools();
const { myStats } = useLeaderboard();

const filters = ['TODOS', 'PRIVADOS', 'PÚBLICOS', 'ATIVOS HOJE'];
const filter = ref('TODOS');

const pools = computed(() => allPools.value.filter(p => {
  if (filter.value === 'PRIVADOS') return !p.isPublic;
  if (filter.value === 'PÚBLICOS') return p.isPublic;
  return true;
}));

const hitRate = computed(() => {
  if (!myStats.value || !myStats.value.totalGuesses) return null;
  return Math.round(((myStats.value.totalExactHits + myStats.value.totalResultHits) / myStats.value.totalGuesses) * 100);
});

const stats = computed(() => [
  { value: myStats.value ? String(myStats.value.poolsCount)      : String(allPools.value.length), layer: 'BOLÕES',      tone: 'magenta' },
  { value: myStats.value ? String(myStats.value.totalPoints)     : '—',                           layer: 'PTS TOTAIS',  tone: 'cobalt'  },
  { value: myStats.value ? String(myStats.value.totalExactHits)  : '—',                           layer: 'CRAVADAS',    tone: 'lime'    },
  { value: myStats.value?.bestRank ? myStats.value.bestRank + 'º': '—',                           layer: 'MELHOR POS.', tone: 'coral'   },
  { value: hitRate.value !== null ? hitRate.value + '%'          : '—',                           layer: 'PALPITADOS',  tone: 'ink', small: true },
]);

</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">
    <DesktopHero
      kicker="EDIÇÃO Nº 04 · BOLETIM DO SÓCIO"
      title="Meus bolões"
      sub="ONDE VOCÊ JOGA · CONTRA QUEM · POR QUANTO"
      tone="magenta"
    >
      <div :style="{
        display: 'flex', gap: 0,
        border: '1.5px solid var(--ink)',
        background: 'var(--paper-2)',
      }">
        <div
          v-for="(stat, i) in stats"
          :key="stat.layer"
          :style="{
            padding: '14px 20px', minWidth: '108px',
            borderRight: i < stats.length - 1 ? '1px dashed var(--ink)' : 'none',
            position: 'relative',
          }"
        >
          <div
            class="font-mono"
            :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }"
          >{{ stat.layer }}</div>
          <div
            class="font-display"
            :style="{ fontSize: stat.small ? '28px' : '36px', lineHeight: 1, marginTop: '4px' }"
          >{{ stat.value }}</div>
          <div :style="{
            position: 'absolute', bottom: 0, left: 0, right: 0, height: '4px',
            background: toneVar(stat.tone),
          }" />
        </div>
      </div>
    </DesktopHero>

    <div :style="{
      display: 'flex', gap: '10px', padding: '14px 32px',
      borderBottom: '1.5px solid var(--ink)', alignItems: 'center',
    }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, marginRight: '4px' }"
      >FILTROS</span>
      <ChipToggle
        v-for="f in filters"
        :key="f"
        :active="filter === f"
        @click="filter = f"
      >{{ f }}</ChipToggle>
      <span
        class="font-mono"
        :style="{ marginLeft: 'auto', display: 'flex', gap: '10px', alignItems: 'center' }"
      >
        <button
          class="font-display press"
          :style="{
            background: 'transparent', color: 'var(--ink)',
            border: '1.5px solid var(--ink)',
            padding: '7px 12px', fontSize: '12px', letterSpacing: '0.06em',
            textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          }"
          @click="join.show()"
        >+ C/ código</button>
        <button
          class="font-display press"
          :style="{
            background: 'var(--lime)', color: 'var(--ink)',
            border: '1.5px solid var(--ink)',
            boxShadow: '3px 3px 0 var(--ink)',
            padding: '7px 14px', fontSize: '12px', letterSpacing: '0.06em',
            textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          }"
          @click="create.show()"
        >+ Novo bolão</button>
      </span>
    </div>

    <div :style="{
      display: 'grid', gridTemplateColumns: '2.1fr 1fr', gap: 0, flex: 1,
    }">
      <div :style="{ padding: '22px 28px', borderRight: '1.5px solid var(--ink)' }">
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '14px' }"
        >{{ pools.length }} BOLÕES · CLIQUE PARA ABRIR</div>
        <div :style="{ display: 'flex', flexDirection: 'column', gap: '16px' }">
          <DesktopPoolCard
            v-for="(p, i) in pools"
            :key="p.id"
            :pool="p"
            :index="i + 1"
            @click="router.push(`/pool/${p.id}`)"
          />
          <div v-if="pools.length === 0" :style="{
            padding: '30px', textAlign: 'center',
            border: '1.5px dashed var(--ink)', borderRadius: '4px',
          }">
            <div class="font-display" :style="{ fontSize: '22px', marginBottom: '4px' }">Nada por aqui</div>
            <div
              class="font-mono"
              :style="{ fontSize: '11px', letterSpacing: '0.1em', opacity: 0.7 }"
            >SEM BOLÕES {{ filter.toLowerCase() }}</div>
          </div>
        </div>
      </div>

      <div :style="{ padding: '22px 24px', background: 'var(--paper-2)' }">
        <DesktopUpcomingMatches />

        <DesktopActivityFeed />
      </div>
    </div>
  </div>
</template>

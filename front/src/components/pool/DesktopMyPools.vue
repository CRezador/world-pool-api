<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import ChipToggle from '@/components/ChipToggle.vue';
import DesktopPoolCard from './DesktopPoolCard.vue';
import DesktopUpcomingMatches from './DesktopUpcomingMatches.vue';
import { toneVar } from '@/data/mock';
import { usePools } from '@/composables/usePools';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';
import DesktopActivityFeed from './DesktopActivityFeed.vue';

const router = useRouter();
const join = useJoinModal();
const create = useCreatePoolModal();

const { pools: allPools, fetchMyPools } = usePools();

onMounted(() => fetchMyPools());

const filters = ['TODOS', 'PRIVADOS', 'PÚBLICOS', 'ATIVOS HOJE'];
const filter = ref('TODOS');

const pools = computed(() => allPools.value.filter(p => {
  
  if (filter.value === 'PRIVADOS'){
    console.log(p.isPublic);
    return !p.isPublic;
  }
  if (filter.value === 'PÚBLICOS') return p.isPublic;
  return true;
}));

const totalExacts = 8;
const guessRate = 0.86;
const totalPts = computed(() => allPools.value.reduce((s, p) => s + p.myPoints, 0));
const bestRank = computed(() => allPools.value.length ? Math.min(...allPools.value.map(p => p.myRank)) : 0);

const stats = computed(() => [
  { value: String(allPools.value.length),              layer: 'BOLÕES',       tone: 'magenta' },
  { value: String(totalPts.value),                     layer: 'PTS TOTAIS',   tone: 'cobalt' },
  { value: String(totalExacts),                        layer: 'CRAVADAS',     tone: 'lime' },
  { value: bestRank.value + 'º',                       layer: 'MELHOR POS.',  tone: 'coral' },
  { value: Math.round(guessRate * 100) + '%',          layer: 'PALPITADOS',   tone: 'ink', small: true },
]);


</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">
    <div :style="{
      padding: '28px 32px 22px',
      borderBottom: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'flex-end', gap: '28px', position: 'relative',
    }">
      <div :style="{ flex: 1 }">
        <div
          class="font-mono"
          :style="{ fontSize: '11px', letterSpacing: '0.22em', fontWeight: 700 }"
        >EDIÇÃO Nº 04 · BOLETIM DO SÓCIO</div>
        <div
          class="font-display misprint-magenta"
          :style="{
            fontSize: '86px', lineHeight: 0.88, marginTop: '6px',
            textTransform: 'uppercase', letterSpacing: '0.005em',
          }"
        >Meus bolões</div>
        <div
          class="font-mono"
          :style="{ fontSize: '12px', letterSpacing: '0.14em', marginTop: '14px', opacity: 0.85 }"
        >ONDE VOCÊ JOGA · CONTRA QUEM · POR QUANTO</div>
      </div>

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
    </div>

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
        <span :style="{ fontSize: '10px', letterSpacing: '0.16em', opacity: 0.6 }">ORDENAR</span>
        <span :style="{
          fontSize: '11px', letterSpacing: '0.12em', fontWeight: 700,
          padding: '4px 8px', border: '1.5px solid var(--ink)', cursor: 'pointer',
        }">ATIVIDADE ▾</span>
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

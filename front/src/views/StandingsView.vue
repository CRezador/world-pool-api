<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import SectionHead from '@/components/SectionHead.vue';
import Stamp from '@/components/Stamp.vue';
import DesktopStandings from '@/components/standings/DesktopStandings.vue';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useStandings } from '@/composables/useStandings';

const { isDesktop } = useBreakpoint();
const { standings, fetchStandings } = useStandings();

const group = ref('');

onMounted(async () => {
  await fetchStandings();
  if (standings.value.length && !group.value) {
    group.value = standings.value[0].group;
  }
});

watch(standings, (val) => {
  if (val.length && !group.value) group.value = val[0].group;
});

const focusGroup = computed(() =>
  standings.value.find(g => g.group === group.value) ?? standings.value[0],
);

const roundsPlayed = computed(() => focusGroup.value?.table[0]?.played ?? 0);

function diffStr(n: number) {
  return n > 0 ? `+${n}` : `${n}`;
}
</script>

<template>
  <DesktopStandings v-if="isDesktop" />
  <div v-else class="page-narrow wide" :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 12px', borderBottom: '1.5px solid var(--ink)' }">
      <SectionHead kicker="TABELA OFICIAL · FIFA WC 26" title="Classificação real">
        <template #action>
          <Stamp tone="cobalt" :rotate="-4">FOOTBALL-DATA.ORG</Stamp>
        </template>
      </SectionHead>
    </div>

    <div
      class="no-scrollbar"
      :style="{
        display: 'flex', gap: '6px', padding: '12px 18px', overflowX: 'auto',
        borderBottom: '1.5px solid var(--ink)',
      }"
    >
      <button
        v-for="g in standings"
        :key="g.group"
        class="font-display"
        :style="{
          minWidth: '40px', height: '40px',
          background: group === g.group ? 'var(--ink)' : 'var(--paper-2)',
          color: group === g.group ? 'var(--lime)' : 'var(--ink)',
          border: '1.5px solid var(--ink)',
          fontSize: '18px', cursor: 'pointer',
        }"
        @click="group = g.group"
      >{{ g.group }}</button>
    </div>

    <div :style="{ padding: '18px' }">
      <div v-if="!focusGroup" class="font-mono" :style="{ fontSize: '12px', opacity: 0.5 }">
        CARREGANDO...
      </div>
      <template v-else>
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, marginBottom: '8px' }"
        >GRUPO {{ focusGroup.group }} · {{ roundsPlayed }} {{ roundsPlayed === 1 ? 'RODADA DISPUTADA' : 'RODADAS DISPUTADAS' }}</div>

        <table :style="{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }">
          <thead>
            <tr :style="{ background: 'var(--ink)', color: 'var(--paper)' }">
              <th
                v-for="(h, i) in ['#','SELEÇÃO','P','V','E','D','GP','GC','SG','PTS']"
                :key="h"
                class="font-mono"
                :style="{
                  padding: '6px 4px', fontSize: '9px', letterSpacing: '0.1em',
                  fontWeight: 700, textAlign: i < 2 ? 'left' : 'center',
                }"
              >{{ h }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, i) in focusGroup.table"
              :key="row.code"
              :style="{
                borderBottom: '1px solid var(--ink)',
                background: i < 2 ? 'var(--paper-2)' : 'transparent',
              }"
            >
              <td class="font-display" :style="{ padding: '8px 6px', fontSize: '16px' }">
                {{ i + 1 }}
                <span
                  :style="{ marginLeft: '4px', color: i < 2 ? 'var(--lime)' : 'var(--coral)' }"
                >{{ i < 2 ? '●' : '✕' }}</span>
              </td>
              <td :style="{ padding: '8px 4px' }">
                <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
                  <img
                    :src="row.crest"
                    :alt="row.team"
                    :style="{
                      width: '27px', height: '18px',
                      objectFit: 'cover', borderRadius: '2px',
                      border: '1px solid var(--ink)',
                    }"
                  />
                  <span class="font-display" :style="{ fontSize: '14px' }">{{ row.code }}</span>
                </div>
              </td>
              <td
                v-for="k in (['played','won','draw','lost','goals_for','goals_against'] as const)"
                :key="k"
                class="font-mono"
                :style="{ padding: '8px 0', textAlign: 'center', fontSize: '12px' }"
              >{{ row[k] }}</td>
              <td
                class="font-mono"
                :style="{ padding: '8px 0', textAlign: 'center', fontSize: '12px', fontWeight: 700 }"
              >{{ diffStr(row.goal_diff) }}</td>
              <td
                class="font-display"
                :style="{ padding: '8px 0', textAlign: 'center', fontSize: '18px' }"
              >{{ row.points }}</td>
            </tr>
          </tbody>
        </table>

        <div
          class="font-mono"
          :style="{ fontSize: '10px', opacity: 0.6, marginTop: '10px', letterSpacing: '0.08em' }"
        >● Classificado para oitavas · ✕ Eliminado</div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import SectionHead from '@/components/SectionHead.vue';
import Stamp from '@/components/Stamp.vue';
import FlagImg from '@/components/FlagImg.vue';
import DesktopStandings from '@/components/standings/DesktopStandings.vue';
import { STANDINGS_C, TEAMS } from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';

const { isDesktop } = useBreakpoint();

const groups = ['A','B','C','D','E','F','G','H','I','J','K','L'];
const group = ref('C');
const data = STANDINGS_C; // mobile demo only shows group C
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
        v-for="g in groups"
        :key="g"
        class="font-display"
        :style="{
          minWidth: '40px', height: '40px',
          background: group === g ? 'var(--ink)' : 'var(--paper-2)',
          color: group === g ? 'var(--lime)' : 'var(--ink)',
          border: '1.5px solid var(--ink)',
          fontSize: '18px', cursor: 'pointer',
        }"
        @click="group = g"
      >{{ g }}</button>
    </div>

    <div :style="{ padding: '18px' }">
      <div
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, marginBottom: '8px' }"
      >GRUPO {{ group }} · 2 RODADAS DISPUTADAS</div>

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
            v-for="(row, i) in data"
            :key="row.team"
            :style="{
              borderBottom: '1px solid var(--ink)',
              background: i < 2 ? 'var(--paper-2)' : 'transparent',
            }"
          >
            <td class="font-display" :style="{ padding: '8px 6px', fontSize: '16px' }">
              {{ i + 1 }}
              <span v-if="i < 2" :style="{ marginLeft: '4px', color: 'var(--lime)' }">●</span>
            </td>
            <td :style="{ padding: '8px 4px' }">
              <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
                <FlagImg :team="row.team" :size="18" :radius="2" />
                <span class="font-display" :style="{ fontSize: '14px' }">{{ TEAMS[row.team].code }}</span>
              </div>
            </td>
            <td
              v-for="k in (['P','V','E','D','GP','GC'] as const)"
              :key="k"
              class="font-mono"
              :style="{ padding: '8px 0', textAlign: 'center', fontSize: '12px' }"
            >{{ row[k] }}</td>
            <td
              class="font-mono"
              :style="{ padding: '8px 0', textAlign: 'center', fontSize: '12px', fontWeight: 700 }"
            >{{ row.GP - row.GC > 0 ? '+' : '' }}{{ row.GP - row.GC }}</td>
            <td
              class="font-display"
              :style="{ padding: '8px 0', textAlign: 'center', fontSize: '18px' }"
            >{{ row.pts }}</td>
          </tr>
        </tbody>
      </table>
      <div
        class="font-mono"
        :style="{ fontSize: '10px', opacity: 0.6, marginTop: '10px', letterSpacing: '0.08em' }"
      >● Classificado para oitavas · atualizado há 2 min</div>
    </div>
  </div>
</template>

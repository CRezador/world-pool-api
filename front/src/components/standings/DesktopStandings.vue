<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import Stamp from '@/components/Stamp.vue';
import { toneVar } from '@/data/mock';
import { useStandings } from '@/composables/useStandings';

// Os dados são carregados pelo pai StandingsView; aqui só lemos o ref
// compartilhado (singleton do composable) para evitar fetch duplicado.
const { standings } = useStandings();

const focus = ref('');

onMounted(() => {
  if (standings.value.length && !focus.value) {
    focus.value = standings.value[0].group;
  }
});

watch(standings, (val) => {
  if (val.length && !focus.value) focus.value = val[0].group;
});

const focusGroup = computed(() =>
  standings.value.find(g => g.group === focus.value) ?? standings.value[0],
);

const totalTeams = computed(() => standings.value.reduce((s, g) => s + g.table.length, 0));
const totalQualified = computed(() => standings.value.length * 2);
const totalMatches = computed(() =>
  standings.value.reduce((s, g) => s + (g.table[0]?.played ?? 0), 0),
);

const stats = computed(() => [
  { v: totalTeams.value,     l: 'SELEÇÕES',     tone: 'magenta' },
  { v: totalQualified.value, l: 'VAGAS DIRETAS', tone: 'lime' },
  { v: totalMatches.value,   l: 'JOGOS RODADOS', tone: 'coral' },
]);

function diffStr(n: number) {
  return n > 0 ? `+${n}` : `${n}`;
}
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
        >CLASSIFICAÇÃO REAL · COPA 26 · {{ standings.length }} GRUPOS</div>
        <div
          class="font-display misprint-cobalt"
          :style="{
            fontSize: '86px', lineHeight: 0.88, marginTop: '6px',
            textTransform: 'uppercase', letterSpacing: '0.005em',
          }"
        >Tabela oficial</div>
        <div :style="{ display: 'flex', gap: '12px', marginTop: '14px', alignItems: 'center' }">
          <Stamp tone="cobalt" :rotate="-3">DADOS AO VIVO</Stamp>
          <span
            class="font-mono"
            :style="{ fontSize: '11px', letterSpacing: '0.14em', opacity: 0.85 }"
          >FONTE FOOTBALL-DATA.ORG</span>
        </div>
      </div>

      <div :style="{
        display: 'flex', gap: 0,
        border: '1.5px solid var(--ink)',
        background: 'var(--paper-2)',
      }">
        <div
          v-for="(s, i) in stats"
          :key="s.l"
          :style="{
            padding: '14px 20px', minWidth: '108px',
            borderRight: i < stats.length - 1 ? '1px dashed var(--ink)' : 'none',
            position: 'relative',
          }"
        >
          <div
            class="font-mono"
            :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }"
          >{{ s.l }}</div>
          <div class="font-display" :style="{ fontSize: '36px', lineHeight: 1, marginTop: '4px' }">
            {{ s.v }}
          </div>
          <div :style="{
            position: 'absolute', bottom: 0, left: 0, right: 0, height: '4px',
            background: toneVar(s.tone),
          }" />
        </div>
      </div>
    </div>

    <div :style="{
      display: 'flex', gap: '6px', padding: '14px 32px',
      borderBottom: '1.5px solid var(--ink)', alignItems: 'center',
    }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, marginRight: '6px' }"
      >GRUPO</span>
      <button
        v-for="g in standings"
        :key="g.group"
        class="font-display press"
        :style="{
          minWidth: '40px', height: '36px',
          background: focus === g.group ? 'var(--ink)' : 'var(--paper-2)',
          color: focus === g.group ? 'var(--lime)' : 'var(--ink)',
          border: '1.5px solid var(--ink)',
          fontSize: '16px', cursor: 'pointer', borderRadius: '3px',
          boxShadow: focus === g.group ? '2px 2px 0 var(--magenta)' : 'none',
        }"
        @click="focus = g.group"
      >{{ g.group }}</button>
      <span class="font-mono" :style="{ marginLeft: 'auto', fontSize: '10px', letterSpacing: '0.14em', opacity: 0.7 }">
        ● CLASSIFICADO · ✕ ELIMINADO
      </span>
    </div>

    <div v-if="!focusGroup" :style="{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center' }">
      <span class="font-mono" :style="{ fontSize: '12px', opacity: 0.5 }">CARREGANDO...</span>
    </div>

    <div v-else :style="{ display: 'grid', gridTemplateColumns: '1.4fr 1fr', gap: 0, flex: 1 }">
      <div :style="{ padding: '24px 28px', borderRight: '1.5px solid var(--ink)' }">
        <div :style="{ display: 'flex', alignItems: 'flex-end', gap: '14px', marginBottom: '16px' }">
          <div
            class="font-display"
            :style="{
              fontSize: '110px', lineHeight: 0.85, color: 'var(--cobalt)',
              textShadow: '4px 4px 0 var(--ink)',
              padding: '0 18px', flexShrink: 0,
            }"
          >{{ focusGroup.group }}</div>
          <div :style="{ flex: 1 }">
            <div
              class="font-mono"
              :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700, opacity: 0.8 }"
            >GRUPO {{ focusGroup.group }} · DESTAQUE</div>
            <div
              class="font-display"
              :style="{ fontSize: '26px', lineHeight: 1, marginTop: '4px', textTransform: 'uppercase' }"
            >Quem passa do grupo</div>
            <div
              class="font-mono"
              :style="{ fontSize: '11px', letterSpacing: '0.1em', opacity: 0.7, marginTop: '6px' }"
            >2 VAGAS DIRETAS · MAIS 1 SE TERMINAR ENTRE OS 8 MELHORES 3ºs</div>
          </div>
        </div>

        <table :style="{ width: '100%', borderCollapse: 'collapse', border: '1.5px solid var(--ink)' }">
          <thead>
            <tr :style="{ background: 'var(--ink)', color: 'var(--paper)' }">
              <th
                v-for="(h, i) in ['#','SELEÇÃO','P','V','E','D','GP','GC','SG','PTS']"
                :key="h"
                class="font-mono"
                :style="{
                  padding: '8px 6px', fontSize: '10px', letterSpacing: '0.14em', fontWeight: 700,
                  textAlign: i === 1 ? 'left' : 'center',
                }"
              >{{ h }}</th>
            </tr>
          </thead>
          <tbody :style="{ background: 'var(--paper-2)' }">
            <tr
              v-for="(row, i) in focusGroup.table"
              :key="row.code"
              :style="{
                borderBottom: i < focusGroup.table.length - 1 ? '1px solid var(--ink)' : 'none',
                background: i < 2 ? 'rgba(212, 247, 92, 0.1)' : 'transparent',
              }"
            >
              <td :style="{ padding: '10px 6px', textAlign: 'center' }">
                <span class="font-display" :style="{ fontSize: '20px' }">{{ i + 1 }}</span>
                <span :style="{
                  marginLeft: '4px', fontSize: '11px',
                  color: i < 2 ? 'var(--lime)' : 'var(--coral)',
                }">{{ i < 2 ? '●' : '✕' }}</span>
              </td>
              <td :style="{ padding: '10px 4px' }">
                <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
                  <img
                    :src="row.crest"
                    alt=""
                    :style="{
                      width: '28px', height: '21px',
                      border: '1px solid var(--ink)', borderRadius: '2px',
                      objectFit: 'cover',
                    }"
                  />
                  <div>
                    <div class="font-display" :style="{ fontSize: '16px', lineHeight: 1 }">{{ row.code }}</div>
                    <div
                      class="font-mono"
                      :style="{ fontSize: '9px', letterSpacing: '0.08em', opacity: 0.7, marginTop: '2px' }"
                    >{{ row.team.toUpperCase() }}</div>
                  </div>
                </div>
              </td>
              <td
                v-for="k in (['played','won','draw','lost','goals_for','goals_against'] as const)"
                :key="k"
                class="font-mono"
                :style="{ textAlign: 'center', fontSize: '13px', padding: '10px 0' }"
              >{{ row[k] }}</td>
              <td
                class="font-mono"
                :style="{ textAlign: 'center', fontSize: '13px', padding: '10px 0', fontWeight: 700 }"
              >{{ diffStr(row.goal_diff) }}</td>
              <td
                class="font-display"
                :style="{
                  textAlign: 'center', fontSize: '28px', padding: '10px 6px',
                  color: i < 2 ? 'var(--lime)' : 'var(--ink)',
                }"
              >{{ row.points }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div :style="{ padding: '22px 24px', background: 'var(--paper-2)' }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700 }">
          OS {{ standings.length }} GRUPOS · TOQUE PARA ABRIR
        </div>
        <div
          class="font-display"
          :style="{ fontSize: '22px', marginTop: '2px', marginBottom: '14px', textTransform: 'uppercase' }"
        >o tabuleiro</div>
        <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }">
          <div
            v-for="g in standings"
            :key="g.group"
            :style="{
              cursor: 'pointer',
              background: g.group === focus ? 'var(--ink)' : 'var(--paper)',
              color: g.group === focus ? 'var(--paper)' : 'var(--ink)',
              border: '1.5px solid var(--ink)',
              boxShadow: g.group === focus ? '3px 3px 0 var(--magenta)' : '2px 2px 0 var(--ink)',
              padding: '8px 10px 6px',
              transition: 'box-shadow 0.14s ease',
            }"
            @click="focus = g.group"
          >
            <div :style="{
              display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: '4px',
            }">
              <div
                class="font-display"
                :style="{
                  fontSize: '20px', lineHeight: 1,
                  color: g.group === focus ? 'var(--lime)' : 'var(--ink)',
                }"
              >{{ g.group }}</div>
              <div
                class="font-mono"
                :style="{ fontSize: '8px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.6 }"
              >GRUPO</div>
            </div>
            <div
              v-for="(row, i) in g.table"
              :key="row.code"
              :style="{
                display: 'flex', alignItems: 'center', gap: '6px',
                padding: '3px 0',
                borderBottom: i < g.table.length - 1
                  ? `1px dashed ${g.group === focus ? 'rgba(242,233,210,0.3)' : 'var(--ink)'}`
                  : 'none',
              }"
            >
              <span
                class="font-display"
                :style="{ width: '12px', fontSize: '11px', opacity: 0.65, textAlign: 'center' }"
              >{{ i + 1 }}</span>
              <img
                :src="row.crest"
                alt=""
                :style="{
                  width: '16px', height: '12px',
                  border: `1px solid ${g.group === focus ? 'rgba(242,233,210,0.4)' : 'var(--ink)'}`,
                  borderRadius: '1px', objectFit: 'cover', flexShrink: 0,
                }"
              />
              <span class="font-display" :style="{ fontSize: '12px', flex: 1, lineHeight: 1 }">
                {{ row.code }}
              </span>
              <span :style="{
                fontSize: '10px',
                color: i < 2 ? 'var(--lime)' : 'transparent',
              }">{{ i < 2 ? '●' : '' }}</span>
              <span
                class="font-mono"
                :style="{ fontSize: '11px', fontWeight: 700, minWidth: '14px', textAlign: 'right' }"
              >{{ row.points }}</span>
            </div>
          </div>
        </div>

        <div :style="{
          marginTop: '18px', padding: '12px 14px',
          background: 'var(--ink)', color: 'var(--paper)',
          position: 'relative', overflow: 'hidden',
        }">
          <div :style="{
            position: 'absolute', top: 0, right: 0, width: '80px', height: '24px', color: 'var(--magenta)',
          }">
            <div class="halftone" :style="{ height: '100%' }" />
          </div>
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--lime)' }"
          >· COMO PASSAR</div>
          <div :style="{ fontSize: '12px', marginTop: '4px', lineHeight: 1.5 }">
            Os 2 primeiros de cada grupo vão direto às oitavas.<br />
            Os 8 melhores 3ºs colocados também garantem vaga.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import FlagImg from '@/components/FlagImg.vue';
import Avatar from '@/components/Avatar.vue';
import SectionHead from '@/components/SectionHead.vue';
import Stamp from '@/components/Stamp.vue';
import { LEADERBOARD, MEMBERS, STANDINGS_C, TEAMS, toneVar } from '@/data/mock';
import type { Pool } from '@/types';

const props = defineProps<{ pool: Pool }>();

const stats = computed(() => [
  { v: props.pool.myRank + 'º', l: 'SUA POS.', tone: 'lime' },
  { v: String(props.pool.myPoints), l: 'PTS', tone: 'magenta' },
  { v: '3', l: 'EXATOS', tone: 'cobalt' },
  { v: '12', l: 'ACERTOS', tone: 'coral' },
]);

const ranking = computed(() => LEADERBOARD.map(e => ({
  entry: e,
  member: MEMBERS.find(m => m.id === e.memberId)!,
})));
</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">
    <div :style="{
      padding: '28px 28px 20px', display: 'flex', gap: '28px', alignItems: 'flex-end',
      background: 'var(--paper)', borderBottom: '1.5px solid var(--ink)',
    }">
      <div :style="{ flex: 1 }">
        <div
          class="font-mono"
          :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '8px' }"
        >BOLÃO Nº 01 · DESDE MAI/26</div>
        <div
          class="font-display misprint-magenta"
          :style="{ fontSize: '80px', lineHeight: 0.88, letterSpacing: '0.01em', textTransform: 'uppercase' }"
        >{{ pool.name }}</div>
        <div :style="{ display: 'flex', gap: '14px', marginTop: '14px', alignItems: 'center' }">
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em' }">
            CÓDIGO <span :style="{ background: 'var(--ink)', color: 'var(--paper)', padding: '2px 8px' }">{{ pool.code }}</span>
          </div>
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em' }">
            {{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }} · {{ pool.members }} SÓCIOS
          </div>
          <Stamp tone="cobalt" :rotate="-4">RODADA 02 EM CURSO</Stamp>
        </div>
      </div>
      <div :style="{ display: 'flex', gap: 0, border: '1.5px solid var(--ink)' }">
        <div
          v-for="(s, i) in stats"
          :key="s.l"
          :style="{
            padding: '14px 22px', minWidth: '110px',
            background: 'var(--paper-2)',
            borderRight: i < 3 ? '1.5px solid var(--ink)' : 'none',
            position: 'relative',
          }"
        >
          <div
            class="font-mono"
            :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }"
          >{{ s.l }}</div>
          <div class="font-display" :style="{ fontSize: '38px', lineHeight: 1, marginTop: '4px' }">
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
      display: 'grid', gridTemplateColumns: '1.3fr 1fr', gap: '28px',
      padding: '28px', flex: 1,
    }">
      <div>
        <SectionHead kicker="RANKING DO BOLÃO" title="O PÓDIO HOJE">
          <template #action>
            <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.12em' }">↻ ATUALIZAR</span>
          </template>
        </SectionHead>
        <div :style="{ marginTop: '16px', background: 'var(--paper-2)', border: '1.5px solid var(--ink)' }">
          <div
            class="font-mono"
            :style="{
              display: 'grid', gridTemplateColumns: '40px 1fr 70px 70px 70px 60px',
              padding: '8px 14px', background: 'var(--ink)', color: 'var(--paper)',
            }"
          >
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'left' }">#</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'left' }">SÓCIO</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">EXATOS</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">ACERTOS</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">PALPITES</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">PTS</span>
          </div>
          <div
            v-for="(row, i) in ranking"
            :key="row.member.id"
            :style="{
              display: 'grid', gridTemplateColumns: '40px 1fr 70px 70px 70px 60px',
              padding: '12px 14px',
              borderBottom: i < ranking.length - 1 ? '1px solid var(--ink)' : 'none',
              background: row.member.isMe ? 'var(--lime)' : 'transparent',
              alignItems: 'center',
            }"
          >
            <span class="font-display" :style="{ fontSize: '22px' }">{{ i + 1 }}</span>
            <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
              <Avatar :member="row.member" :size="30" />
              <div>
                <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">
                  {{ row.member.name }}
                  <span
                    v-if="row.member.isMe"
                    class="font-mono"
                    :style="{ fontSize: '9px' }"
                  > · VOCÊ</span>
                </div>
                <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.6, marginTop: '1px' }">
                  {{ row.member.handle }}
                </div>
              </div>
            </div>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ row.entry.exact }}</span>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ row.entry.result }}</span>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ row.entry.guesses }}</span>
            <span class="font-display" :style="{ textAlign: 'center', fontSize: '22px' }">{{ row.entry.points }}</span>
          </div>
        </div>
      </div>

      <div :style="{ display: 'flex', flexDirection: 'column', gap: '20px' }">
        <div>
          <SectionHead kicker="PRÓXIMO JOGO · AMANHÃ" title="FRA × BEL" />
          <div :style="{ marginTop: '14px' }">
            <div
              class="perf-bottom"
              :style="{
                background: 'var(--paper-2)', border: '1.5px solid var(--ink)',
                boxShadow: '5px 5px 0 var(--cobalt), 5px 5px 0 1px var(--ink)',
                padding: '18px',
              }"
            >
              <div :style="{ display: 'flex', alignItems: 'center', justifyContent: 'space-around', gap: '12px' }">
                <div :style="{ textAlign: 'center' }">
                  <FlagImg team="FRA" :size="56" :radius="4" :style="{ boxShadow: '3px 3px 0 var(--ink)' }" />
                  <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">FRA</div>
                </div>
                <div :style="{ textAlign: 'center' }">
                  <div class="font-display" :style="{ fontSize: '32px', opacity: 0.6 }">VS</div>
                  <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', marginTop: '4px' }">
                    AMANHÃ · 16:00
                  </div>
                </div>
                <div :style="{ textAlign: 'center' }">
                  <FlagImg team="BEL" :size="56" :radius="4" :style="{ boxShadow: '3px 3px 0 var(--ink)' }" />
                  <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">BEL</div>
                </div>
              </div>
              <div :style="{
                marginTop: '14px', padding: '10px 12px',
                background: 'var(--ink)', color: 'var(--paper)',
                display: 'flex', justifyContent: 'space-between', alignItems: 'center',
              }">
                <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
                  SEU PALPITE · 2 × 0
                </span>
                <span class="font-display" :style="{ fontSize: '14px', color: 'var(--lime)' }">
                  EDITAR PALPITE →
                </span>
              </div>
            </div>
          </div>
        </div>

        <div>
          <SectionHead kicker="GRUPO C · TABELA REAL" title="Como está o grupo" />
          <table :style="{
            width: '100%', borderCollapse: 'collapse', marginTop: '14px',
            border: '1.5px solid var(--ink)',
          }">
            <thead>
              <tr :style="{ background: 'var(--ink)', color: 'var(--paper)' }">
                <th
                  v-for="(h, i) in ['#','TIME','P','V','E','D','PTS']"
                  :key="h"
                  class="font-mono"
                  :style="{
                    padding: '6px 8px', fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700,
                    textAlign: i < 2 ? 'left' : 'center',
                  }"
                >{{ h }}</th>
              </tr>
            </thead>
            <tbody :style="{ background: 'var(--paper-2)' }">
              <tr
                v-for="(row, i) in STANDINGS_C"
                :key="row.team"
                :style="{ borderBottom: i < STANDINGS_C.length - 1 ? '1px solid var(--ink)' : 'none' }"
              >
                <td class="font-display" :style="{ padding: '8px 8px', fontSize: '16px' }">
                  {{ i + 1 }}<span v-if="i < 2" :style="{ color: 'var(--lime)', marginLeft: '4px' }">●</span>
                </td>
                <td :style="{ padding: '8px 4px' }">
                  <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
                    <FlagImg :team="row.team" :size="16" :radius="2" />
                    <span class="font-display" :style="{ fontSize: '14px' }">{{ TEAMS[row.team].code }}</span>
                  </div>
                </td>
                <td
                  v-for="k in (['P','V','E','D'] as const)"
                  :key="k"
                  class="font-mono"
                  :style="{ textAlign: 'center', fontSize: '12px', padding: '8px 0' }"
                >{{ row[k] }}</td>
                <td class="font-display" :style="{ textAlign: 'center', fontSize: '18px' }">{{ row.pts }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

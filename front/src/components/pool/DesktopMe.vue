<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import FlagImg from '@/components/FlagImg.vue';
import type { GuessHistoryEntry } from '@/types';
import { TEAMS, toneVar, toneFg } from '@/data/mock';
import { useMe } from '@/composables/useMe';
import { usePools } from '@/composables/usePools';

const router = useRouter();
const { profile: me, history, resultCount, loading, loadMe } = useMe();
const { pools, fetchMyPools } = usePools();

onMounted(() => {
  loadMe();
  fetchMyPools().catch(() => {});
});

const heroStats = computed(() => [
  { v: me.value.seasonPoints, l: 'PTS · TEMPORADA', tone: 'magenta' },
  { v: me.value.hitRate + '%', l: 'APROVEITAMENTO', tone: 'cobalt' },
  { v: me.value.streak, l: 'SEQUÊNCIA', tone: 'lime' },
  { v: me.value.bestRank ? me.value.bestRank + 'º' : '—', l: 'MELHOR POS.', tone: 'coral' },
]);

const breakdown = computed(() => {
  const exact = me.value.exactCount;
  const result = resultCount.value;
  const errors = Math.max(0, me.value.totalGuesses - exact - result);
  return [
    { l: 'CRAVADOS', v: exact, tone: 'lime', note: '+3 cada' },
    { l: 'RESULTADO', v: result, tone: 'cobalt', note: '+1 cada' },
    { l: 'ERROS', v: errors, tone: 'coral', note: '0 pts' },
  ];
});
const breakdownTotal = computed(() => breakdown.value.reduce((s, b) => s + b.v, 0));

function rowMeta(g: GuessHistoryEntry) {
  const pending = g.status === 'pending';
  const tone = pending ? 'cobalt' : g.pts === 3 ? 'lime' : g.pts === 1 ? 'cobalt' : 'coral';
  const verdict = pending ? 'EM JOGO' : g.pts === 3 ? 'CRAVOU' : g.pts === 1 ? 'ACERTOU' : 'ERROU';
  return { pending, tone, verdict };
}

const code = (key: string) => TEAMS[key]?.code ?? key;
const cols = '1.3fr 1.6fr 0.7fr 0.7fr 0.9fr';
</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">
    <!-- Hero band — identidade do jogador + stats agregadas -->
    <div :style="{
      padding: '26px 32px 22px',
      borderBottom: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'flex-end', gap: '28px', position: 'relative',
    }">
      <div :style="{ display: 'flex', alignItems: 'center', gap: '22px', flex: 1 }">
        <div
          class="font-display"
          :style="{
            width: '96px', height: '96px', flexShrink: 0,
            background: toneVar(me.tone), color: 'var(--ink)',
            border: '2px solid var(--ink)', boxShadow: '6px 6px 0 var(--ink)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '44px', borderRadius: '8px',
          }"
        >{{ me.avatar }}</div>
        <div>
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.22em', fontWeight: 700 }">
            CARTEIRA DO JOGADOR · {{ me.handle }}
          </div>
          <div
            class="font-display misprint-magenta"
            :style="{ fontSize: '86px', lineHeight: 0.88, marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.005em' }"
          >{{ me.name }}</div>
          <div class="font-mono" :style="{ fontSize: '12px', letterSpacing: '0.14em', marginTop: '12px', opacity: 0.85 }">
            {{ me.exactCount }} PLACARES CRAVADOS · {{ me.totalGuesses }} PALPITES · {{ pools.length }} {{ pools.length === 1 ? 'BOLÃO' : 'BOLÕES' }}
          </div>
        </div>
      </div>

      <!-- Right: 4 big stats -->
      <div :style="{ display: 'flex', gap: 0, border: '1.5px solid var(--ink)', background: 'var(--paper-2)' }">
        <div
          v-for="(s, i) in heroStats"
          :key="s.l"
          :style="{
            padding: '14px 22px', minWidth: '118px',
            borderRight: i < heroStats.length - 1 ? '1px dashed var(--ink)' : 'none',
            position: 'relative',
          }"
        >
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }">
            {{ s.l }}
          </div>
          <div class="font-display" :style="{ fontSize: '40px', lineHeight: 1, marginTop: '4px' }">
            {{ s.v }}
          </div>
          <div :style="{ position: 'absolute', bottom: 0, left: 0, right: 0, height: '4px', background: toneVar(s.tone) }" />
        </div>
      </div>
    </div>

    <!-- Main grid — extrato (left) + desempenho (right) -->
    <div :style="{ display: 'grid', gridTemplateColumns: '2.1fr 1fr', gap: 0, flex: 1 }">
      <!-- LEFT — extrato de palpites como tabela -->
      <div :style="{ padding: '22px 28px', borderRight: '1.5px solid var(--ink)' }">
        <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: '14px' }">
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700 }">
            EXTRATO DE PALPITES · {{ history.length }} ÚLTIMOS
          </div>
          <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.14em', opacity: 0.6 }">
            SEU PALPITE × PLACAR REAL
          </span>
        </div>

        <!-- Table header -->
        <div
          class="font-mono"
          :style="{
            display: 'grid', gridTemplateColumns: cols,
            gap: '10px', padding: '0 14px 8px',
            fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.6,
            borderBottom: '1.5px solid var(--ink)',
          }"
        >
          <span>BOLÃO · DATA</span>
          <span>JOGO</span>
          <span :style="{ textAlign: 'center' }">VOCÊ</span>
          <span :style="{ textAlign: 'center' }">REAL</span>
          <span :style="{ textAlign: 'right' }">PTS</span>
        </div>

        <div :style="{ display: 'flex', flexDirection: 'column' }">
          <div
            v-for="(g, i) in history"
            :key="i"
            class="font-mono"
            :style="{
              display: 'grid', gridTemplateColumns: cols,
              gap: '10px', padding: '12px 14px', alignItems: 'center',
              borderBottom: '1px dashed var(--ink)',
              cursor: g.matchId ? 'pointer' : 'default',
            }"
            @click="g.matchId && router.push(`/match/${g.matchId}`)"
          >
            <!-- pool + date -->
            <div :style="{ minWidth: 0 }">
              <div
                class="font-display"
                :style="{ fontSize: '15px', lineHeight: 1, textTransform: 'uppercase', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }"
              >{{ g.pool }}</div>
              <div :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.6, marginTop: '3px' }">{{ g.date }}</div>
            </div>
            <!-- match -->
            <div :style="{ display: 'flex', alignItems: 'center', gap: '7px' }">
              <FlagImg :team="g.home" :size="18" :radius="2" />
              <span class="font-display" :style="{ fontSize: '17px' }">{{ code(g.home) }}</span>
              <span class="font-display" :style="{ fontSize: '13px', opacity: 0.4 }">×</span>
              <span class="font-display" :style="{ fontSize: '17px' }">{{ code(g.away) }}</span>
              <FlagImg :team="g.away" :size="18" :radius="2" />
            </div>
            <!-- my guess -->
            <div class="font-display" :style="{ fontSize: '18px', textAlign: 'center' }">
              {{ g.myHome }}-{{ g.myAway }}
            </div>
            <!-- real -->
            <div
              class="font-display"
              :style="{ fontSize: '18px', textAlign: 'center', color: toneVar(rowMeta(g).tone), WebkitTextStroke: '0.5px var(--ink)' }"
            >{{ g.realHome }}-{{ g.realAway }}</div>
            <!-- pts + verdict -->
            <div :style="{ display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: '3px' }">
              <span
                class="font-display"
                :style="{
                  minWidth: '34px', textAlign: 'center', padding: '1px 7px',
                  background: toneVar(rowMeta(g).tone), color: toneFg(rowMeta(g).tone),
                  border: '1px solid var(--ink)', borderRadius: '2px', fontSize: '16px',
                }"
              >{{ rowMeta(g).pending ? '—' : '+' + g.pts }}</span>
              <span :style="{ fontSize: '8px', letterSpacing: '0.1em', fontWeight: 700, opacity: 0.7 }">{{ rowMeta(g).verdict }}</span>
            </div>
          </div>
          <div
            v-if="!loading && !history.length"
            class="font-mono"
            :style="{ padding: '24px 14px', textAlign: 'center', fontSize: '12px', letterSpacing: '0.08em', opacity: 0.6 }"
          >Nenhum palpite ainda. Bora cravar o primeiro!</div>
        </div>
      </div>

      <!-- RIGHT — desempenho + por bolão -->
      <div :style="{ padding: '22px 24px', background: 'var(--paper-2)' }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '12px' }">
          DESEMPENHO · {{ breakdownTotal }} PALPITES
        </div>

        <!-- stacked bar -->
        <div :style="{ display: 'flex', height: '16px', border: '1.5px solid var(--ink)', overflow: 'hidden', marginBottom: '12px' }">
          <div
            v-for="(b, i) in breakdown"
            :key="b.l"
            :style="{
              width: `${breakdownTotal ? (b.v / breakdownTotal) * 100 : 0}%`,
              background: toneVar(b.tone),
              borderRight: i < breakdown.length - 1 ? '1.5px solid var(--ink)' : 'none',
            }"
          />
        </div>

        <!-- breakdown rows -->
        <div :style="{ display: 'flex', flexDirection: 'column', gap: '8px', marginBottom: '22px' }">
          <div
            v-for="b in breakdown"
            :key="b.l"
            :style="{
              display: 'flex', alignItems: 'center', gap: '10px',
              padding: '9px 12px', background: 'var(--paper)',
              border: '1.5px solid var(--ink)', borderRadius: '4px',
            }"
          >
            <span :style="{ width: '14px', height: '14px', background: toneVar(b.tone), border: '1.5px solid var(--ink)', flexShrink: 0 }" />
            <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', fontWeight: 700, flex: 1 }">{{ b.l }}</span>
            <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.55 }">{{ b.note }}</span>
            <span class="font-display" :style="{ fontSize: '24px', lineHeight: 1, minWidth: '30px', textAlign: 'right' }">{{ b.v }}</span>
          </div>
        </div>

        <!-- Per-pool standing -->
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '12px' }">
          SUA POSIÇÃO · POR BOLÃO
        </div>
        <div :style="{ display: 'flex', flexDirection: 'column', gap: '10px' }">
          <div
            v-if="!loading && !pools.length"
            class="font-mono"
            :style="{ padding: '14px 12px', textAlign: 'center', fontSize: '11px', letterSpacing: '0.08em', opacity: 0.6, background: 'var(--paper)', border: '1.5px dashed var(--ink)', borderRadius: '4px' }"
          >Você ainda não está em nenhum bolão.</div>
          <div
            v-for="p in pools"
            :key="p.id"
            :style="{
              display: 'flex', alignItems: 'center', gap: '12px',
              padding: '10px 12px', background: 'var(--paper)',
              border: '1.5px solid var(--ink)',
              boxShadow: `3px 3px 0 ${toneVar(p.accent)}, 3px 3px 0 1px var(--ink)`,
              borderRadius: '4px', cursor: 'pointer',
            }"
            @click="router.push(`/pool/${p.id}`)"
          >
            <div :style="{ flex: 1, minWidth: 0 }">
              <div
                class="font-display"
                :style="{ fontSize: '16px', lineHeight: 1, textTransform: 'uppercase', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }"
              >{{ p.name }}</div>
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.1em', opacity: 0.6, marginTop: '3px' }">
                {{ p.myPoints }} PTS · {{ p.members.toLocaleString('pt-BR') }} SÓCIOS
              </div>
            </div>
            <div :style="{ textAlign: 'right' }">
              <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1 }">
                {{ p.myRank }}<span :style="{ fontSize: '12px', opacity: 0.55 }">º</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

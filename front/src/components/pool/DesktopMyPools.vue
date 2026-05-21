<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import FlagImg from '@/components/FlagImg.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import DesktopPoolCard from './DesktopPoolCard.vue';
import { MATCHES, TEAMS, toneVar } from '@/data/mock';
import { usePools } from '@/composables/usePools';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';

const router = useRouter();
const join = useJoinModal();
const create = useCreatePoolModal();

const { pools: allPools, fetchMyPools } = usePools();

onMounted(() => fetchMyPools());

const filters = ['TODOS', 'PRIVADOS', 'PÚBLICOS', 'ATIVOS HOJE'];
const filter = ref('TODOS');

const pools = computed(() => allPools.value.filter(p => {
  if (filter.value === 'PRIVADOS') return !p.isPublic;
  if (filter.value === 'PÚBLICOS') return p.isPublic;
  return true;
}));

const totalExacts = 8;
const guessRate = 0.86;
const totalPts = computed(() => allPools.value.reduce((s, p) => s + p.myPoints, 0));
const bestRank = computed(() => allPools.value.length ? Math.min(...allPools.value.map(p => p.myRank)) : 0);

const stats = computed(() => [
  { v: String(allPools.value.length),              l: 'BOLÕES',       tone: 'magenta' },
  { v: String(totalPts.value),                     l: 'PTS TOTAIS',   tone: 'cobalt' },
  { v: String(totalExacts),                        l: 'CRAVADAS',     tone: 'lime' },
  { v: bestRank.value + 'º',                       l: 'MELHOR POS.',  tone: 'coral' },
  { v: Math.round(guessRate * 100) + '%',          l: 'PALPITADOS',   tone: 'ink', small: true },
]);

const upcomingMatches = MATCHES.filter(m => m.status === 'SCHEDULED').slice(0, 3);

interface ActivityItem {
  pool: string; accent: string; who: string; what: string;
  pts: string; when: string; isMe?: boolean; isPending?: boolean;
}

const activity: ActivityItem[] = [
  { pool: 'Resenha do Bar',  accent: 'magenta', who: 'Helena V.',  what: 'cravou BRA 2×1 CRO', pts: '+3', when: 'há 2 min' },
  { pool: 'Resenha do Bar',  accent: 'magenta', who: 'Você',       what: 'cravou BRA 2×1 CRO', pts: '+3', when: 'há 4 min', isMe: true },
  { pool: 'Trampo FC',       accent: 'cobalt',  who: 'Diretoria',  what: 'acertou vencedor',   pts: '+1', when: 'há 9 min' },
  { pool: 'Geral Brasil',    accent: 'lime',    who: '@neto88',    what: 'cravou MEX 1×0 USA', pts: '+3', when: 'há 22 min' },
  { pool: 'Resenha do Bar',  accent: 'magenta', who: 'Diego K.',   what: 'errou JPN 1×1 GER',  pts: '0',  when: 'há 1h' },
  { pool: 'Trampo FC',       accent: 'cobalt',  who: 'Você',       what: 'palpitou FRA 2×0 BEL', pts: 'pendente', when: 'há 1h', isMe: true, isPending: true },
  { pool: 'Geral Brasil',    accent: 'lime',    who: '@duda_rj',   what: 'acertou vencedor',   pts: '+1', when: 'há 2h' },
];

function ptsBg(act: ActivityItem) {
  if (act.isPending) return 'transparent';
  if (act.pts === '0') return 'var(--paper-3)';
  if (act.pts === '+3') return 'var(--lime)';
  return 'var(--cobalt)';
}
function ptsFg(act: ActivityItem) {
  if (act.isPending || act.pts === '+3' || act.pts === '0') return 'var(--ink)';
  return 'var(--paper)';
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
        >EDIÇÃO Nº 04 · BOLETIM DO SÓCIO · {{ allPools.length }} ATIVOS</div>
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
          <div
            class="font-display"
            :style="{ fontSize: s.small ? '28px' : '36px', lineHeight: 1, marginTop: '4px' }"
          >{{ s.v }}</div>
          <div :style="{
            position: 'absolute', bottom: 0, left: 0, right: 0, height: '4px',
            background: toneVar(s.tone),
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
        <div :style="{
          padding: '14px', background: 'var(--ink)', color: 'var(--paper)',
          position: 'relative', overflow: 'hidden', marginBottom: '18px',
        }">
          <div :style="{ position: 'absolute', top: 0, right: 0, width: '90px', height: '28px', color: 'var(--coral)' }">
            <div class="halftone" :style="{ height: '100%' }" />
          </div>
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--coral)' }"
          >· PENDENTE · {{ upcomingMatches.length }} JOGOS</div>
          <div
            class="font-display"
            :style="{ fontSize: '22px', lineHeight: 1, marginTop: '6px', textTransform: 'uppercase' }"
          >Falta apitar</div>
          <div :style="{ marginTop: '12px', display: 'flex', flexDirection: 'column', gap: '8px' }">
            <div
              v-for="m in upcomingMatches"
              :key="m.id"
              :style="{
                display: 'flex', alignItems: 'center', gap: '8px',
                padding: '8px 10px',
                background: 'rgba(242, 233, 210, 0.08)',
                border: '1px dashed rgba(242, 233, 210, 0.3)',
                cursor: 'pointer',
              }"
              @click="router.push(`/matches`)"
            >
              <FlagImg :team="m.home" :size="16" :radius="2" />
              <span class="font-display" :style="{ fontSize: '14px' }">
                {{ TEAMS[m.home].code }} × {{ TEAMS[m.away].code }}
              </span>
              <FlagImg :team="m.away" :size="16" :radius="2" />
              <span
                class="font-mono"
                :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.7, marginLeft: '4px' }"
              >{{ m.day.toUpperCase() }} · {{ m.kickoff }}</span>
              <span
                class="font-display"
                :style="{ fontSize: '12px', color: 'var(--lime)', marginLeft: 'auto' }"
              >APITAR →</span>
            </div>
          </div>
        </div>

        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '4px' }"
        >ATIVIDADE · ÚLTIMOS LANCES</div>
        <div
          class="font-display"
          :style="{ fontSize: '20px', marginBottom: '12px', textTransform: 'uppercase' }"
        >no calor da mesa</div>
        <div :style="{ display: 'flex', flexDirection: 'column', gap: 0 }">
          <div
            v-for="(act, i) in activity"
            :key="i"
            :style="{
              display: 'flex', alignItems: 'flex-start', gap: '10px',
              padding: '10px 0',
              borderBottom: i < activity.length - 1 ? '1px dashed var(--ink)' : 'none',
              background: act.isMe ? 'rgba(212, 247, 92, 0.16)' : 'transparent',
              marginLeft: act.isMe ? '-8px' : 0, marginRight: act.isMe ? '-8px' : 0,
              paddingLeft: act.isMe ? '8px' : 0, paddingRight: act.isMe ? '8px' : 0,
            }"
          >
            <div :style="{
              width: '6px', alignSelf: 'stretch', background: toneVar(act.accent),
              flexShrink: 0,
            }" />
            <div :style="{ flex: 1, minWidth: 0 }">
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }"
              >{{ act.pool.toUpperCase() }} · {{ act.when.toUpperCase() }}</div>
              <div :style="{ fontSize: '12px', marginTop: '2px' }">
                <b>{{ act.who }}</b> {{ act.what }}
              </div>
            </div>
            <span
              class="font-display"
              :style="{
                padding: '3px 7px', fontSize: '12px',
                background: ptsBg(act),
                border: act.isPending ? '1.5px dashed var(--ink)' : 'none',
                color: ptsFg(act),
                letterSpacing: '0.02em',
                borderRadius: '2px',
                whiteSpace: 'nowrap',
              }"
            >{{ act.pts }}</span>
          </div>
        </div>

        <div :style="{
          marginTop: '18px', padding: '12px 14px',
          border: '1.5px dashed var(--ink)', borderRadius: '4px',
        }">
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700 }"
          >· COMO SUBIR NA TABELA</div>
          <div :style="{ fontSize: '12px', marginTop: '4px', lineHeight: 1.45 }">
            Placar exato paga <b>+3</b>. Só vencedor, <b>+1</b>. Errou tudo? Próximo jogo, mestre.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

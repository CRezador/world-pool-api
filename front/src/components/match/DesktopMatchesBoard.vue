<script setup lang="ts">
import { ref, computed } from 'vue';
import DesktopGroupCard from '@/components/match/DesktopGroupCard.vue';
import DesktopMatchCard from '@/components/match/DesktopMatchCard.vue';
import {
  MATCHES, ALL_GROUPS_STANDINGS, KNOCKOUT_STAGES, getGroupMatches, toneVar, toneFg,
} from '@/data/mock';
import { useGuessModal } from '@/composables/useGuessModal';
import type { Match, MatchStatus } from '@/types';

const guess = useGuessModal();

const phase = ref<'groups' | 'knockout'>('groups');
const group = ref<string | null>(null);

const phases = [
  { id: 'groups', label: 'Fase de grupos' },
  { id: 'knockout', label: 'Mata-mata' },
] as const;

function switchPhase(p: 'groups' | 'knockout') {
  phase.value = p;
  group.value = null;
}

const statusCols: MatchStatus[] = ['SCHEDULED', 'IN_PROGRESS', 'FINISHED'];
const statusTitles: Record<MatchStatus, string> = {
  SCHEDULED: 'A PALPITAR', IN_PROGRESS: 'AO VIVO', FINISHED: 'ENCERRADAS',
};
const statusAccents: Record<MatchStatus, string> = {
  SCHEDULED: 'magenta', IN_PROGRESS: 'coral', FINISHED: 'cobalt',
};

const selectedGroup = computed(() =>
  group.value ? ALL_GROUPS_STANDINGS.find(g => g.g === group.value) ?? null : null,
);
const groupMatches = computed(() => (group.value ? getGroupMatches(group.value) : []));
const groupByStatus = (status: MatchStatus) => groupMatches.value.filter(m => m.status === status);

const knockoutBlocks = computed(() =>
  KNOCKOUT_STAGES
    .map(stg => ({ stg, list: MATCHES.filter(m => m.stage === stg.id) }))
    .filter(b => b.list.length > 0),
);

function palpitar(m: Match) {
  guess.show(m);
}
</script>

<template>
  <div :style="{ minHeight: '100%' }">
    <!-- Header band -->
    <div :style="{
      padding: '24px 28px 20px', borderBottom: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between', gap: '24px',
    }">
      <div :style="{ flex: 1 }">
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700 }">
          CALENDÁRIO · 64 PARTIDAS · 16 SEDES
        </div>
        <div
          class="font-display misprint-cobalt"
          :style="{ fontSize: '64px', lineHeight: 0.9, marginTop: '4px', textTransform: 'uppercase' }"
        >Tabela de jogos</div>
      </div>

      <!-- Phase toggle -->
      <div :style="{
        display: 'flex', border: '1.5px solid var(--ink)', borderRadius: '4px',
        overflow: 'hidden', boxShadow: '4px 4px 0 var(--ink)', flexShrink: 0,
      }">
        <button
          v-for="(p, i) in phases"
          :key="p.id"
          class="press font-display"
          :style="{
            padding: '12px 24px', fontSize: '20px', letterSpacing: '0.03em', textTransform: 'uppercase',
            border: 'none', borderRight: i === 0 ? '1.5px solid var(--ink)' : 'none',
            background: phase === p.id ? 'var(--ink)' : 'var(--paper-2)',
            color: phase === p.id ? 'var(--lime)' : 'var(--ink)',
            cursor: 'pointer', transition: 'background 0.15s, color 0.15s',
          }"
          @click="switchPhase(p.id)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- GROUPS · grid -->
    <div v-if="phase === 'groups' && !group" :style="{ padding: '22px 28px 30px' }">
      <div :style="{ minWidth: 0 }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          A CLASSIFICAÇÃO · 12 CHAVES
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Os grupos
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Toque num grupo pra abrir os jogos e palpitar · ● classificado direto
        </div>
      </div>
      <div :style="{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: '16px', marginTop: '18px' }">
        <DesktopGroupCard
          v-for="g in ALL_GROUPS_STANDINGS"
          :key="g.g"
          :group="g"
          @click="group = g.g"
        />
      </div>
    </div>

    <!-- GROUPS · games of one group -->
    <div v-else-if="phase === 'groups' && group && selectedGroup" :style="{ padding: '22px 28px 30px' }">
      <!-- Group hero -->
      <div :style="{
        display: 'flex', alignItems: 'stretch',
        border: '1.5px solid var(--ink)', boxShadow: '4px 4px 0 var(--ink)',
        borderRadius: '3px', overflow: 'hidden', marginBottom: '26px', background: 'var(--paper-2)',
      }">
        <div :style="{
          background: 'var(--cobalt)', color: 'var(--paper)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          padding: '0 26px', borderRight: '1.5px solid var(--ink)',
        }">
          <span class="font-display" :style="{ fontSize: '92px', lineHeight: 0.8, letterSpacing: '0.02em', textShadow: '4px 4px 0 var(--ink)' }">
            {{ group }}
          </span>
        </div>
        <div :style="{ flex: 1, padding: '16px 22px', display: 'flex', flexDirection: 'column', justifyContent: 'center' }">
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, opacity: 0.7 }">
            FASE DE GRUPOS · CHAVE {{ group }}
          </div>
          <div class="font-display" :style="{ fontSize: '30px', lineHeight: 1, margin: '4px 0 12px', textTransform: 'uppercase' }">
            Grupo {{ group }}
          </div>
          <div :style="{ display: 'flex', gap: '18px', flexWrap: 'wrap' }">
            <div
              v-for="row in selectedGroup.rows"
              :key="row.code"
              :style="{ display: 'flex', alignItems: 'center', gap: '8px' }"
            >
              <img
                :src="`https://flagcdn.com/40x30/${row.iso}.png`"
                alt=""
                :style="{ width: '24px', height: '16px', border: '1px solid var(--ink)', borderRadius: '2px', objectFit: 'cover' }"
              />
              <span class="font-display" :style="{ fontSize: '16px' }">{{ row.code }}</span>
            </div>
          </div>
        </div>
        <button
          class="font-mono press"
          :style="{
            alignSelf: 'flex-start', margin: '14px', flexShrink: 0,
            padding: '9px 14px', fontSize: '11px', letterSpacing: '0.12em', fontWeight: 700,
            background: 'var(--ink)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--magenta)',
            cursor: 'pointer', borderRadius: '3px',
          }"
          @click="group = null"
        >← GRUPOS</button>
      </div>

      <!-- Section rule -->
      <div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          OS PALPITES · GRUPO {{ group }}
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Jogos pra palpitar
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Acertou o vencedor +1 · cravou o placar +3
        </div>
      </div>

      <!-- Status board · 3 columns -->
      <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '20px', marginTop: '18px' }">
        <div v-for="status in statusCols" :key="status">
          <div
            class="font-mono"
            :style="{
              display: 'flex', justifyContent: 'space-between', alignItems: 'center',
              fontSize: '11px', letterSpacing: '0.18em', fontWeight: 700, padding: '10px 14px',
              background: toneVar(statusAccents[status]), color: toneFg(statusAccents[status]),
              border: '1.5px solid var(--ink)',
            }"
          >
            <span>{{ statusTitles[status] }}</span>
            <span>{{ groupByStatus(status).length }}</span>
          </div>
          <div :style="{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '12px' }">
            <div
              v-if="groupByStatus(status).length === 0"
              class="font-mono"
              :style="{ padding: '16px', opacity: 0.5, fontSize: '11px', border: '1.5px dashed var(--ink)', textAlign: 'center' }"
            >nenhum jogo</div>
            <DesktopMatchCard
              v-for="m in groupByStatus(status)"
              :key="m.id"
              :match="m"
              :accent="statusAccents[status]"
              @palpitar="palpitar(m)"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- KNOCKOUT -->
    <div v-else :style="{ padding: '22px 28px 30px' }">
      <div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, color: 'var(--magenta)' }">
          ELIMINATÓRIAS · A FORCA
        </div>
        <div :style="{ display: 'flex', alignItems: 'baseline', gap: '14px', marginTop: '2px' }">
          <div class="font-display" :style="{ fontSize: '34px', lineHeight: 0.95, textTransform: 'uppercase', whiteSpace: 'nowrap', flexShrink: 0 }">
            Mata-mata
          </div>
          <div :style="{ flex: 1, height: '3px', background: 'var(--ink)' }" />
        </div>
        <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.06em', opacity: 0.7, marginTop: '6px' }">
          Cada fase, um corte. Palpite jogo a jogo.
        </div>
      </div>

      <div :style="{ display: 'flex', flexDirection: 'column', gap: '26px', marginTop: '20px' }">
        <div v-for="{ stg, list } in knockoutBlocks" :key="stg.id">
          <!-- Phase header -->
          <div :style="{
            display: 'flex', alignItems: 'stretch',
            border: '1.5px solid var(--ink)', boxShadow: '3px 3px 0 var(--ink)',
            borderRadius: '3px', overflow: 'hidden', marginBottom: '16px',
          }">
            <div :style="{
              background: toneVar(stg.accent), color: toneFg(stg.accent),
              padding: '12px 20px', display: 'flex', alignItems: 'center', gap: '14px',
              borderRight: '1.5px solid var(--ink)',
            }">
              <span class="font-display" :style="{ fontSize: '34px', lineHeight: 1, letterSpacing: '0.03em', textTransform: 'uppercase', whiteSpace: 'nowrap' }">
                {{ stg.label }}
              </span>
            </div>
            <div :style="{
              flex: 1, background: 'var(--paper-2)',
              display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: '0 18px',
            }">
              <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.75 }">
                {{ stg.note }}
              </span>
              <span
                class="font-mono"
                :style="{
                  fontSize: '11px', letterSpacing: '0.14em', fontWeight: 700,
                  padding: '5px 12px', background: 'var(--ink)', color: 'var(--paper)', borderRadius: '999px',
                }"
              >{{ list.length }} {{ list.length === 1 ? 'JOGO' : 'JOGOS' }}</span>
            </div>
          </div>

          <div :style="{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '16px' }">
            <DesktopMatchCard
              v-for="m in list"
              :key="m.id"
              :match="m"
              :accent="stg.accent"
              @palpitar="palpitar(m)"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

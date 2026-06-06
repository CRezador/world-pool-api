<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import SectionHead from '@/components/SectionHead.vue';
import MatchListCard from '@/components/match/MatchListCard.vue';
import MatchGroupCard from '@/components/match/MatchGroupCard.vue';
import DesktopMatchesBoard from '@/components/match/DesktopMatchesBoard.vue';
import {
  MATCHES, ALL_GROUPS_STANDINGS, KNOCKOUT_STAGES, getGroupMatches, toneVar, toneFg,
} from '@/data/mock';
import { useBreakpoint } from '@/composables/useBreakpoint';
import type { Match } from '@/types';

const router = useRouter();
const { isDesktop } = useBreakpoint();

const phase = ref<'groups' | 'knockout'>('groups');
const group = ref<string | null>(null);

const phases = [
  { id: 'groups', label: 'Grupos' },
  { id: 'knockout', label: 'Mata-mata' },
] as const;

function switchPhase(p: 'groups' | 'knockout') {
  phase.value = p;
  group.value = null;
}

const selectedGroup = computed(() =>
  group.value ? ALL_GROUPS_STANDINGS.find(g => g.g === group.value) ?? null : null,
);
const groupMatches = computed(() => (group.value ? getGroupMatches(group.value) : []));

const knockoutBlocks = computed(() =>
  KNOCKOUT_STAGES
    .map(stg => ({ stg, list: MATCHES.filter(m => m.stage === stg.id) }))
    .filter(b => b.list.length > 0),
);

function goMatch(m: Match) {
  router.push(m.status === 'SCHEDULED' ? `/guess/${m.id}` : `/match/${m.id}`);
}
</script>

<template>
  <DesktopMatchesBoard v-if="isDesktop" />
  <div v-else :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 12px', borderBottom: '1.5px solid var(--ink)' }">
      <SectionHead kicker="CALENDÁRIO · COPA 26" title="Tabela de jogos" />
    </div>

    <!-- Phase toggle — Grupos / Mata-mata -->
    <div :style="{ padding: '12px 18px', borderBottom: '1.5px solid var(--ink)' }">
      <div :style="{
        display: 'flex', width: '100%',
        border: '1.5px solid var(--ink)', borderRadius: '3px', overflow: 'hidden',
        boxShadow: '3px 3px 0 var(--ink)',
      }">
        <button
          v-for="(p, i) in phases"
          :key="p.id"
          class="press font-display"
          :style="{
            flex: 1, padding: '10px 8px', border: 'none',
            borderRight: i === 0 ? '1.5px solid var(--ink)' : 'none',
            fontSize: '17px', letterSpacing: '0.03em', textTransform: 'uppercase',
            background: phase === p.id ? 'var(--ink)' : 'var(--paper-2)',
            color: phase === p.id ? 'var(--lime)' : 'var(--ink)', cursor: 'pointer',
          }"
          @click="switchPhase(p.id)"
        >{{ p.label }}</button>
      </div>
    </div>

    <!-- GROUPS · grid -->
    <div v-if="phase === 'groups' && !group" :style="{ padding: '14px 18px 20px' }">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--magenta)' }">
        A CLASSIFICAÇÃO · 12 CHAVES
      </div>
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.04em', opacity: 0.7, marginTop: '4px', marginBottom: '14px' }">
        Toque num grupo pra abrir os jogos e palpitar.
      </div>
      <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }">
        <MatchGroupCard
          v-for="g in ALL_GROUPS_STANDINGS"
          :key="g.g"
          :group="g"
          @click="group = g.g"
        />
      </div>
    </div>

    <!-- GROUPS · games of one group -->
    <div v-else-if="phase === 'groups' && group && selectedGroup">
      <div :style="{ padding: '14px 18px', borderBottom: '1.5px solid var(--ink)', background: 'var(--paper-2)' }">
        <button
          class="font-mono press"
          :style="{
            padding: '7px 12px', fontSize: '10px', letterSpacing: '0.12em', fontWeight: 700,
            background: 'var(--ink)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--magenta)',
            cursor: 'pointer', borderRadius: '3px', marginBottom: '12px',
          }"
          @click="group = null"
        >← GRUPOS</button>
        <div :style="{ display: 'flex', alignItems: 'center', gap: '14px' }">
          <div
            class="font-display"
            :style="{ fontSize: '60px', lineHeight: 0.78, color: 'var(--cobalt)', textShadow: '3px 3px 0 var(--ink)', flexShrink: 0 }"
          >{{ group }}</div>
          <div :style="{ minWidth: 0 }">
            <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7 }">
              FASE DE GRUPOS · CHAVE {{ group }}
            </div>
            <div class="font-display" :style="{ fontSize: '26px', textTransform: 'uppercase', lineHeight: 1, marginTop: '2px' }">
              Grupo {{ group }}
            </div>
          </div>
        </div>
        <div :style="{ display: 'flex', gap: '12px', marginTop: '12px', flexWrap: 'wrap' }">
          <div
            v-for="row in selectedGroup.rows"
            :key="row.code"
            :style="{ display: 'flex', alignItems: 'center', gap: '6px' }"
          >
            <img
              :src="`https://flagcdn.com/40x30/${row.iso}.png`"
              alt=""
              :style="{ width: '20px', height: '13px', border: '1px solid var(--ink)', borderRadius: '2px', objectFit: 'cover' }"
            />
            <span class="font-display" :style="{ fontSize: '14px' }">{{ row.code }}</span>
          </div>
        </div>
      </div>

      <div :style="{ padding: '14px 18px 6px' }">
        <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700 }">
          OS PALPITES · GRUPO {{ group }}
        </span>
      </div>
      <div :style="{ padding: '8px 18px 18px', display: 'flex', flexDirection: 'column', gap: '14px' }">
        <MatchListCard
          v-for="m in groupMatches"
          :key="m.id"
          :match="m"
          @click="goMatch(m)"
        />
      </div>
    </div>

    <!-- KNOCKOUT · stage blocks -->
    <div v-else :style="{ padding: '14px 18px 18px' }">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--magenta)' }">
        ELIMINATÓRIAS · A FORCA
      </div>
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.04em', opacity: 0.7, marginTop: '4px' }">
        Cada fase, um corte. Palpite jogo a jogo.
      </div>
      <div
        v-for="{ stg, list } in knockoutBlocks"
        :key="stg.id"
        :style="{ marginTop: '20px' }"
      >
        <div :style="{
          display: 'flex', alignItems: 'center', justifyContent: 'space-between',
          padding: '9px 12px', background: toneVar(stg.accent), color: toneFg(stg.accent),
          border: '1.5px solid var(--ink)', borderRadius: '3px', boxShadow: '2px 2px 0 var(--ink)',
        }">
          <span class="font-display" :style="{ fontSize: '21px', textTransform: 'uppercase', letterSpacing: '0.03em' }">
            {{ stg.label }}
          </span>
          <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700 }">
            {{ list.length }} {{ list.length === 1 ? 'JOGO' : 'JOGOS' }}
          </span>
        </div>
        <div :style="{ display: 'flex', flexDirection: 'column', gap: '12px', marginTop: '12px' }">
          <MatchListCard
            v-for="m in list"
            :key="m.id"
            :match="m"
            @click="goMatch(m)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

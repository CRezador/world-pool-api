<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import BackBar from '@/components/BackBar.vue';
import StageBadge from '@/components/StageBadge.vue';
import StatusPill from '@/components/StatusPill.vue';
import FlagImg from '@/components/FlagImg.vue';
import Avatar from '@/components/Avatar.vue';
import Stamp from '@/components/Stamp.vue';
import { findMatch, MEMBERS, TEAMS } from '@/data/mock';

const route = useRoute();
const matchId = computed(() => Number(route.params.matchId));
const match = computed(() => findMatch(matchId.value));
const home = computed(() => TEAMS[match.value.home as string]);
const away = computed(() => TEAMS[match.value.away as string]);
const isLive = computed(() => match.value.status === 'IN_PROGRESS');

const guesses = computed(() => {
  const fakes: [number, number][] = [[2,1],[1,1],[2,0],[0,2],[3,1],[1,2],[2,2],[1,0]];
  return MEMBERS.slice(0, 8).map((m, i) => {
    const [h, a] = fakes[i];
    let pts = 0;
    if (match.value.status !== 'SCHEDULED'
        && match.value.homeScore !== undefined
        && match.value.awayScore !== undefined) {
      if (h === match.value.homeScore && a === match.value.awayScore) pts = 3;
      else if (
        (h > a && match.value.homeScore > match.value.awayScore) ||
        (h < a && match.value.homeScore < match.value.awayScore) ||
        (h === a && match.value.homeScore === match.value.awayScore)
      ) pts = 1;
    }
    return { ...m, home: h, away: a, pts };
  }).sort((x, y) => y.pts - x.pts);
});
</script>

<template>
  <div :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '14px 18px 8px' }">
      <BackBar
        :title="isLive ? 'AO VIVO' : 'Encerrada'"
        kicker="DETALHE DO JOGO"
        fallback="/matches"
      />
    </div>

    <div :style="{ padding: '8px 18px 14px', position: 'relative' }">
      <div
        class="perf-bottom"
        :style="{
          background: 'var(--ink)', color: 'var(--paper)',
          padding: '16px 16px 22px',
          position: 'relative',
        }"
      >
        <div :style="{ display: 'flex', justifyContent: 'space-between', marginBottom: '14px' }">
          <StageBadge :stage="match.stage" :group="match.group" :tone="isLive ? 'coral' : 'cobalt'" />
          <StatusPill :status="match.status" />
        </div>
        <div :style="{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }">
          <div :style="{ textAlign: 'center', flex: 1 }">
            <FlagImg
              :team="(match.home as string)"
              :size="56"
              :radius="4"
              :style="{ boxShadow: '3px 3px 0 var(--magenta)', border: '1.5px solid var(--paper)' }"
            />
            <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">{{ home.code }}</div>
          </div>
          <div
            class="font-display"
            :style="{ fontSize: '64px', lineHeight: 1, color: 'var(--lime)' }"
          >
            {{ match.homeScore }}<span :style="{ color: 'var(--paper)', opacity: 0.3 }">×</span>{{ match.awayScore }}
          </div>
          <div :style="{ textAlign: 'center', flex: 1 }">
            <FlagImg
              :team="(match.away as string)"
              :size="56"
              :radius="4"
              :style="{ boxShadow: '3px 3px 0 var(--cobalt)', border: '1.5px solid var(--paper)' }"
            />
            <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">{{ away.code }}</div>
          </div>
        </div>
        <div
          class="font-mono"
          :style="{
            fontSize: '10px', letterSpacing: '0.14em', opacity: 0.6,
            marginTop: '14px', textAlign: 'center',
          }"
        >{{ match.venue.toUpperCase() }} · {{ match.day.toUpperCase() }}</div>
        <div :style="{
          position: 'absolute', top: 0, right: 0, width: '80px', height: '30px',
          color: 'var(--magenta)',
        }">
          <div class="halftone" :style="{ height: '100%' }" />
        </div>
      </div>
    </div>

    <div :style="{ padding: '8px 18px 14px' }">
      <div :style="{
        padding: '14px', background: 'var(--lime)',
        border: '1.5px solid var(--ink)', borderRadius: '4px',
        boxShadow: '3px 3px 0 var(--ink)',
        display: 'flex', alignItems: 'center', gap: '14px',
      }">
        <Stamp tone="ink" :rotate="-8">+3 EXATO</Stamp>
        <div>
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.14em' }">SEU PALPITE</div>
          <div class="font-display" :style="{ fontSize: '22px' }">VOCÊ CRAVOU 2 × 1</div>
        </div>
      </div>
    </div>

    <div :style="{ padding: '8px 18px 22px' }">
      <div
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700, marginBottom: '8px' }"
      >PALPITES DA RESENHA · 8 DE 12</div>
      <div :style="{ display: 'flex', flexDirection: 'column', gap: '8px' }">
        <div
          v-for="g in guesses"
          :key="g.id"
          :style="{
            display: 'flex', alignItems: 'center', gap: '10px',
            padding: '10px 12px',
            background: g.isMe ? 'var(--paper-2)' : 'transparent',
            border: g.isMe ? '1.5px solid var(--magenta)' : '1.5px solid var(--ink)',
            borderRadius: '4px',
          }"
        >
          <Avatar :member="g" :size="32" />
          <div :style="{ flex: 1 }">
            <div class="font-display" :style="{ fontSize: '14px' }">
              {{ g.name.split(' ')[0] }}
            </div>
          </div>
          <div class="font-display" :style="{ fontSize: '18px' }">{{ g.home }} × {{ g.away }}</div>
          <span
            class="font-mono"
            :style="{
              minWidth: '36px', textAlign: 'center', fontSize: '11px', fontWeight: 700,
              padding: '4px 6px', borderRadius: '2px',
              background: g.pts === 3 ? 'var(--lime)' : g.pts === 1 ? 'var(--cobalt)' : 'var(--paper-3)',
              color: g.pts === 1 ? 'var(--paper)' : 'var(--ink)',
            }"
          >{{ g.pts > 0 ? `+${g.pts}` : '0' }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

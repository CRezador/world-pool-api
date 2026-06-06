<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import Avatar from '@/components/Avatar.vue';
import SectionHead from '@/components/SectionHead.vue';
import Stamp from '@/components/Stamp.vue';
import DesktopMatchCarousel from '@/components/match/DesktopMatchCarousel.vue';
import DesktopGroupStandings from '@/components/pool/DesktopGroupStandings.vue';
import PoolMembersTab from '@/components/pool/tabs/PoolMembersTab.vue';
import DesktopPoolConfigPanel from '@/components/pool/DesktopPoolConfigPanel.vue';
import { toneVar } from '@/data/mock';
import { useMatch } from '@/composables/useMatch';
import { useLeaderboard } from '@/composables/useLeaderboard';
import type { Pool, Tone } from '@/types';

type Role = 'OWNER' | 'ADMIN' | 'MEMBER';
type TabId = 'rank' | 'members' | 'config';

const props = defineProps<{
  pool: Pool;
  myRole: Role;
  joinedAt: string;
}>();

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];
function deriveTone(id: number): Tone {
  return TONES[id % TONES.length];
}

const { upcomingMatches } = useMatch();
const { leaderboard, myEntry, myPoolEntry, fetchLeaderboard } = useLeaderboard();

function chipBg(n: number) {
  if (n === 3) return 'var(--lime)';
  if (n > 0)  return 'var(--cobalt)';
  return 'var(--paper-3)';
}
function chipFg(n: number) {
  return n === 3 || n === 0 ? 'var(--ink)' : 'var(--paper)';
}

const gameDay = computed(() => {
  const d = upcomingMatches.value[0]?.gameDay;
  return d != null ? String(d).padStart(2, '0') : null;
});

const stats = computed(() => [
  { v: myEntry.value ? myEntry.value.rank + 'º' : '—', l: 'SUA POS.', tone: 'lime' },
  { v: myEntry.value ? String(myEntry.value.points) : '—', l: 'PTS', tone: 'magenta' },
  { v: myEntry.value ? String(myEntry.value.exactHits) : '—', l: 'EXATOS', tone: 'cobalt' },
  { v: myEntry.value ? String(myEntry.value.resultHits) : '—', l: 'ACERTOS', tone: 'coral' },
]);

const loading = ref(true);
const tab = ref<TabId>('rank');

const tabs = computed<{ id: TabId; label: string }[]>(() => [
  { id: 'rank',    label: 'RANKING'  },
  { id: 'members', label: 'MEMBROS'  },
  { id: 'config',  label: 'CONFIG ⚙' },
]);

onMounted(async () => {
  try {
    await fetchLeaderboard(props.pool.id);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">

    <!-- Header -->
    <div :style="{
      padding: '28px 32px 20px', display: 'flex', gap: '28px', alignItems: 'flex-end',
      background: 'var(--paper)', borderBottom: '1.5px solid var(--ink)',
    }">
      <div :style="{ flex: 1 }">
        <div
          class="font-mono"
          :style="{ fontSize: '11px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '12px' }"
        >BOLÃO Nº 01 · DESDE MAI/26</div>
        <div
          class="font-display misprint-magenta"
          :style="{ fontSize: '80px', lineHeight: 1, letterSpacing: '0.01em', textTransform: 'uppercase' }"
        >{{ pool.name }}</div>
        <div :style="{ display: 'flex', gap: '14px', marginTop: '18px', alignItems: 'center' }">
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em' }">
            CÓDIGO <span :style="{ background: 'var(--ink)', color: 'var(--paper)', padding: '2px 8px' }">{{ pool.code }}</span>
          </div>
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.14em' }">
            {{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }} · {{ pool.members }} SÓCIOS
          </div>
          <Stamp v-if="gameDay" tone="cobalt" :rotate="-4">RODADA {{ gameDay }} EM CURSO</Stamp>
        </div>
      </div>

      <!-- Stats chips skeleton -->
      <div v-if="loading" :style="{ display: 'flex', gap: 0, border: '1.5px solid var(--ink)' }">
        <div
          v-for="n in 5" :key="n"
          class="skeleton"
          :style="{ width: '110px', height: '78px', borderRight: '1.5px solid var(--ink)', borderRadius: 0 }"
        />
      </div>

      <!-- Stats chips loaded -->
      <div v-else :style="{ display: 'flex', gap: 0, border: '1.5px solid var(--ink)' }">
        <div
          v-for="(s, i) in stats"
          :key="s.l"
          :style="{
            padding: '14px 22px', minWidth: '110px',
            background: 'var(--paper-2)',
            borderRight: '1.5px solid var(--ink)',
            position: 'relative',
          }"
        >
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }">{{ s.l }}</div>
          <div class="font-display" :style="{ fontSize: '38px', lineHeight: 1, marginTop: '4px' }">{{ s.v }}</div>
          <div :style="{ position: 'absolute', bottom: 0, left: 0, right: 0, height: '4px', background: toneVar(s.tone) }" />
        </div>
        <div :style="{ padding: '14px 22px', background: 'var(--paper-2)', position: 'relative' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }">ÚLTIMOS 3</div>
          <div :style="{ display: 'flex', gap: '4px', marginTop: '8px' }">
            <template v-if="myPoolEntry?.lastResults.length">
              <span
                v-for="(n, i) in myPoolEntry.lastResults"
                :key="i"
                class="font-display"
                :style="{
                  minWidth: '32px', padding: '3px 6px', textAlign: 'center',
                  background: chipBg(n), color: chipFg(n),
                  fontSize: '20px', border: '1px solid var(--ink)',
                }"
              >{{ n > 0 ? `+${n}` : '0' }}</span>
            </template>
            <span v-else class="font-display" :style="{ fontSize: '20px', opacity: 0.4 }">—</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab bar -->
    <div :style="{
      display: 'flex', borderBottom: '1.5px solid var(--ink)',
      background: 'var(--paper-2)', flexShrink: 0,
    }">
      <button
        v-for="t in tabs"
        :key="t.id"
        class="font-display press"
        :style="{
          padding: '14px 28px', border: 'none',
          borderRight: '1.5px solid var(--ink)',
          fontSize: '16px', letterSpacing: '0.06em', cursor: 'pointer',
          background: tab === t.id ? 'var(--ink)' : 'transparent',
          color: tab === t.id ? (t.id === 'config' ? 'var(--lime)' : 'var(--paper)') : 'var(--ink)',
          borderBottom: tab === t.id ? '3px solid var(--magenta)' : '3px solid transparent',
          transition: 'background 0.14s, color 0.14s',
        }"
        @click="tab = t.id"
      >{{ t.label }}</button>
    </div>

    <!-- RANKING tab -->
    <div
      v-if="tab === 'rank'"
      :style="{
        display: 'grid', gridTemplateColumns: '1.3fr 1fr', gap: '28px',
        padding: '28px 32px', flex: 1,
      }"
    >
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
              display: 'grid', gridTemplateColumns: '40px 52px 1fr 70px 70px 70px 60px',
              padding: '8px 14px', background: 'var(--ink)', color: 'var(--paper)',
            }"
          >
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">#</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">TEND.</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700 }">SÓCIO</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">EXATOS</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">ACERTOS</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">PALPITES</span>
            <span :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, textAlign: 'center' }">PTS</span>
          </div>

          <template v-if="loading">
            <div v-for="n in 8" :key="n" :style="{ padding: '10px 14px', borderBottom: '1px solid var(--ink)' }">
              <div class="skeleton" :style="{ height: '30px' }" />
            </div>
          </template>

          <template v-else>
            <div
              v-for="(entry, i) in leaderboard"
              :key="entry.userId"
              :style="{
                display: 'grid', gridTemplateColumns: '40px 52px 1fr 70px 70px 70px 60px',
                padding: '12px 14px',
                borderBottom: i < leaderboard.length - 1 ? '1px solid var(--ink)' : 'none',
                background: entry.isMe ? 'var(--lime)' : 'transparent',
                alignItems: 'center',
              }"
            >
              <span class="font-display" :style="{ fontSize: '22px' }">{{ entry.rank }}</span>
              <div>
                <span
                  v-if="entry.trend !== 'equal'"
                  class="font-mono"
                  :style="{
                    display: 'inline-flex', alignItems: 'center', gap: '2px',
                    padding: '2px 6px', fontSize: '10px', fontWeight: 700, letterSpacing: '0.04em',
                    background: entry.trend === 'up' ? 'var(--lime)' : 'var(--coral)',
                    color: 'var(--ink)', border: '1.5px solid var(--ink)', borderRadius: '2px',
                    whiteSpace: 'nowrap',
                  }"
                >
                  {{ entry.trend === 'up' ? '▲' : '▼' }}
                  {{ entry.previousRank !== null ? Math.abs(entry.previousRank - entry.rank) : '' }}
                </span>
                <span v-else class="font-mono" :style="{ fontSize: '12px', opacity: 0.35 }">—</span>
              </div>
              <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
                <Avatar
                  :member="{ id: entry.userId, name: entry.name, handle: '', avatar: '', tone: deriveTone(entry.userId), role: 'MEMBER', isMe: entry.isMe }"
                  :size="30"
                />
                <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1, display: 'flex', alignItems: 'center', gap: '6px', flexWrap: 'wrap' }">
                  {{ entry.name }}
                  <span v-if="entry.isMe" class="font-mono" :style="{ fontSize: '9px' }"> · VOCÊ</span>
                  <Stamp v-if="entry.role === 'OWNER'" tone="magenta" :rotate="3">DONO</Stamp>
                  <Stamp v-else-if="entry.role === 'ADMIN'" tone="cobalt" :rotate="-3">ADMIN</Stamp>
                </div>
              </div>
              <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.exactHits }}</span>
              <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.resultHits }}</span>
              <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.guessesCount }}</span>
              <span class="font-display" :style="{ textAlign: 'center', fontSize: '22px' }">{{ entry.points }}</span>
            </div>
          </template>
        </div>
      </div>

      <div :style="{ display: 'flex', flexDirection: 'column', gap: '20px' }">
        <DesktopMatchCarousel />
        <DesktopGroupStandings />
      </div>
    </div>

    <!-- MEMBROS tab -->
    <div v-else-if="tab === 'members'" :style="{ padding: '24px 32px', flex: 1 }">
      <div :style="{ maxWidth: '680px' }">
        <PoolMembersTab :pool-id="pool.id" />
      </div>
    </div>

    <!-- CONFIG tab -->
    <DesktopPoolConfigPanel
      v-else-if="tab === 'config'"
      :pool="pool"
      :my-role="myRole"
      :joined-at="joinedAt"
    />

  </div>
</template>

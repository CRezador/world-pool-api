<script setup lang="ts">
import { computed, onMounted } from 'vue';
import Avatar from '@/components/Avatar.vue';
import SectionHead from '@/components/SectionHead.vue';
import Stamp from '@/components/Stamp.vue';
import DesktopMatchCarousel from '@/components/match/DesktopMatchCarousel.vue';
import DesktopGroupStandings from '@/components/pool/DesktopGroupStandings.vue';
import { toneVar } from '@/data/mock';
import { useMatch } from '@/composables/useMatch';
import { useLeaderboard } from '@/composables/useLeaderboard';
import type { Pool, Tone } from '@/types';

const props = defineProps<{ pool: Pool }>();

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];
function deriveTone(id: number): Tone {
  return TONES[id % TONES.length];
}

const { upcomingMatches } = useMatch();
const { leaderboard, myEntry, fetchLeaderboard } = useLeaderboard();

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

onMounted(() => fetchLeaderboard(props.pool.id));
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
      <div :style="{ display: 'flex', gap: 0, border: '1.5px solid var(--ink)' }">
        <div
          v-for="(s, i) in stats"
          :key="s.l"
          :style="{
            padding: '14px 22px', minWidth: '110px',
            background: 'var(--paper-2)',
            borderRight: i < stats.length - 1 ? '1.5px solid var(--ink)' : 'none',
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
            v-for="(entry, i) in leaderboard"
            :key="entry.userId"
            :style="{
              display: 'grid', gridTemplateColumns: '40px 1fr 70px 70px 70px 60px',
              padding: '12px 14px',
              borderBottom: i < leaderboard.length - 1 ? '1px solid var(--ink)' : 'none',
              background: entry.isMe ? 'var(--lime)' : 'transparent',
              alignItems: 'center',
            }"
          >
            <span class="font-display" :style="{ fontSize: '22px' }">{{ entry.rank }}</span>
            <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
              <Avatar
                :member="{ id: entry.userId, name: entry.name, handle: '', avatar: '', tone: deriveTone(entry.userId), role: 'MEMBER', isMe: entry.isMe }"
                :size="30"
              />
              <div>
                <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">
                  {{ entry.name }}
                  <span
                    v-if="entry.isMe"
                    class="font-mono"
                    :style="{ fontSize: '9px' }"
                  > · VOCÊ</span>
                </div>
              </div>
            </div>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.exactHits }}</span>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.resultHits }}</span>
            <span class="font-mono" :style="{ textAlign: 'center', fontSize: '13px' }">{{ entry.guessesCount }}</span>
            <span class="font-display" :style="{ textAlign: 'center', fontSize: '22px' }">{{ entry.points }}</span>
          </div>
        </div>
      </div>

      <div :style="{ display: 'flex', flexDirection: 'column', gap: '20px' }">
        <DesktopMatchCarousel />

        <DesktopGroupStandings />
      </div>
    </div>
  </div>
</template>

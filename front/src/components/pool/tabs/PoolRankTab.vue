<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { toneVar, toneFg } from '@/data/mock';
import { useLeaderboard } from '@/composables/useLeaderboard';
import { deriveAccent } from '@/composables/usePools';
import Stamp from '@/components/Stamp.vue';
import type { LeaderboardEntry } from '@/types';

const props = defineProps<{ poolId: string }>();
const emit = defineEmits<{ (e: 'refresh'): void }>();

const { leaderboard, myEntry, fetchLeaderboard } = useLeaderboard();

const loading = computed(() => leaderboard.value.length === 0);

onMounted(() => fetchLeaderboard(props.poolId));

function reload() {
  fetchLeaderboard(props.poolId);
  emit('refresh');
}

const top3Order = [2, 1, 3];

const top3 = computed(() =>
  top3Order.map(rank => {
    const entry = leaderboard.value[rank - 1] as LeaderboardEntry | undefined;
    const height = rank === 1 ? 120 : rank === 2 ? 92 : 76;
    const tone = rank === 1 ? 'lime' : rank === 2 ? 'cobalt' : 'coral';
    return { rank, entry, height, tone };
  }).filter(p => p.entry),
);

const rest = computed(() =>
  leaderboard.value.slice(3).map((entry, i) => ({ entry, rank: i + 4 })),
);

function initials(name: string) {
  const parts = name.trim().split(' ');
  return parts.length > 1
    ? parts[0][0] + parts[parts.length - 1][0]
    : parts[0][0];
}
</script>

<template>
  <div v-if="loading" :style="{ padding: '14px 18px 18px' }">
    <div :style="{ display: 'flex', justifyContent: 'space-between', marginBottom: '12px' }">
      <div class="skeleton" :style="{ height: '12px', width: '140px' }" />
      <div class="skeleton" :style="{ height: '28px', width: '90px' }" />
    </div>
    <div :style="{ display: 'flex', alignItems: 'flex-end', gap: '8px', marginBottom: '18px' }">
      <div v-for="(h, i) in [92, 120, 76]" :key="i" :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '6px' }">
        <div class="skeleton" :style="{ width: '42px', height: '42px', borderRadius: '50%' }" />
        <div class="skeleton" :style="{ height: '10px', width: '50px' }" />
        <div class="skeleton" :style="{ height: `${h}px`, width: '100%' }" />
      </div>
    </div>
    <div :style="{ display: 'flex', flexDirection: 'column', gap: '1px' }">
      <div v-for="n in 5" :key="n" class="skeleton" :style="{ height: '52px' }" />
    </div>
  </div>

  <div v-else class="fade-up" :style="{ padding: '14px 18px 18px' }">
    <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700 }"
      >PÓDIO · TOP {{ leaderboard.length }}</span>
      <span
        class="font-mono press"
        :style="{
          fontSize: '10px', letterSpacing: '0.12em', cursor: 'pointer',
          padding: '4px 8px', border: '1.5px solid var(--ink)', borderRadius: '3px',
        }"
        @click="reload"
      >↻ ATUALIZAR</span>
    </div>

    <!-- Podium top 3 -->
    <div :style="{ display: 'flex', alignItems: 'flex-end', gap: '8px', marginBottom: '18px' }">
      <div
        v-for="p in top3"
        :key="p.rank"
        :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center' }"
      >
        <!-- Avatar circle -->
        <div
          class="font-display"
          :style="{
            width: '42px', height: '42px', borderRadius: '50%',
            background: toneVar(deriveAccent(p.entry!.userId)),
            color: toneFg(deriveAccent(p.entry!.userId)),
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '14px', letterSpacing: '0.04em',
            border: '1.5px solid var(--ink)',
            boxShadow: '1.5px 1.5px 0 var(--ink)',
            outline: p.entry!.isMe ? '2px solid var(--cobalt)' : 'none',
            outlineOffset: '2px',
          }"
        >{{ initials(p.entry!.name).toUpperCase() }}</div>

        <div class="font-display" :style="{ fontSize: '12px', marginTop: '6px', textAlign: 'center' }">
          {{ p.entry!.name.split(' ')[0] }}
        </div>
        <Stamp v-if="p.entry!.role === 'OWNER'" tone="magenta" :rotate="3" :style="{ fontSize: '8px' }">DONO</Stamp>
        <Stamp v-else-if="p.entry!.role === 'ADMIN'" tone="cobalt" :rotate="-3" :style="{ fontSize: '8px' }">ADMIN</Stamp>
        <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7 }">{{ p.entry!.points }} pts</div>

        <!-- Bar -->
        <div :style="{
          marginTop: '6px',
          width: '100%', height: `${p.height}px`,
          background: toneVar(p.tone),
          color: p.tone === 'lime' ? 'var(--ink)' : 'var(--paper)',
          border: '1.5px solid var(--ink)',
          display: 'flex', alignItems: 'flex-start', justifyContent: 'center',
          paddingTop: '8px',
          position: 'relative',
        }">
          <div
            class="font-display"
            :style="{ fontSize: '36px', lineHeight: 1, letterSpacing: '0.02em' }"
          >{{ p.rank }}º</div>
          <div
            class="halftone"
            :style="{
              position: 'absolute', inset: 0, opacity: 0.25,
              color: p.tone === 'lime' ? 'var(--ink)' : 'var(--paper)',
            }"
          />
        </div>
      </div>
    </div>

    <!-- Rest of ranking -->
    <div :style="{ borderTop: '1.5px solid var(--ink)' }">
      <div
        v-for="row in rest"
        :key="row.entry.userId"
        :style="{
          display: 'flex', alignItems: 'center', gap: '12px',
          padding: '12px 4px',
          borderBottom: '1px solid var(--ink)',
          background: row.entry.isMe ? 'var(--lime)' : 'transparent',
          margin: row.entry.isMe ? '0 -6px' : '0',
          paddingLeft: row.entry.isMe ? '10px' : '4px',
          paddingRight: row.entry.isMe ? '10px' : '4px',
        }"
      >
        <div
          class="font-display"
          :style="{ fontSize: '22px', minWidth: '36px', textAlign: 'center' }"
        >{{ row.rank }}</div>

        <!-- Avatar -->
        <div
          class="font-display"
          :style="{
            width: '32px', height: '32px', borderRadius: '50%', flexShrink: 0,
            background: toneVar(deriveAccent(row.entry.userId)),
            color: toneFg(deriveAccent(row.entry.userId)),
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: '12px', letterSpacing: '0.04em',
            border: '1.5px solid var(--ink)',
            boxShadow: '1.5px 1.5px 0 var(--ink)',
          }"
        >{{ initials(row.entry.name).toUpperCase() }}</div>

        <div :style="{ flex: 1, minWidth: 0 }">
          <div class="font-display" :style="{ fontSize: '15px' }">
            {{ row.entry.name }}
            <span
              v-if="row.entry.isMe"
              class="font-mono"
              :style="{ fontSize: '10px', marginLeft: '4px' }"
            >VOCÊ</span>
          </div>
          <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7 }">
            ✓ {{ row.entry.exactHits }} exatos · {{ row.entry.resultHits }} acertos · {{ row.entry.guessesCount }} palpites
          </div>
        </div>

        <Stamp v-if="row.entry.role === 'OWNER'" tone="magenta" :rotate="3">DONO</Stamp>
        <Stamp v-else-if="row.entry.role === 'ADMIN'" tone="cobalt" :rotate="-3">ADMIN</Stamp>
        <div class="font-display" :style="{ fontSize: '22px' }">{{ row.entry.points }}</div>
        <span
          class="font-mono"
          :style="{
            fontSize: '12px', width: '16px', textAlign: 'center', flexShrink: 0,
            color: row.entry.trend === 'up' ? 'var(--lime)' : row.entry.trend === 'down' ? 'var(--coral)' : 'var(--ink)',
          }"
        >{{ row.entry.trend === 'up' ? '▲' : row.entry.trend === 'down' ? '▼' : '·' }}</span>
      </div>
    </div>

    <!-- My position (if not in top) -->
    <div
      v-if="myEntry && !leaderboard.find(e => e.isMe)"
      :style="{
        marginTop: '8px',
        display: 'flex', alignItems: 'center', gap: '12px',
        padding: '12px 10px',
        background: 'var(--lime)',
        border: '1.5px solid var(--ink)',
        margin: '8px -18px 0',
      }"
    >
      <div
        class="font-display"
        :style="{ fontSize: '22px', minWidth: '36px', textAlign: 'center' }"
      >{{ myEntry.rank }}º</div>
      <div
        class="font-display"
        :style="{
          width: '32px', height: '32px', borderRadius: '50%', flexShrink: 0,
          background: toneVar(deriveAccent(myEntry.userId)),
          color: toneFg(deriveAccent(myEntry.userId)),
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: '12px', border: '1.5px solid var(--ink)',
          boxShadow: '1.5px 1.5px 0 var(--ink)',
        }"
      >{{ initials(myEntry.name).toUpperCase() }}</div>
      <div :style="{ flex: 1 }">
        <div class="font-display" :style="{ fontSize: '15px' }">
          {{ myEntry.name }}
          <span class="font-mono" :style="{ fontSize: '10px', marginLeft: '4px' }">VOCÊ</span>
        </div>
        <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.8 }">
          ✓ {{ myEntry.exactHits }} exatos · {{ myEntry.resultHits }} acertos
        </div>
      </div>
      <div class="font-display" :style="{ fontSize: '22px' }">{{ myEntry.points }}</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Avatar from '@/components/Avatar.vue';
import { LEADERBOARD, findMember, toneVar } from '@/data/mock';

defineEmits<{ (e: 'refresh'): void }>();

const top3Order = [2, 1, 3];

const top3 = computed(() => top3Order.map(rank => {
  const entry = LEADERBOARD[rank - 1];
  const member = findMember(entry.memberId)!;
  const height = rank === 1 ? 120 : rank === 2 ? 92 : 76;
  const tone = rank === 1 ? 'lime' : rank === 2 ? 'cobalt' : 'coral';
  return { rank, entry, member, height, tone };
}));

const rest = computed(() => LEADERBOARD.slice(3).map((entry, i) => ({
  entry,
  member: findMember(entry.memberId)!,
  rank: i + 4,
})));
</script>

<template>
  <div class="fade-up" :style="{ padding: '14px 18px 18px' }">
    <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700 }"
      >PÓDIO · APÓS 22 JOGOS</span>
      <span
        class="font-mono press"
        :style="{
          fontSize: '10px', letterSpacing: '0.12em', cursor: 'pointer',
          padding: '4px 8px', border: '1.5px solid var(--ink)', borderRadius: '3px',
        }"
        @click="$emit('refresh')"
      >↻ ATUALIZAR</span>
    </div>

    <div :style="{ display: 'flex', alignItems: 'flex-end', gap: '8px', marginBottom: '18px' }">
      <div
        v-for="p in top3"
        :key="p.rank"
        :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center' }"
      >
        <Avatar :member="p.member" :size="42" />
        <div class="font-display" :style="{ fontSize: '12px', marginTop: '6px' }">
          {{ p.member.name.split(' ')[0] }}
        </div>
        <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7 }">{{ p.entry.points }} pts</div>
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

    <div :style="{ borderTop: '1.5px solid var(--ink)' }">
      <div
        v-for="row in rest"
        :key="row.entry.memberId"
        :style="{
          display: 'flex', alignItems: 'center', gap: '12px',
          padding: '12px 4px',
          borderBottom: '1px solid var(--ink)',
          background: row.member.isMe ? 'var(--lime)' : 'transparent',
          margin: row.member.isMe ? '0 -6px' : '0',
          paddingLeft: row.member.isMe ? '10px' : '4px',
          paddingRight: row.member.isMe ? '10px' : '4px',
        }"
      >
        <div
          class="font-display"
          :style="{ fontSize: '22px', minWidth: '36px', textAlign: 'center' }"
        >{{ row.rank }}</div>
        <Avatar :member="row.member" :size="32" />
        <div :style="{ flex: 1 }">
          <div class="font-display" :style="{ fontSize: '15px' }">
            {{ row.member.name }}
            <span
              v-if="row.member.isMe"
              class="font-mono"
              :style="{ fontSize: '10px', marginLeft: '4px' }"
            >VOCÊ</span>
          </div>
          <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7 }">
            ✓ {{ row.entry.exact }} EXATOS · {{ row.entry.result }} ACERTOS · {{ row.entry.guesses }}/22
          </div>
        </div>
        <div class="font-display" :style="{ fontSize: '22px' }">{{ row.entry.points }}</div>
        <span
          class="font-mono"
          :style="{
            fontSize: '12px', width: '16px', textAlign: 'center',
            color: row.entry.trend === 'up' ? 'var(--lime)' : row.entry.trend === 'down' ? 'var(--coral)' : 'var(--ink)',
          }"
        >{{ row.entry.trend === 'up' ? '▲' : row.entry.trend === 'down' ? '▼' : '·' }}</span>
      </div>
    </div>
  </div>
</template>

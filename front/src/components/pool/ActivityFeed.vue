<script setup lang="ts">
import type { ActivityItem } from '@/types';
import { toneVar, toneFg } from '@/data/mock';
import { deriveAccent } from '@/composables/usePools';

defineProps<{ items: ActivityItem[] }>();

function formatTimeAgo(iso: string): string {
  const diff = Date.now() - new Date(iso).getTime();
  const mins = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);
  if (mins < 1) return 'AGORA';
  if (mins < 60) return `HÁ ${mins} MIN`;
  if (hours < 24) return `HÁ ${hours}H`;
  return `HÁ ${days}D`;
}

function formatActor(name: string, isMe: boolean): string {
  if (isMe) return 'Você';
  const parts = name.trim().split(' ');
  if (parts.length === 1) return parts[0];
  return `${parts[0]} ${parts[parts.length - 1][0]}.`;
}

function badgeBg(points: number | null): string {
  if (points === null || points === 0) return 'transparent';
  if (points >= 3) return toneVar('lime');
  return toneVar('cobalt');
}

function badgeFg(points: number | null): string {
  if (points === null || points === 0) return 'var(--ink)';
  if (points >= 3) return toneFg('lime');
  return toneFg('cobalt');
}

function badgeLabel(points: number | null): string {
  if (points === null) return 'pendente';
  return points > 0 ? `+${points}` : '0';
}

function badgeBorder(points: number | null): string {
  return points === null ? '1.5px dashed var(--ink)' : 'none';
}
</script>

<template>
  <div :style="{ margin: '0 18px 24px' }">
    <div :style="{ marginBottom: '10px' }">
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6 }"
      >ATIVIDADE · ÚLTIMOS LANCES</div>
      <div
        class="font-display"
        :style="{ fontSize: '26px', lineHeight: 1, marginTop: '2px', textTransform: 'uppercase' }"
      >No Calor da Mesa</div>
    </div>

    <div
      v-if="items.length === 0"
      :style="{
        padding: '20px', textAlign: 'center',
        border: '1.5px dashed var(--ink)', borderRadius: '4px',
      }"
    >
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.6 }">
        SEM ATIVIDADE NO MOMENTO
      </div>
    </div>

    <div
      v-for="(item, i) in items"
      :key="item.id"
      :style="{
        display: 'flex', alignItems: 'center', gap: '10px',
        padding: '10px 0',
        borderBottom: i < items.length - 1 ? '1px dashed var(--ink)' : 'none',
      }"
    >
      <div :style="{
        width: '3px', alignSelf: 'stretch', flexShrink: 0,
        background: toneVar(deriveAccent(item.poolId)),
      }" />

      <div :style="{ flex: 1, minWidth: 0 }">
        <div
          class="font-mono"
          :style="{ fontSize: '8px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.6, marginBottom: '3px' }"
        >{{ item.poolName.toUpperCase() }} · {{ formatTimeAgo(item.createdAt) }}</div>
        <div :style="{ fontSize: '13px', lineHeight: 1.3 }">
          <span :style="{ fontWeight: 700 }">{{ formatActor(item.actor, item.isMe) }}</span>
          {{ ' ' }}{{ item.action }} {{ item.subject }}
        </div>
      </div>

      <div
        class="font-mono"
        :style="{
          flexShrink: 0,
          padding: '4px 8px',
          fontSize: '11px', fontWeight: 700, letterSpacing: '0.04em',
          background: badgeBg(item.points),
          color: badgeFg(item.points),
          border: badgeBorder(item.points),
          borderRadius: '2px',
          minWidth: '36px', textAlign: 'center',
        }"
      >{{ badgeLabel(item.points) }}</div>
    </div>
  </div>
</template>

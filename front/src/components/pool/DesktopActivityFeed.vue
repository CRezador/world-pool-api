<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { toneVar, toneFg } from '@/data/mock';
import { deriveAccent } from '@/composables/usePools';
import { useActivity } from '@/composables/useActivity';
import type { ActivityItem } from '@/types';

const TICKER_ROW_H = 54;
const MAX_VISIBLE  = 7;
const INTERVAL_MS  = 3000;
const ANIM_MS      = 620;

const { activity } = useActivity();

const offset    = ref(0);
const animating = ref(false);
const paused    = ref(false);

let timerId: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
  timerId = setInterval(() => {
    if (paused.value || activity.value.length <= MAX_VISIBLE) return;
    animating.value = true;
    setTimeout(() => {
      offset.value = (offset.value + 1) % activity.value.length;
      animating.value = false;
    }, ANIM_MS);
  }, INTERVAL_MS);
});

onUnmounted(() => { if (timerId) clearInterval(timerId); });

// Render MAX_VISIBLE + 1 rows: the extra sits just below the viewport so it slides in.
const visible = computed<Array<ActivityItem & { _k: number }>>(() => {
  const items = activity.value;
  if (!items.length) return [];
  return Array.from({ length: MAX_VISIBLE + 1 }, (_, i) => ({
    ...items[(offset.value + i) % items.length],
    _k: offset.value + i,
  }));
});

function formatTimeAgo(iso: string): string {
  const diff  = Date.now() - new Date(iso).getTime();
  const mins  = Math.floor(diff / 60_000);
  const hours = Math.floor(diff / 3_600_000);
  const days  = Math.floor(diff / 86_400_000);
  if (mins  < 1)  return 'agora';
  if (mins  < 60) return `há ${mins} min`;
  if (hours < 24) return `há ${hours}h`;
  return `há ${days}d`;
}

function formatActor(name: string, isMe: boolean): string {
  if (isMe) return 'Você';
  const parts = name.trim().split(' ');
  if (parts.length === 1) return parts[0];
  return `${parts[0]} ${parts[parts.length - 1][0]}.`;
}

function ptsBg(act: ActivityItem): string {
  if (act.points === null || act.points === 0) return 'transparent';
  return act.points >= 3 ? 'var(--lime)' : 'var(--cobalt)';
}

function ptsFg(act: ActivityItem): string {
  if (act.points === null || act.points === 0) return 'var(--ink)';
  return act.points >= 3 ? toneFg('lime') : toneFg('cobalt');
}

function ptsLabel(act: ActivityItem): string {
  if (act.points === null) return 'pendente';
  return act.points > 0 ? `+${act.points}` : '0';
}
</script>

<template>
  <div>
    <div
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '4px' }"
    >ATIVIDADE · ÚLTIMOS LANCES</div>
    <div
      class="font-display"
      :style="{ fontSize: '20px', marginBottom: '8px', textTransform: 'uppercase' }"
    >no calor da mesa</div>

    <!-- Empty state -->
    <div
      v-if="activity.length === 0"
      :style="{
        padding: '20px 14px',
        border: '1.5px dashed var(--ink)',
        borderRadius: '4px',
        textAlign: 'center',
      }"
    >
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.14em', opacity: 0.5 }">
        SEM ATIVIDADE NO MOMENTO
      </div>
    </div>

    <!-- Ticker window -->
    <div
      v-else
      :style="{
        position: 'relative',
        height: `${TICKER_ROW_H * MAX_VISIBLE}px`,
        overflow: 'hidden',
        borderTop: '1.5px solid var(--ink)',
        borderBottom: '1.5px solid var(--ink)',
        WebkitMaskImage: 'linear-gradient(to bottom, transparent 0, #000 14px, #000 calc(100% - 18px), transparent 100%)',
        maskImage: 'linear-gradient(to bottom, transparent 0, #000 14px, #000 calc(100% - 18px), transparent 100%)',
      }"
      @mouseenter="paused = true"
      @mouseleave="paused = false"
    >
      <!-- Sliding track -->
      <div :style="{
        transform: animating ? `translateY(-${TICKER_ROW_H}px)` : 'translateY(0)',
        transition: animating ? `transform ${ANIM_MS}ms cubic-bezier(.55,.05,.2,1)` : 'none',
        willChange: 'transform',
      }">
        <div
          v-for="(act, i) in visible"
          :key="act._k"
          :style="{
            height: `${TICKER_ROW_H}px`,
            boxSizing: 'border-box',
            display: 'flex', alignItems: 'center', gap: '10px',
            padding: '8px 0',
            borderBottom: '1px dashed var(--ink)',
            background: act.isMe ? 'rgba(212, 247, 92, 0.18)' : 'transparent',
            marginLeft: act.isMe ? '-8px' : 0,
            marginRight: act.isMe ? '-8px' : 0,
            paddingLeft: act.isMe ? '8px' : 0,
            paddingRight: act.isMe ? '8px' : 0,
            opacity: i === MAX_VISIBLE && !animating ? 0 : 1,
            transition: animating ? `opacity ${ANIM_MS}ms ease` : 'none',
          }"
        >
          <div :style="{
            width: '6px', alignSelf: 'stretch', flexShrink: 0,
            background: toneVar(deriveAccent(act.poolId)),
          }" />
          <div :style="{ flex: 1, minWidth: 0 }">
            <div
              class="font-mono"
              :style="{
                fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.7,
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
              }"
            >{{ act.poolName.toUpperCase() }} · {{ formatTimeAgo(act.createdAt).toUpperCase() }}</div>
            <div :style="{
              fontSize: '12px', marginTop: '2px',
              whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
            }">
              <b>{{ formatActor(act.actor, act.isMe) }}</b> {{ act.action }} {{ act.subject }}
            </div>
          </div>
          <span
            class="font-display"
            :style="{
              padding: '3px 7px', fontSize: '12px',
              background: ptsBg(act),
              border: act.points === null ? '1.5px dashed var(--ink)' : 'none',
              color: ptsFg(act),
              letterSpacing: '0.02em', borderRadius: '2px',
              whiteSpace: 'nowrap', flexShrink: 0,
            }"
          >{{ ptsLabel(act) }}</span>
        </div>
      </div>

      <!-- Coral progress bar — restarts on each tick via :key -->
      <div :style="{
        position: 'absolute', left: 0, right: 0, bottom: 0, height: '2px',
        background: 'var(--paper-3)',
      }">
        <div
          :key="`${offset}-${paused ? 'p' : 'r'}`"
          class="ticker-progress"
          :style="{
            height: '100%',
            background: 'var(--coral)',
            animation: paused ? 'none' : `tickerBar ${INTERVAL_MS}ms linear forwards`,
            width: paused ? '100%' : '0%',
            opacity: paused ? 0.4 : 1,
          }"
        />
      </div>
    </div>

    <!-- Tip box -->
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
</template>

<style>
@keyframes tickerBar {
  from { width: 0%; }
  to   { width: 100%; }
}
</style>

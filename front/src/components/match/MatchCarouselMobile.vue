<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import MatchPreviewApi from './MatchPreviewApi.vue';
import type { ApiMatch } from '@/types';

const props = withDefaults(defineProps<{
  matches: ApiMatch[];
  intervalMs?: number;
}>(), { intervalMs: 4200 });

const INTERVAL_MS = props.intervalMs;
const DOT_TONES = ['cobalt', 'magenta', 'lime', 'coral'];

const idx = ref(0);
const paused = ref(false);
const progressKey = ref(0);

let timer: ReturnType<typeof setInterval> | null = null;

function go(n: number) {
  idx.value = ((n % props.matches.length) + props.matches.length) % props.matches.length;
  progressKey.value++;
}

function startTimer() {
  if (timer) clearInterval(timer);
  if (props.matches.length <= 1) return;
  timer = setInterval(() => {
    if (!paused.value) go(idx.value + 1);
  }, INTERVAL_MS);
}

onMounted(startTimer);
onUnmounted(() => { if (timer) clearInterval(timer); });

watch(() => props.matches.length, startTimer);

// Touch swipe
const touchX = ref(0);
function onTouchStart(e: TouchEvent) {
  paused.value = true;
  touchX.value = e.touches[0].clientX;
}
function onTouchEnd(e: TouchEvent) {
  const dx = e.changedTouches[0].clientX - touchX.value;
  if (Math.abs(dx) > 40) go(idx.value + (dx < 0 ? 1 : -1));
  setTimeout(() => { paused.value = false; }, 200);
}

const trackStyle = computed(() => ({
  display: 'flex',
  transform: `translateX(-${idx.value * 100}%)`,
  transition: 'transform 600ms cubic-bezier(.55,.05,.2,1)',
  willChange: 'transform',
}));

function dotStyle(i: number) {
  const active = i === idx.value;
  return {
    width: active ? '22px' : '8px',
    height: '8px',
    padding: 0,
    border: '1.5px solid var(--ink)',
    borderRadius: '999px',
    background: active ? `var(--${DOT_TONES[i % 4]})` : 'var(--paper)',
    cursor: 'pointer',
    transition: 'width 280ms ease, background 280ms ease',
    flexShrink: '0',
  };
}
</script>

<template>
  <div v-if="matches.length">
    <!-- Slide track -->
    <div
      :style="{ position: 'relative', overflow: 'hidden', borderRadius: '6px' }"
      @mouseenter="paused = true"
      @mouseleave="paused = false"
      @touchstart.passive="onTouchStart"
      @touchend.passive="onTouchEnd"
    >
      <div :style="trackStyle">
        <div
          v-for="(m, i) in matches"
          :key="m.id"
          :style="{
            flex: '0 0 100%', minWidth: 0,
            padding: '4px 6px 8px',
            boxSizing: 'border-box',
            opacity: i === idx ? 1 : 0.55,
            transition: 'opacity 400ms ease',
          }"
        >
          <MatchPreviewApi :match="m" />
        </div>
      </div>
    </div>

    <!-- Pager: dots + rodada counter -->
    <div :style="{
      display: 'flex', alignItems: 'center', gap: '10px',
      marginTop: '12px', padding: '0 4px',
    }">
      <div :style="{ display: 'flex', gap: '7px', flex: 1, alignItems: 'center', overflow: 'hidden' }">
        <button
          v-for="(m, i) in matches"
          :key="m.id"
          :style="dotStyle(i)"
          :aria-label="`Jogo ${i + 1}`"
          @click="go(i)"
        />
      </div>
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.75, whiteSpace: 'nowrap', flexShrink: 0 }"
      >
        {{ matches[idx]?.gameDay != null ? `RODADA ${String(matches[idx].gameDay).padStart(2, '0')}` : `${String(idx + 1).padStart(2, '0')}/${String(matches.length).padStart(2, '0')}` }}
      </span>
    </div>

    <!-- Progress bar -->
    <div :style="{
      marginTop: '8px', height: '2px',
      background: 'rgba(0,0,0,0.08)', borderRadius: '1px', overflow: 'hidden',
    }">
      <div
        :key="`${idx}-${paused}`"
        :style="{
          height: '100%',
          background: 'var(--coral)',
          animation: paused ? 'none' : `tickerBar ${INTERVAL_MS}ms linear forwards`,
          width: paused ? '100%' : '0%',
          opacity: paused ? 0.35 : 1,
        }"
      />
    </div>
  </div>
</template>

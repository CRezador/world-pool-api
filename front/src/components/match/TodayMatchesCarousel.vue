<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMatch } from '@/composables/useMatch';
import { toneVar } from '@/data/mock';
import type { ApiMatch, MatchStatus } from '@/types';

const props = withDefaults(defineProps<{ intervalMs?: number }>(), { intervalMs: 4500 });

const router = useRouter();
const { todayMatches, fetchTodayMatches, upcomingMatches, fetchUpcomingMatches } = useMatch();

const STATUS_META: Record<MatchStatus, { label: string; tone: string }> = {
  SCHEDULED: { label: 'PRÓXIMOS JOGOS', tone: 'magenta' },
  IN_PROGRESS: { label: 'EM PROGRESSO', tone: 'coral' },
  FINISHED: { label: 'ENCERRADO', tone: 'cobalt' },
};

const idx = ref(0);
const paused = ref(false);

// Jogos de hoje; sem nenhum, cai para o próximo jogo agendado.
const isFallback = computed(() => todayMatches.value.length === 0);
const slides = computed<ApiMatch[]>(() =>
  isFallback.value ? upcomingMatches.value.slice(0, 1) : todayMatches.value,
);
const total = computed(() => slides.value.length);
const slide = computed<ApiMatch | undefined>(() => slides.value[idx.value]);

const meta = computed(() => (slide.value ? STATUS_META[slide.value.status] : null));
const hasScore = computed(() => slide.value?.status !== 'SCHEDULED');

function kickoffTime(raw: string | null): string {
  if (!raw) return '—';
  const d = new Date(raw);
  if (isNaN(d.getTime())) return raw;
  return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function go(delta: number) {
  if (total.value === 0) return;
  idx.value = (idx.value + delta + total.value) % total.value;
}

function open() {
  if (!slide.value) return;
  if (slide.value.groupId != null) {
    router.push(`/matches/${slide.value.groupId}`);
  } else {
    router.push({ path: '/matches', query: { phase: 'knockout' } });
  }
}

let intervalId: ReturnType<typeof setInterval> | null = null;

onMounted(async () => {
  try {
    await Promise.all([fetchTodayMatches(), fetchUpcomingMatches()]);
  } catch {
    // suppress — rejection must not leak to the next page after navigation
  }
  intervalId = setInterval(() => {
    if (!paused.value && total.value > 1) {
      idx.value = (idx.value + 1) % total.value;
    }
  }, props.intervalMs);
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<template>
  <div
    v-if="slide && meta"
    class="today-ticker"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    @click="open()"
  >
    <span
      class="font-mono today-ticker__label"
      :style="{ color: toneVar(meta.tone) }"
    >
      <span
        :style="{
          display: 'inline-block', width: '6px', height: '6px', borderRadius: '50%',
          background: toneVar(meta.tone), marginRight: '6px',
        }"
      />{{ meta.label }}
    </span>

    <span class="font-display today-ticker__teams">
      <img
        v-if="slide.home.flag_url"
        :src="slide.home.flag_url"
        :alt="slide.home.code"
        :style="{ width: '20px', height: '14px', objectFit: 'cover', borderRadius: '2px' }"
      />
      {{ slide.home.code }}
      <template v-if="hasScore">{{ slide.homeScore ?? 0 }} × {{ slide.awayScore ?? 0 }}</template>
      <template v-else>×</template>
      {{ slide.away.code }}
      <img
        v-if="slide.away.flag_url"
        :src="slide.away.flag_url"
        :alt="slide.away.code"
        :style="{ width: '20px', height: '14px', objectFit: 'cover', borderRadius: '2px' }"
      />
    </span>

    <span class="font-mono today-ticker__time">
      {{ kickoffTime(slide.kickoff) }}
    </span>

    <div v-if="total > 1" class="today-ticker__dots">
      <span
        v-for="(_, i) in slides"
        :key="i"
        :style="{
          width: i === idx ? '16px' : '6px', height: '6px', borderRadius: '999px',
          background: i === idx ? 'var(--paper)' : 'rgba(255,255,255,0.4)',
          transition: 'width 0.2s ease',
        }"
      />
    </div>
  </div>
</template>

<style scoped>
.today-ticker {
  background: var(--ink);
  color: var(--paper);
  padding: 8px 18px;
  display: flex;
  align-items: center;
  gap: 14px;
  cursor: pointer;
}
.today-ticker__label {
  font-size: 10px;
  letter-spacing: 0.18em;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  white-space: nowrap;
}
.today-ticker__teams {
  font-size: 16px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.today-ticker__time {
  font-size: 11px;
  letter-spacing: 0.08em;
  opacity: 0.7;
  white-space: nowrap;
}
.today-ticker__dots {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 5px;
}
</style>

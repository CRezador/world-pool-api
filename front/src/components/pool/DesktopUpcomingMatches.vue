<script setup lang="ts">
import { onMounted, computed, ref, watch, watchEffect } from 'vue';
import { useMatch } from '@/composables/useMatch';
import PendingMatchRow from './PendingMatchRow.vue';

const { upcomingMatches, fetchUpcomingMatches } = useMatch();
onMounted(fetchUpcomingMatches);

const slides = computed(() => {
  const pending = upcomingMatches.value;
  const groupStage = pending.filter(m => m.stage === 'GROUP_STAGE');
  const knockout = pending.filter(m => m.stage !== 'GROUP_STAGE');

  const groupBuckets: Record<string, typeof pending> = {};
  groupStage.forEach(m => {
    const g = m.group ?? 'X';
    if (!groupBuckets[g]) groupBuckets[g] = [];
    groupBuckets[g].push(m);
  });

  const groupSlides = Object.entries(groupBuckets)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([g, list]) => ({
      kind: 'group' as const,
      key: `g-${g}`,
      label: `GRUPO ${g}`,
      sub: `${list.length} ${list.length === 1 ? 'JOGO' : 'JOGOS'} · FASE DE GRUPOS`,
      matches: list,
    }));

  const koSlides = [];
  for (let i = 0; i < knockout.length; i += 2) {
    const pair = knockout.slice(i, i + 2);
    const stageLabel = pair[0].stage.replace(/_/g, ' ');
    koSlides.push({
      kind: 'pair' as const,
      key: `k-${i}`,
      label: stageLabel,
      sub: `JOGOS ${i + 1}–${Math.min(i + 2, knockout.length)} · MATA-MATA`,
      matches: pair,
    });
  }

  return [...groupSlides, ...koSlides];
});

const totalSlides = computed(() => slides.value.length || 1);
const totalPending = computed(() => upcomingMatches.value.length);

const currentIdx = ref(0);
const paused = ref(false);

watch(totalSlides, (newTotal) => {
  if (currentIdx.value >= newTotal) currentIdx.value = 0;
});

watchEffect((onCleanup) => {
  if (paused.value || totalSlides.value < 2) return;
  const t = setInterval(() => {
    currentIdx.value = (currentIdx.value + 1) % totalSlides.value;
  }, 4500);
  onCleanup(() => clearInterval(t));
});

const currentSlide = computed(() => slides.value[currentIdx.value]);

function go(delta: number) {
  currentIdx.value = (currentIdx.value + delta + totalSlides.value) % totalSlides.value;
}
</script>

<template>
  <div
    v-if="upcomingMatches.length > 0"
    :style="{
      padding: '14px', background: 'var(--ink)', color: 'var(--paper)',
      position: 'relative', overflow: 'hidden', marginBottom: '18px',
      height: '210px', display: 'flex', flexDirection: 'column',
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
  >
    <div :style="{ position: 'absolute', top: 0, right: 0, width: '110px', height: '28px', color: 'var(--coral)' }">
      <div class="halftone" :style="{ height: '100%' }" />
    </div>

    <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '8px' }">
      <div>
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--coral)' }"
        >· PENDENTE · {{ totalPending }} {{ totalPending === 1 ? 'JOGO' : 'JOGOS' }}</div>
        <div
          class="font-display"
          :style="{ fontSize: '22px', lineHeight: 1, marginTop: '6px', textTransform: 'uppercase' }"
        >Falta apitar</div>
        <div
          v-if="currentSlide"
          class="font-mono"
          :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, marginTop: '6px' }"
        >{{ currentSlide.label }} · {{ currentSlide.sub }}</div>
      </div>

      <div v-if="slides.length > 1" :style="{ display: 'flex', alignItems: 'center', gap: '4px' }">
        <button
          class="font-display"
          aria-label="Anterior"
          :style="{
            width: '26px', height: '26px', padding: 0,
            background: 'transparent', color: 'var(--paper)',
            border: '1.5px solid var(--paper)',
            fontSize: '14px', lineHeight: 1, cursor: 'pointer', borderRadius: '3px',
          }"
          @click="go(-1)"
        >‹</button>
        <button
          class="font-display"
          aria-label="Próximo"
          :style="{
            width: '26px', height: '26px', padding: 0,
            background: 'var(--paper)', color: 'var(--ink)',
            border: '1.5px solid var(--paper)',
            fontSize: '14px', lineHeight: 1, cursor: 'pointer', borderRadius: '3px',
          }"
          @click="go(1)"
        >›</button>
      </div>
    </div>

    <div
      v-if="currentSlide"
      :key="currentSlide.key"
      :style="{ marginTop: '12px', display: 'flex', flexDirection: 'column', gap: '8px', flex: 1, overflow: 'hidden' }"
    >
      <PendingMatchRow
        v-for="(m, mi) in currentSlide.matches"
        :key="m.id"
        :match="m"
        :badge="currentSlide.kind === 'group' ? (m.group ?? '?') : `J${mi + 1}`"
      />
    </div>

    <div
      v-if="slides.length > 1"
      :style="{
        display: 'flex', alignItems: 'center', justifyContent: 'space-between',
        marginTop: '12px', paddingTop: '10px',
        borderTop: '1px dashed rgba(242, 233, 210, 0.3)',
      }"
    >
      <div :style="{ display: 'flex', gap: '5px' }">
        <button
          v-for="(_, i) in slides"
          :key="i"
          :aria-label="`Slide ${i + 1}`"
          :style="{
            width: i === currentIdx ? '20px' : '6px',
            height: '6px',
            border: '1px solid var(--paper)',
            background: i === currentIdx ? 'var(--paper)' : 'transparent',
            cursor: 'pointer', padding: 0,
            transition: 'width 0.2s ease, background 0.2s ease',
            borderRadius: 0,
          }"
          @click="currentIdx = i"
        />
      </div>
      <span
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.55 }"
      >{{ currentIdx + 1 }}/{{ slides.length }} · {{ paused ? 'PAUSADO' : 'AUTO 4.5s' }}</span>
    </div>
  </div>

  <div
    v-else
    :style="{
      padding: '14px', background: 'var(--ink)', color: 'var(--paper)',
      position: 'relative', overflow: 'hidden', marginBottom: '18px',
      height: '210px',
    }"
  >
    <div :style="{ position: 'absolute', top: 0, right: 0, width: '110px', height: '28px', color: 'var(--coral)' }">
      <div class="halftone" :style="{ height: '100%' }" />
    </div>
    <div
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700, color: 'var(--coral)' }"
    >· PENDENTE</div>
    <div
      class="font-display"
      :style="{ fontSize: '22px', lineHeight: 1, marginTop: '6px', textTransform: 'uppercase' }"
    >Falta apitar</div>
    <div
      class="font-mono"
      :style="{ fontSize: '11px', letterSpacing: '0.12em', opacity: 0.6, marginTop: '12px', padding: '8px 0' }"
    >NENHUM JOGO PENDENTE</div>
  </div>
</template>

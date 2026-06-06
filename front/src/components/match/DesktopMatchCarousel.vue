<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useMatch } from '@/composables/useMatch';
import SectionHead from '@/components/SectionHead.vue';
import StageBadge from '@/components/StageBadge.vue';
import { toneVar } from '@/data/mock';

const router = useRouter();
const { upcomingMatches, fetchUpcomingMatches } = useMatch();

const TONES = ['cobalt', 'magenta', 'lime', 'coral'] as const;

const idx = ref(0);
const paused = ref(false);

const total = computed(() => upcomingMatches.value.length);
const slide = computed(() => upcomingMatches.value[idx.value]);
const tone = computed(() => TONES[idx.value % TONES.length]);
const slideColor = computed(() => toneVar(tone.value));

function go(delta: number) {
  if (total.value === 0) return;
  idx.value = (idx.value + delta + total.value) % total.value;
}

let intervalId: ReturnType<typeof setInterval> | null = null;

const loading = ref(true);

onMounted(async () => {
  try {
    await fetchUpcomingMatches();
    intervalId = setInterval(() => {
      if (!paused.value && total.value > 0) {
        idx.value = (idx.value + 1) % total.value;
      }
    }, 4500);
  } catch {
    // suppress — rejection must not leak to the next page after navigation
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<template>
  <!-- Carousel skeleton -->
  <div v-if="loading">
    <div :style="{ marginBottom: '14px' }">
      <div class="skeleton" :style="{ height: '10px', width: '130px', marginBottom: '8px' }" />
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }">
        <div class="skeleton" :style="{ height: '22px', width: '160px' }" />
        <div :style="{ display: 'flex', gap: '8px' }">
          <div class="skeleton" :style="{ width: '30px', height: '30px' }" />
          <div class="skeleton" :style="{ width: '30px', height: '30px' }" />
        </div>
      </div>
    </div>
    <div class="skeleton" :style="{ height: '210px' }" />
    <div :style="{ display: 'flex', gap: '6px', marginTop: '12px' }">
      <div v-for="n in 4" :key="n" class="skeleton" :style="{ width: '8px', height: '8px' }" />
    </div>
  </div>

  <div
    v-else-if="total > 0 && slide"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
  >
    <SectionHead
      :kicker="`PRÓXIMOS JOGOS · ${idx + 1}/${total}`"
      :title="`${slide.home.code} × ${slide.away.code}`"
    >
      <template #action>
        <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
          <button
            class="font-display press"
            aria-label="Anterior"
            :style="{
              width: '30px', height: '30px', padding: 0,
              background: 'var(--paper-2)', color: 'var(--ink)',
              border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
              fontSize: '16px', cursor: 'pointer', borderRadius: '3px',
            }"
            @click="go(-1)"
          >‹</button>
          <button
            class="font-display press"
            aria-label="Próximo"
            :style="{
              width: '30px', height: '30px', padding: 0,
              background: 'var(--ink)', color: 'var(--paper)',
              border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
              fontSize: '16px', cursor: 'pointer', borderRadius: '3px',
            }"
            @click="go(1)"
          >›</button>
        </div>
      </template>
    </SectionHead>

    <!-- Poster -->
    <div :style="{ position: 'relative', marginTop: '14px' }">
      <div
        :key="idx"
        class="perf-bottom fade-up"
        :style="{
          background: 'var(--paper-2)',
          border: '1.5px solid var(--ink)',
          boxShadow: `5px 5px 0 ${slideColor}, 5px 5px 0 1px var(--ink)`,
          padding: '18px',
          position: 'relative',
          overflow: 'hidden',
        }"
      >
        <!-- Corner halftone -->
        <div :style="{
          position: 'absolute', top: 0, right: 0,
          width: '110px', height: '22px',
          color: slideColor, pointerEvents: 'none',
        }">
          <div class="halftone" :style="{ height: '100%' }" />
        </div>

        <!-- Stage + kickoff -->
        <div :style="{
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
          marginBottom: '12px',
        }">
          <StageBadge :stage="slide.stage" :group="slide.group ?? undefined" :tone="tone" />
          <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.7 }">
            {{ slide.kickoff ?? '—' }}
          </span>
        </div>

        <!-- Teams -->
        <div :style="{
          display: 'flex', alignItems: 'center',
          justifyContent: 'space-around', gap: '12px',
        }">
          <div :style="{ textAlign: 'center' }">
            <img
              v-if="slide.home.flag_url"
              :src="slide.home.flag_url"
              :alt="slide.home.code"
              :style="{
                width: '84px', height: '56px',
                objectFit: 'cover', borderRadius: '4px',
                border: '1.5px solid var(--ink)',
                boxShadow: '3px 3px 0 var(--ink)',
              }"
            />
            <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">{{ slide.home.code }}</div>
          </div>

          <div class="font-display" :style="{ fontSize: '32px', opacity: 0.5 }">VS</div>

          <div :style="{ textAlign: 'center' }">
            <img
              v-if="slide.away.flag_url"
              :src="slide.away.flag_url"
              :alt="slide.away.code"
              :style="{
                width: '84px', height: '56px',
                objectFit: 'cover', borderRadius: '4px',
                border: '1.5px solid var(--ink)',
                boxShadow: '3px 3px 0 var(--ink)',
              }"
            />
            <div class="font-display" :style="{ fontSize: '22px', marginTop: '8px' }">{{ slide.away.code }}</div>
          </div>
        </div>

        <!-- Guess bar -->
        <div :style="{
          marginTop: '14px', padding: '10px 12px',
          background: 'var(--ink)', color: 'var(--paper)',
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
        }">
          <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">
            AINDA SEM PALPITE
          </span>
          <span
            class="font-display"
            :style="{ fontSize: '14px', color: 'var(--magenta)', cursor: 'pointer' }"
            @click="router.push(`/guess/${slide.id}`)"
          >✎ APITAR PALPITE →</span>
        </div>
      </div>
    </div>

    <!-- Dots -->
    <div :style="{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      marginTop: '12px', padding: '0 2px',
    }">
      <div :style="{ display: 'flex', gap: '6px' }">
        <button
          v-for="(_, i) in upcomingMatches"
          :key="i"
          :aria-label="`Slide ${i + 1}`"
          :style="{
            width: i === idx ? '22px' : '8px', height: '8px',
            border: '1.5px solid var(--ink)',
            background: i === idx ? 'var(--ink)' : 'var(--paper)',
            cursor: 'pointer', padding: 0,
            transition: 'width 0.2s ease, background 0.2s ease',
            borderRadius: 0,
          }"
          @click="idx = i"
        />
      </div>
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.6 }">
        {{ paused ? 'PAUSADO · MOUSE FORA P/ RETOMAR' : 'AVANÇO AUTOMÁTICO · 4.5s' }}
      </span>
    </div>
  </div>
</template>

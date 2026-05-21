<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';
import type { Pool } from '@/types';

interface Activity { result: string; detail: string; tone: string }

const props = defineProps<{
  pool: Pool;
  index: number;
  activity?: Activity;
}>();

defineEmits<{ (e: 'click'): void }>();

const accentVar = computed(() => toneVar(props.pool.accent));
const ribbonFg = computed(() => props.pool.accent === 'lime' ? 'var(--ink)' : 'var(--paper)');
const ratio = computed(() =>
  Math.max(0.06, Math.min(1, props.pool.myPoints / props.pool.leaderPoints)),
);
const fmt = new Intl.NumberFormat('pt-BR');

function activityBg(tone: string) {
  if (tone === 'paper-3') return 'var(--paper-3)';
  return toneVar(tone);
}
function activityFg(tone: string) {
  return tone === 'lime' || tone === 'paper-3' ? 'var(--ink)' : 'var(--paper)';
}
</script>

<template>
  <div
    class="perf-bottom"
    :style="{
      position: 'relative', cursor: 'pointer',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: `5px 5px 0 ${accentVar}, 5px 5px 0 1px var(--ink)`,
      borderRadius: '6px', overflow: 'hidden',
    }"
    @click="$emit('click')"
  >
    <div :style="{
      position: 'absolute', top: 0, left: 0, bottom: 0, width: '42px',
      background: accentVar,
      borderRight: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
    }">
      <div
        class="font-display"
        :style="{
          color: ribbonFg,
          fontSize: '30px', transform: 'rotate(-90deg)', letterSpacing: '0.06em',
        }"
      >0{{ index }}</div>
    </div>

    <div :style="{ paddingLeft: '54px', paddingRight: '16px', paddingTop: '14px', paddingBottom: '14px' }">
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '8px' }">
        <div :style="{ flex: 1, minWidth: 0 }">
          <div
            class="font-mono"
            :style="{
              display: 'flex', alignItems: 'center', gap: '8px',
              fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.75,
              marginBottom: '4px',
            }"
          >
            <span :style="{ background: 'var(--ink)', color: 'var(--paper)', padding: '1px 6px' }">{{ pool.code }}</span>
            <span>{{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }}</span>
            <span :style="{ opacity: 0.6 }">· {{ fmt.format(pool.members) }} sócios</span>
          </div>
          <div
            class="font-display"
            :style="{ fontSize: '24px', lineHeight: 1, letterSpacing: '0.01em', textTransform: 'uppercase' }"
          >{{ pool.name }}</div>
        </div>
        <span class="font-display" :style="{ fontSize: '18px', opacity: 0.5 }">→</span>
      </div>

      <div :style="{
        display: 'flex', alignItems: 'flex-end', gap: '14px',
        marginTop: '12px', paddingTop: '12px', borderTop: '1px dashed var(--ink)',
      }">
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">POSIÇÃO</div>
          <div class="font-display" :style="{ fontSize: '28px', lineHeight: 1 }">
            {{ pool.myRank }}<span :style="{ fontSize: '13px', opacity: 0.55 }">º/{{ fmt.format(pool.members) }}</span>
          </div>
        </div>
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">PONTOS</div>
          <div class="font-display" :style="{ fontSize: '28px', lineHeight: 1 }">
            {{ pool.myPoints }}<span :style="{ fontSize: '13px', opacity: 0.55 }">/{{ pool.leaderPoints }}</span>
          </div>
        </div>
        <div :style="{ flex: 1, textAlign: 'right' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">LÍDER</div>
          <div class="font-display" :style="{ fontSize: '14px', lineHeight: 1.05, marginTop: '2px' }">
            {{ pool.leader }}
          </div>
        </div>
      </div>

      <div :style="{
        marginTop: '10px', height: '8px',
        background: 'var(--paper)', border: '1.5px solid var(--ink)',
        position: 'relative', overflow: 'hidden',
      }">
        <div :style="{
          position: 'absolute', top: 0, bottom: 0, left: 0,
          width: `${ratio * 100}%`,
          background: accentVar,
        }" />
        <div
          class="halftone"
          :style="{
            position: 'absolute', inset: 0,
            color: 'var(--paper)', opacity: 0.45,
            mixBlendMode: 'difference', pointerEvents: 'none',
          }"
        />
      </div>

      <div
        v-if="activity"
        :style="{
          marginTop: '12px', padding: '8px 10px',
          background: 'var(--ink)', color: 'var(--paper)',
          display: 'flex', alignItems: 'center', gap: '10px', borderRadius: '3px',
        }"
      >
        <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', opacity: 0.65 }">
          ÚLTIMO PALPITE
        </span>
        <span class="font-display" :style="{ fontSize: '13px', opacity: 0.95, flex: 1 }">
          {{ activity.detail }}
        </span>
        <span
          class="font-display"
          :style="{
            padding: '3px 8px', fontSize: '13px',
            background: activityBg(activity.tone),
            color: activityFg(activity.tone),
            borderRadius: '2px',
          }"
        >{{ activity.result }}</span>
      </div>
    </div>
  </div>
</template>

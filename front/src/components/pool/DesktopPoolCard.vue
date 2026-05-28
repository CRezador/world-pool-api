<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { toneVar } from '@/data/mock';
import type { Pool } from '@/types';

const props = defineProps<{
  pool: Pool;
  index: number;
}>();

const router = useRouter();
const accentVar = computed(() => toneVar(props.pool.accent));
const ribbonFg = computed(() => props.pool.accent === 'lime' ? 'var(--ink)' : 'var(--paper)');
const ratio = computed(() =>
  Math.max(0.06, Math.min(1, props.pool.myPoints / props.pool.leaderPoints)),
);
const fmt = new Intl.NumberFormat('pt-BR');

const last3 = computed<string[]>(() =>
  props.pool.lastResults.map(n => n > 0 ? `+${n}` : '0'),
);

function chipColor(r: string) {
  return r === '+3' ? 'lime' : r !== '0' ? 'cobalt' : 'paper-3';
}
function chipBg(r: string) {
  const c = chipColor(r);
  return c === 'paper-3' ? 'var(--paper-3)' : toneVar(c);
}
function chipFg(r: string) {
  const c = chipColor(r);
  return c === 'lime' || c === 'paper-3' ? 'var(--ink)' : 'var(--paper)';
}
</script>

<template>
  <div
    class="perf-bottom"
    :style="{
      position: 'relative',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: `6px 6px 0 ${accentVar}, 6px 6px 0 1px var(--ink)`,
      borderRadius: '6px', overflow: 'hidden',
      display: 'grid', gridTemplateColumns: '54px 1fr', gap: 0,
    }"
  >
    <div :style="{
      background: accentVar,
      borderRight: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
      position: 'relative',
    }">
      <div
        class="font-display"
        :style="{
          color: ribbonFg,
          fontSize: '38px', transform: 'rotate(-90deg)', letterSpacing: '0.06em',
        }"
      >0{{ index }}</div>
    </div>

    <div :style="{ padding: '16px 20px' }">
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
        <div :style="{ flex: 1, minWidth: 0 }">
          <div
            class="font-mono"
            :style="{
              display: 'flex', alignItems: 'center', gap: '10px',
              fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700,
              marginBottom: '4px', opacity: 0.85,
            }"
          >
            <span :style="{ background: 'var(--ink)', color: 'var(--paper)', padding: '2px 7px' }">{{ pool.code }}</span>
            <span>{{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }}</span>
            <span :style="{ opacity: 0.65 }">· {{ fmt.format(pool.members) }} sócios</span>
          </div>
          <div
            class="font-display"
            :style="{ fontSize: '30px', lineHeight: 1, letterSpacing: '0.01em', textTransform: 'uppercase' }"
          >{{ pool.name }}</div>
        </div>
        <button
          class="font-display press"
          :style="{
            background: 'var(--ink)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)',
            padding: '7px 14px', fontSize: '12px', letterSpacing: '0.06em',
            textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
            flexShrink: 0,
          }"
          @click.stop="router.push(`/pool/${pool.id}`)"
        >Abrir bolão →</button>
      </div>

      <div :style="{
        display: 'grid', gridTemplateColumns: 'repeat(4, 1fr) 1.2fr',
        marginTop: '14px', padding: '12px 0',
        borderTop: '1px dashed var(--ink)',
        borderBottom: '1px dashed var(--ink)',
        gap: '10px',
      }">
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700 }">POSIÇÃO</div>
          <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1 }">
            {{ pool.myRank }}<span :style="{ fontSize: '12px', opacity: 0.55 }">º</span>
          </div>
        </div>
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700 }">PONTOS</div>
          <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1 }">
            {{ pool.myPoints }}<span :style="{ fontSize: '12px', opacity: 0.55 }">/{{ pool.leaderPoints }}</span>
          </div>
        </div>
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700 }">LÍDER</div>
          <div class="font-display" :style="{ fontSize: '14px', lineHeight: 1.1, marginTop: '4px' }">
            {{ pool.leader }}
          </div>
        </div>
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700 }">SÓCIOS</div>
          <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1 }">
            {{ fmt.format(pool.members) }}
          </div>
        </div>
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700 }">ÚLTIMOS 3</div>
          <div :style="{ display: 'flex', gap: '4px', marginTop: '4px' }">
            <span
              v-for="(r, i) in last3"
              :key="i"
              class="font-display"
              :style="{
                minWidth: '28px', padding: '2px 6px', textAlign: 'center',
                background: chipBg(r), color: chipFg(r), fontSize: '14px', borderRadius: '2px',
                border: '1px solid var(--ink)',
              }"
            >{{ r }}</span>
          </div>
        </div>
      </div>

      <div :style="{ marginTop: '12px', display: 'flex', alignItems: 'center', gap: '12px' }">
        <span
          class="font-mono"
          :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7, fontWeight: 700, minWidth: '80px' }"
        >VS LÍDER</span>
        <div :style="{
          flex: 1, height: '10px',
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
              color: 'var(--paper)', opacity: 0.4,
              mixBlendMode: 'difference', pointerEvents: 'none',
            }"
          />
        </div>
        <span
          class="font-mono"
          :style="{
            fontSize: '11px', letterSpacing: '0.08em', fontWeight: 700,
            minWidth: '64px', textAlign: 'right',
          }"
        >{{ Math.round(ratio * 100) }}%</span>
      </div>
    </div>
  </div>
</template>

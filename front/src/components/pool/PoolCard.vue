<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/data/mock';
import type { Pool } from '@/types';

const props = defineProps<{
  pool: Pool;
  index: number;
}>();

defineEmits<{ (e: 'click'): void }>();

const accentVar = computed(() => toneVar(props.pool.accent));
const ribbonFg = computed(() => props.pool.accent === 'lime' ? 'var(--ink)' : 'var(--paper)');
</script>

<template>
  <div
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
      position: 'absolute', top: 0, left: 0, bottom: 0, width: '38px',
      background: accentVar,
      borderRight: '1.5px solid var(--ink)',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
    }">
      <div
        class="font-display"
        :style="{
          color: ribbonFg,
          fontSize: '28px', transform: 'rotate(-90deg)', letterSpacing: '0.06em',
        }"
      >0{{ index }}</div>
    </div>

    <div :style="{ paddingLeft: '50px', paddingRight: '16px', paddingTop: '14px', paddingBottom: '14px' }">
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }">
        <div>
          <div
            class="font-mono"
            :style="{
              fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, marginBottom: '4px',
              color: 'var(--ink)', opacity: 0.7,
            }"
          >
            CÓDIGO
            <span :style="{ background: 'var(--ink)', color: 'var(--paper)', padding: '1px 5px' }">{{ pool.code }}</span>
            <span :style="{ marginLeft: '10px' }">{{ pool.isPublic ? 'PÚBLICO' : 'PRIVADO' }}</span>
          </div>
          <div
            class="font-display"
            :style="{ fontSize: '24px', lineHeight: 1, letterSpacing: '0.01em' }"
          >{{ pool.name }}</div>
        </div>
      </div>

      <div :style="{
        display: 'flex', alignItems: 'flex-end', gap: '12px', marginTop: '12px',
        paddingTop: '12px', borderTop: '1px dashed var(--ink)',
      }">
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">
            SUA POSIÇÃO
          </div>
          <div class="font-display" :style="{ fontSize: '28px', lineHeight: 1 }">
            {{ pool.myRank }}<span :style="{ fontSize: '14px', opacity: 0.6 }">º de {{ pool.members }}</span>
          </div>
        </div>
        <div :style="{ marginLeft: 'auto', textAlign: 'right' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.7 }">
            PONTOS
          </div>
          <div class="font-display" :style="{ fontSize: '28px', lineHeight: 1 }">
            {{ pool.myPoints }}<span :style="{ fontSize: '14px', opacity: 0.6 }">/{{ pool.leaderPoints }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

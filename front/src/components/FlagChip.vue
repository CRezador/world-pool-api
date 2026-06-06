<script setup lang="ts">
import { computed } from 'vue';
import { toneVar } from '@/utils/tone';

const props = withDefaults(defineProps<{
  flagCode?: string;
  teamName?: string;
  teamCode?: string;
  size?: number;
  mode?: 'flag' | 'code';
  tone?: string;
}>(), {
  size: 38,
  mode: 'flag',
  tone: 'ink',
});

const iso = computed(() => props.flagCode);
const displayCode = computed(() => props.teamCode ?? '?');
const displayName = computed(() => props.teamName ?? '');
const shadowColor = computed(() => toneVar(props.tone));
const visible = computed(() => Boolean(iso.value || props.teamCode));
</script>

<template>
  <template v-if="visible">
    <div
      v-if="mode === 'code'"
      class="font-display"
      :style="{
        width: `${size}px`, height: `${size}px`, borderRadius: '4px',
        background: 'var(--ink)', color: 'var(--paper)',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        fontSize: `${size * 0.42}px`, letterSpacing: '0.05em',
      }"
    >{{ displayCode }}</div>
    <div
      v-else
      :style="{
        width: `${size}px`, height: `${size}px`, borderRadius: '50%',
        background: 'var(--paper-2)',
        border: '2px solid var(--ink)',
        display: 'flex', alignItems: 'center', justifyContent: 'center',
        overflow: 'hidden',
        boxShadow: `2px 2px 0 ${shadowColor}`,
        flexShrink: 0,
      }"
    >
      <img
        v-if="iso"
        :src="`https://flagcdn.com/w160/${iso}.png`"
        :srcset="`https://flagcdn.com/w160/${iso}.png 1x, https://flagcdn.com/w320/${iso}.png 2x`"
        :alt="displayName"
        :style="{
          width: `${size + 8}px`, height: `${size + 8}px`,
          objectFit: 'cover', borderRadius: '50%',
        }"
      />
    </div>
  </template>
</template>

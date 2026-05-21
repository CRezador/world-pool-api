<script setup lang="ts">
defineProps<{ visible: boolean }>();
</script>

<template>
  <div :style="{
    height: visible ? '50px' : '0',
    transition: 'height 0.25s ease',
    overflow: 'hidden',
    display: 'flex', alignItems: 'center', justifyContent: 'center',
    color: 'var(--magenta)',
  }">
    <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
      <div class="spin" :style="{
        width: '18px', height: '18px', borderRadius: '50%',
        border: '2.5px solid var(--ink)',
        borderTopColor: 'transparent',
      }" />
      <span
        class="font-mono"
        :style="{ fontSize: '11px', letterSpacing: '0.1em', fontWeight: 700 }"
      >ATUALIZANDO PALPITES…</span>
    </div>
  </div>
</template>

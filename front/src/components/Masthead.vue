<script setup lang="ts">
withDefaults(defineProps<{
  kicker?: string;
  title?: string;
  sub?: string;
}>(), {
  kicker: 'EDIÇÃO Nº 04 · JUN 2026',
  title: 'BOLÃO COPA',
  sub: 'USA · CAN · MEX',
});
</script>

<template>
  <div :style="{
    borderBottom: '2px solid var(--ink)',
    padding: '14px 18px 12px',
    background: 'var(--paper)',
  }">
    <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end' }">
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700 }">
        {{ kicker }}
      </div>
      <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em' }">
        {{ sub }}
      </div>
    </div>
    <div
      class="font-display"
      :style="{
        fontSize: '36px', lineHeight: 0.92, letterSpacing: '0.02em',
        marginTop: '4px', textTransform: 'uppercase',
      }"
    >{{ title }}</div>
    <div
      class="font-mono"
      :style="{
        fontSize: '9px', letterSpacing: '0.18em', marginTop: '6px',
        display: 'flex', justifyContent: 'space-between',
      }"
    >
      <span>·VOL. III·</span>
      <span>BR · PT</span>
      <span>R$ 0,00</span>
    </div>
  </div>
</template>

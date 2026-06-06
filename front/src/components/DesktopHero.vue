<script setup lang="ts">
withDefaults(defineProps<{
  kicker: string;
  title: string;
  sub?: string;
  tone?: string;
  titleSize?: string;
}>(), {
  tone: 'cobalt',
  titleSize: '86px',
});
</script>

<template>
  <div :style="{
    padding: '28px 32px 22px',
    borderBottom: '1.5px solid var(--ink)',
    display: 'flex', alignItems: 'flex-end', gap: '28px', position: 'relative',
  }">
    <div :style="{ flex: 1 }">
      <div
        class="font-mono"
        :style="{ fontSize: '11px', letterSpacing: '0.22em', fontWeight: 700 }"
      >{{ kicker }}</div>
      <div
        class="font-display"
        :class="`misprint-${tone}`"
        :style="{
          fontSize: titleSize,
          lineHeight: 1,
          marginTop: '10px',
          textTransform: 'uppercase',
          letterSpacing: titleSize === '76px' ? undefined : '0.005em',
        }"
      >{{ title }}</div>
      <div
        v-if="sub"
        class="font-mono"
        :style="{ fontSize: '12px', letterSpacing: '0.14em', marginTop: '14px', opacity: 0.85 }"
      >{{ sub }}</div>
    </div>
    <slot />
  </div>
</template>

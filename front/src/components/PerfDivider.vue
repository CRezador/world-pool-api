<script setup lang="ts">
withDefaults(defineProps<{ color?: string }>(), {
  color: 'var(--ink)',
});
</script>

<template>
  <div :style="{ position: 'relative', height: '14px', margin: '4px 0' }">
    <div :style="{
      position: 'absolute', left: '-8px', top: '50%', transform: 'translateY(-50%)',
      width: '14px', height: '14px', borderRadius: '50%',
      background: 'var(--paper)', border: `1.5px solid ${color}`,
    }" />
    <div :style="{
      position: 'absolute', right: '-8px', top: '50%', transform: 'translateY(-50%)',
      width: '14px', height: '14px', borderRadius: '50%',
      background: 'var(--paper)', border: `1.5px solid ${color}`,
    }" />
    <div :style="{
      position: 'absolute', left: '14px', right: '14px', top: '50%',
      borderTop: `1.5px dashed ${color}`,
    }" />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label: string;
  modelValue: string;
}>();

defineEmits<{ (e: 'update:modelValue', v: string): void }>();
</script>

<template>
  <div>
    <div
      class="font-mono"
      :style="{ fontSize: '10px', letterSpacing: '0.14em', marginBottom: '4px' }"
    >{{ label }}</div>
    <input
      :value="modelValue"
      :style="{
        width: '100%', padding: '10px 12px',
        background: 'var(--paper)',
        border: '1.5px solid var(--ink)',
        fontSize: '15px', fontFamily: 'DM Sans, sans-serif', color: 'var(--ink)',
        outline: 'none',
      }"
      @input="(e) => $emit('update:modelValue', (e.target as HTMLInputElement).value)"
    />
  </div>
</template>

<script setup lang="ts">
defineProps<{ size?: 'sm' | 'md' | 'lg' }>()
</script>

<template>
  <svg
    :class="[
      'animate-spin text-green-600',
      size === 'lg' ? 'size-8' : size === 'sm' ? 'size-4' : 'size-6',
    ]"
    viewBox="0 0 24 24"
    fill="none"
  >
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
  </svg>
</template>

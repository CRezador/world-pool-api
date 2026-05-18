<template>
  <button
    :class="[
      variant === 'secondary'
        ? 'bg-copa-600 hover:bg-copa-700 text-white'
        : 'bg-accent-600 hover:bg-accent-700 text-white',
      size === 'sm'
        ? 'text-caption px-3 py-1'
        : 'text-body-sm px-4 py-2',
      'font-medium rounded-md transition-colors cursor-pointer',
    ]"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'secondary'
  size?: 'sm' | 'md'
}>()
</script>

<script setup lang="ts">
defineProps<{
  label?: string
  error?: string
  type?: string
}>()

const model = defineModel<string>()
</script>

<template>
  <div class="flex flex-col gap-1">
    <label v-if="label" class="text-sm font-medium text-gray-700">{{ label }}</label>
    <input
      v-model="model"
      :type="type ?? 'text'"
      :class="[
        'rounded-lg border px-3 py-2 text-sm shadow-sm outline-none transition focus:ring-2 focus:ring-green-500',
        error ? 'border-red-400 focus:ring-red-400' : 'border-gray-300',
      ]"
    />
    <span v-if="error" class="text-xs text-red-500">{{ error }}</span>
  </div>
</template>

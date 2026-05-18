<template>
  <div class="flex flex-col gap-1">
    <label
      class="text-neutral-700 text-body-sm font-medium"
      :for="id"
    >{{ label }}</label>
    <div :class="isPassword ? 'relative' : ''">
      <input
        :id="id"
        :value="modelValue"
        :type="inputType"
        :placeholder="placeholder"
        class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 text-body-sm placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-accent-300"
        :class="[isPassword ? 'pr-11' : '', error ? 'border-error focus:ring-error/40' : '']"
        @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      >
      <button
        v-if="isPassword"
        type="button"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-600"
        @click="showPassword = !showPassword"
      >
        <svg
          v-if="!showPassword"
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="1.8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
          />
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
          />
        </svg>
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="1.8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.563-4.044M6.634 6.634A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.054 10.054 0 01-4.132 5.411M3 3l18 18"
          />
        </svg>
      </button>
    </div>
    <p
      v-if="error"
      class="text-error text-caption"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
  modelValue: string
  label: string
  id: string
  type?: string
  placeholder?: string
  error?: string
}>();

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>();

const isPassword = computed(() => props.type === 'password');
const showPassword = ref(false);
const inputType = computed(() => {
  if (isPassword.value) return showPassword.value ? 'text' : 'password';
  return props.type ?? 'text';
});
</script>

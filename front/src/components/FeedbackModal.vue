<template>
  <Transition name="fade">
    <div
      v-show="true"
      class="fixed inset-0 flex items-center justify-center z-50"
      style="background: rgba(0,0,0,0.35);"
      @click.self="$emit('close')"
    >
      <div class="bg-neutral-50 rounded-2xl shadow-2xl p-8 w-80 flex flex-col items-center gap-4">
        <!-- Ícone -->
        <div
          class="w-16 h-16 rounded-full flex items-center justify-center"
          :class="success ? 'bg-success/15' : 'bg-error/15'"
        >
          <!-- Sucesso -->
          <svg
            v-if="success"
            class="w-8 h-8 text-success"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M5 13l4 4L19 7"
            />
          </svg>
          <!-- Erro -->
          <svg
            v-else
            class="w-8 h-8 text-error"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </div>

        <!-- Mensagem -->
        <p class="text-neutral-800 font-semibold text-heading-sm text-center">
          {{ message }}
        </p>

        <!-- Botão fechar -->
        <button
          class="mt-1 w-full py-2.5 rounded-xl font-medium text-body-sm transition-colors text-white"
          :class="success ? 'bg-success hover:bg-brand-700' : 'bg-error hover:bg-red-700'"
          @click="$emit('close')"
        >
          OK
        </button>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">

defineProps<{
  success: boolean;
  message: string;
}>();

defineEmits<{
  close: [];
}>();
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>

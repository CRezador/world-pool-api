<template>
  <Transition name="fade">
    <div
      class="fixed inset-0 flex items-center justify-center z-50"
      style="background: rgba(0,0,0,0.35);"
      @click.self="$emit('close')"
    >
      <div class="bg-white rounded-2xl shadow-2xl p-8 w-80 flex flex-col items-center gap-4">
        <!-- Ícone -->
        <div
          class="w-16 h-16 rounded-full flex items-center justify-center"
          :class="success ? 'bg-green-100' : 'bg-red-100'"
        >
          <!-- Sucesso -->
          <svg v-if="success" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          <!-- Erro -->
          <svg v-else class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>

        <!-- Mensagem -->
        <p class="text-gray-800 font-semibold text-lg text-center">{{ message }}</p>

        <!-- Botão fechar -->
        <button
          @click="$emit('close')"
          class="mt-1 w-full py-2.5 rounded-xl font-medium text-sm transition-colors"
          :class="success
            ? 'bg-green-500 hover:bg-green-600 text-white'
            : 'bg-red-500 hover:bg-red-600 text-white'"
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

<template>
  <div class="min-h-screen w-screen flex items-center justify-center">
    <div class="bg-white/85 backdrop-blur-md rounded-3xl px-10 py-10 w-96 shadow-2xl">
      <p class="text-green-800 text-sm font-medium mb-1">Bolão da Copa</p>
      <h1 class="text-gray-900 text-4xl font-bold mb-7">Cadastro</h1>

      <FeedbackModal
        v-if="feedback.visible"
        :success="feedback.success"
        :message="feedback.message"
        @close="feedback.visible = false"
      />

      <form class="flex flex-col gap-4" @submit.prevent="handleSubmit">
        <!-- Name -->
        <div class="flex flex-col gap-1">
          <label class="text-gray-700 text-sm font-medium" for="name">Nome</label>
          <input
            id="name"
            v-model="name"
            type="text"
            placeholder="Seu nome"
            class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-300"
          />
        </div>

        <!-- Email -->
        <div class="flex flex-col gap-1">
          <label class="text-gray-700 text-sm font-medium" for="email">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="username@gmail.com"
            class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-300"
          />
        </div>

        <!-- Password -->
        <div class="flex flex-col gap-1">
          <label class="text-gray-700 text-sm font-medium" for="password">Senha</label>
          <div class="relative">
            <input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Senha"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-11 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-300"
            />
            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              @click="showPassword = !showPassword"
            >
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.563-4.044M6.634 6.634A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.054 10.054 0 01-4.132 5.411M3 3l18 18" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="flex flex-col gap-1">
          <label class="text-gray-700 text-sm font-medium" for="confirmPassword">Confirmar Senha</label>
          <div class="relative">
            <input
              id="confirmPassword"
              v-model="confirmPassword"
              :type="showConfirmPassword ? 'text' : 'password'"
              placeholder="Confirmar Senha"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pr-11 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-300"
              :class="passwordMismatch ? 'border-red-400 focus:ring-red-300' : ''"
            />
            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              @click="showConfirmPassword = !showConfirmPassword"
            >
              <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.563-4.044M6.634 6.634A9.97 9.97 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.054 10.054 0 01-4.132 5.411M3 3l18 18" />
              </svg>
            </button>
          </div>
          <p v-if="passwordMismatch" class="text-red-500 text-xs">As senhas não coincidem.</p>
        </div>

        <!-- Register button -->
        <button
          type="submit"
          class="bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold py-3 rounded-xl w-full transition-colors"
        >
          Criar conta
        </button>

        <!-- Back to login -->
        <p class="text-center text-gray-500 text-sm">
          Já tem uma conta?
          <RouterLink to="/" class="text-orange-500 font-semibold hover:underline">Entrar</RouterLink>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import FeedbackModal from '../components/FeedbackModal.vue';
import { register } from '../services/auth.service';

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordMismatch = computed(
  () => confirmPassword.value.length > 0 && password.value !== confirmPassword.value
);

const feedback = reactive({
  visible: false,
  success: false,
  message: '',
});

async function handleSubmit() {
  if (passwordMismatch.value) return;

  try {
    await register(name.value, email.value, password.value);
    feedback.success = true;
    feedback.message = 'Conta criada com sucesso!';
  } catch {
    feedback.success = false;
    feedback.message = 'Erro ao criar conta. Tente novamente.';
  } finally {
    feedback.visible = true;
  }
}
</script>

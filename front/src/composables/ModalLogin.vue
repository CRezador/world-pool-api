<template>
  <div
    class="min-h-screen w-screen flex items-center justify-center"
  >
    <div class="bg-white/85 backdrop-blur-md rounded-3xl px-10 py-10 w-96 shadow-2xl">
      <p class="text-green-800 text-sm font-medium mb-1">Bolão da Copa</p>
      <h1 class="text-gray-900 text-4xl font-bold mb-7">Login</h1>

      <FeedbackModal
        v-if="feedback.visible"
        :success="feedback.success"
        :message="feedback.message"
        @close="feedback.visible = false"
      />

      <form class="flex flex-col gap-4" @submit.prevent="handleSubmit">
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
          <label class="text-gray-700 text-sm font-medium" for="password">Password</label>
          <div class="relative">
            <input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Password"
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

        <!-- Forgot password -->
        <div class="flex justify-end -mt-1">
          <a href="#" class="text-orange-500 text-sm font-medium hover:underline">Forgot Password?</a>
        </div>

        <!-- Sign in -->
        <button
          type="submit"
          class="bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white font-semibold py-3 rounded-xl w-full transition-colors"
        >
          Sign in
        </button>

        <!-- Divider -->
        <div class="flex items-center gap-3">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-gray-400 text-xs">ou continue com</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <!-- Google button -->
        <button
          type="button"
          class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:bg-gray-50 active:bg-gray-100 transition-all font-medium text-gray-700 text-sm"
        >
          <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          Entrar com Google
        </button>

        <!-- Register -->
        <p class="text-center text-gray-500 text-sm">
          <RouterLink to="/register" class="text-orange-500 font-semibold hover:underline">Crie uma conta</RouterLink>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import FeedbackModal from '../components/FeedbackModal.vue';
import { login } from '../services/auth.service';

const email = ref('');
const password = ref('');
const showPassword = ref(false);

const feedback = reactive({
  visible: false,
  success: false,
  message: '',
});

async function handleSubmit() {
  try {
    await login(email.value, password.value);
    feedback.success = true;
    feedback.message = 'Logado com sucesso!';
  } catch {
    feedback.success = false;
    feedback.message = 'Login inválido. Verifique suas credenciais.';
  } finally {
    feedback.visible = true;
  }
}
</script>

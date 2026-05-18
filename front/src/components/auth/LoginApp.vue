<template>
  <div
    class="h-dvh w-screen flex items-center justify-center overflow-y-auto py-4"
  >
    <div class="bg-neutral-50/85 backdrop-blur-md rounded-3xl px-8 py-7 w-96 shadow-2xl">
      <p class="text-brand-700 text-body-sm font-semibold mb-1 font-display tracking-wide uppercase">
        Bolão da Copa
      </p>
      <h1 class="text-neutral-900 font-display text-display-lg font-bold mb-5">
        Login
      </h1>

      <FeedbackModal
        v-if="feedback.visible"
        :success="feedback.success"
        :message="feedback.message"
        @close="feedback.visible = false"
      />

      <form
        class="flex flex-col gap-3"
        @submit.prevent="handleSubmit"
      >
        <InputForm
          id="email"
          v-model="email"
          label="Email"
          type="email"
          placeholder="Enter your email"
        />
        <InputForm
          id="password"
          v-model="password"
          label="Senha"
          type="password"
          placeholder="Enter your password"
        />

        <div class="flex justify-end -mt-1">
          <a
            href="#"
            class="text-accent-500 text-body-sm font-medium hover:underline"
          >Esqueceu a senha?</a>
        </div>

        <button
          type="submit"
          class="bg-accent-600 hover:bg-accent-700 active:bg-accent-800 text-white font-semibold py-2.5 rounded-xl w-full transition-colors text-body-md"
        >
          Entrar
        </button>

        <div class="flex items-center gap-3">
          <div class="flex-1 h-px bg-neutral-200" />
          <span class="text-neutral-400 text-caption">ou continue com</span>
          <div class="flex-1 h-px bg-neutral-200" />
        </div>

        <button
          type="button"
          class="flex items-center justify-center gap-3 w-full py-2.5 px-4 bg-neutral-50 border border-neutral-200 rounded-xl shadow-sm hover:shadow-md hover:bg-neutral-100 active:bg-neutral-200 transition-all font-medium text-neutral-700 text-body-sm"
        >
          <svg
            class="w-5 h-5 shrink-0"
            viewBox="0 0 24 24"
          >
            <path
              fill="#4285F4"
              d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
            />
            <path
              fill="#34A853"
              d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
            />
            <path
              fill="#FBBC05"
              d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
            />
            <path
              fill="#EA4335"
              d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
            />
          </svg>
          Entrar com Google
        </button>

        <p class="text-center text-neutral-500 text-body-sm">
          <RouterLink
            to="/register"
            class="text-accent-600 font-semibold hover:underline"
          >
            Crie uma conta
          </RouterLink>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import FeedbackModal from '../FeedbackModal.vue';
import InputForm from '../ui/InputForm.vue';
import { login } from '../../services/auth.service';
import { useAuth } from '../../composables/useAuth';

const email = ref('');
const password = ref('');

const router = useRouter();
const { checkAuth, clearUser } = useAuth();

const feedback = reactive({
  visible: false,
  success: false,
  message: '',
});

async function handleSubmit() {
  try {
    await login(email.value, password.value);
    clearUser();
    await checkAuth();
    router.replace({ name: 'dashboard' });
  } catch {
    feedback.success = false;
    feedback.message = 'Login inválido. Verifique suas credenciais.';
    feedback.visible = true;
  }
}
</script>

<template>
  <div class="min-h-screen w-screen flex items-center justify-center">
    <div class="bg-neutral-50/85 backdrop-blur-md rounded-3xl px-10 py-10 w-96 shadow-2xl">
      <p class="text-brand-700 text-body-sm font-semibold mb-1 font-display tracking-wide uppercase">
        Bolão da Copa
      </p>
      <h1 class="text-neutral-900 font-display text-display-xl font-bold mb-7">
        Cadastro
      </h1>

      <FeedbackModal
        v-if="feedback.visible"
        :success="feedback.success"
        :message="feedback.message"
        @close="feedback.visible = false"
      />

      <form
        class="flex flex-col gap-4"
        @submit.prevent="handleSubmit"
      >
        <InputForm
          id="name"
          v-model="name"
          label="Nome"
          type="text"
          placeholder="Seu nome"
        />
        <InputForm
          id="email"
          v-model="email"
          label="Email"
          type="email"
          placeholder="username@gmail.com"
        />
        <InputForm
          id="password"
          v-model="password"
          label="Senha"
          type="password"
          placeholder="Senha"
        />
        <InputForm
          id="confirmPassword"
          v-model="confirmPassword"
          label="Confirmar Senha"
          type="password"
          placeholder="Confirmar Senha"
          :error="passwordMismatch ? 'As senhas não coincidem.' : undefined"
        />

        <button
          type="submit"
          :disabled="loading"
          class="bg-accent-600 hover:bg-accent-700 active:bg-accent-800 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-xl w-full transition-colors flex items-center justify-center gap-2 text-body-md"
        >
          <svg
            v-if="loading"
            class="w-4 h-4 animate-spin"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            />
          </svg>
          {{ loading ? 'Criando conta...' : 'Criar conta' }}
        </button>

        <p class="text-center text-neutral-500 text-body-sm">
          Já tem uma conta?
          <RouterLink
            to="/"
            class="text-accent-600 font-semibold hover:underline"
          >
            Entrar
          </RouterLink>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import FeedbackModal from '../FeedbackModal.vue';
import InputForm from '../ui/InputForm.vue';
import { register } from '../../services/auth.service';

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const loading = ref(false);

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

  loading.value = true;
  try {
    await register(name.value, email.value, password.value);
    feedback.success = true;
    feedback.message = 'Conta criada com sucesso!';
  } catch (error: unknown) {
    feedback.success = false;
    feedback.message = error instanceof Error ? error.message : String(error);
  } finally {
    loading.value = false;
    feedback.visible = true;
  }
}
</script>

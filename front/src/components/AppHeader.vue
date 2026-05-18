<template>
  <header class="fixed top-0 left-0 right-0 z-50 px-6 py-4 flex items-center justify-between bg-brand-900 shadow-md">
    <span class="text-white font-display text-heading-md font-bold">Bolão da Copa</span>

    <div
      v-if="isLoggedIn"
      ref="menuRef"
      class="relative"
    >
      <button
        class="w-9 h-9 rounded-full bg-brand-700 hover:bg-brand-600 transition-colors flex items-center justify-center"
        @click="open = !open"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-5 h-5 text-white"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="1.8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
          />

        </svg>
      </button>

      <div
        v-if="open"
        class="absolute right-0 mt-2 w-48 bg-neutral-50 rounded-2xl shadow-xl border border-neutral-100 py-1 z-50"
      >
        <p class="px-4 py-2 text-caption text-neutral-400 font-medium border-b border-neutral-100">
          {{ user?.name }}
        </p>

        <RouterLink
          to="/credentials"
          class="flex items-center gap-2 px-4 py-2.5 text-body-sm text-neutral-700 hover:bg-neutral-100 transition-colors"
          @click="open = false"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4 text-neutral-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
            />
          </svg>
          Credenciais de API
        </RouterLink>

        <button
          class="w-full flex items-center gap-2 px-4 py-2.5 text-body-sm text-error hover:bg-error/10 transition-colors"
          @click="handleLogout"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-4 h-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
            />
          </svg>
          Sair
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { logout } from '../services/auth.service';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const { user, isLoggedIn, clearUser } = useAuth();

const open = ref(false);
const menuRef = ref<HTMLElement | null>(null);

function onClickOutside(e: MouseEvent) {
  if (menuRef.value && !menuRef.value.contains(e.target as Node)) {
    open.value = false;
  }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));

async function handleLogout() {
  open.value = false;
  await logout();
  clearUser();
  router.replace({ name: 'home' });
}
</script>

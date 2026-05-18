<template>
  <AppHeader />
  <AppMain v-if="isLoggedIn" />
  <main
    class="flex flex-col h-screen bg-neutral-100/60 pt-17"
    :class="isLoggedIn ? 'pl-56' : ''"
  >
    <RouterView />
  </main>
</template>

<script setup lang="ts">
import { useAuth } from './composables/useAuth';
import AppHeader from './components/AppHeader.vue';
import AppMain from './components/AppMain.vue';

const { isLoggedIn } = useAuth();
</script>

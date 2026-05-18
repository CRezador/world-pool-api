<template>
  <div class="flex-1 flex items-center justify-center">
    <p class="text-neutral-700 text-body-lg">
      Bem-vindo, {{ user?.name }}!
    </p>
  </div>
</template>

<script setup lang="ts">
import { useAuth } from '../composables/useAuth';

const { user } = useAuth();
</script>

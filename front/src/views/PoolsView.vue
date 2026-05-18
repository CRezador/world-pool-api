<template>
  <div class="w-full px-6 py-8">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-display-lg font-display font-bold text-neutral-900">
        {{ route.name === 'public-pools' ? 'Bolões Públicos' : 'Meus Bolões' }}
      </h1>
      <AppButton variant="primary">Entrar em um bolão</AppButton>
    </div>
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm">
        <PublicPools v-if="route.name === 'public-pools'" />
        <MyPools v-else />
    </div>
  </div>
</template>
<script setup lang="ts">
import PublicPools from '../components/pool/PublicPools.vue';
import MyPools from '../components/pool/MyPools.vue';
import { useRoute } from 'vue-router';
import AppButton from '../components/ui/AppButton.vue';

const route = useRoute();
</script>

<template>
  <SkeletonPoolList v-if="loading" />
  <div v-else class="flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl">
    <div
      v-for="pool in publicPools"
      :key="pool.id"
      class="flex items-center justify-between bg-neutral-50 hover:bg-neutral-100 transition-colors px-5 py-4 gap-4"
    >
      <div class="flex flex-col gap-0.5 min-w-0">
        <span class="text-heading-sm font-display font-semibold text-neutral-900 truncate">
          {{ pool.name }}
        </span>
        <span class="text-caption text-neutral-500">
          Criado por {{ pool.owner }}
        </span>
      </div>
      <AppButton variant="primary" class="shrink-0">Entrar</AppButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import AppButton from '../ui/AppButton.vue'
import SkeletonPoolList from './SkeletonPoolList.vue'
import { usePool } from '../../composables/usePool'

const { publicPools, loading, getPublicPools } = usePool()

onMounted(() => {
  getPublicPools()
})
</script>

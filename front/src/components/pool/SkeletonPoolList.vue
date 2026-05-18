<template>
  <div class="animate-pulse flex flex-col divide-y divide-neutral-200 overflow-hidden rounded-xl">
    <div
      v-for="n in 5"
      :key="n"
      class="flex items-center justify-between bg-neutral-50 px-5 py-4 gap-4"
    >
      <div class="flex flex-col gap-1.5 min-w-0">
        <SkeletonBlock class="h-4 w-48" />
        <SkeletonBlock class="h-3 w-32" />
      </div>
      <SkeletonBlock class="h-8 w-16 shrink-0 rounded-md" />
    </div>
  </div>
</template>

<script setup lang="ts">
import SkeletonBlock from '../ui/SkeletonBlock.vue'
</script>

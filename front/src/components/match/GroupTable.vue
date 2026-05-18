<template>
  <div class="grid grid-cols-2 gap-4">
    <template v-if="loading">
      <div
        v-for="n in 6"
        :key="n"
        class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden animate-pulse"
      >
        <div class="flex items-center justify-between px-4 py-3 border-b border-neutral-100">
          <SkeletonBlock class="h-5 w-20" />
          <div class="flex items-center gap-4">
            <div class="flex gap-3">
              <SkeletonBlock v-for="i in 8" :key="i" class="h-3 w-6" />
            </div>
            <SkeletonBlock class="h-7 w-20 rounded-md" />
          </div>
        </div>
        <div class="divide-y divide-neutral-100">
          <div
            v-for="r in 4"
            :key="r"
            class="flex items-center justify-between px-4 py-3"
          >
            <div class="flex items-center gap-3 flex-1">
              <SkeletonBlock class="h-3 w-3" />
              <SkeletonBlock class="h-4 w-6" />
              <SkeletonBlock class="h-3 w-32" />
            </div>
            <div class="flex gap-3">
              <SkeletonBlock v-for="i in 8" :key="i" class="h-3 w-6" />
            </div>
            <div class="w-24" />
          </div>
        </div>
      </div>
    </template>

    <div
      v-else
      v-for="standing in standings"
      :key="standing.group"
      class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden"
    >
      <div class="grid grid-cols-[1fr_repeat(8,2rem)_6rem] items-center gap-x-2 px-4 py-3 border-b border-neutral-100">
        <span class="text-heading-sm font-display font-semibold text-neutral-900">
          Grupo {{ standing.group }}
        </span>
        <span
          v-for="col in columns"
          :key="col"
          class="text-caption font-medium text-neutral-400 text-center"
        >
          {{ col }}
        </span>
        <div class="flex justify-end">
          <AppButton variant="secondary" size="sm">Ver grupo</AppButton>
        </div>
      </div>

      <div class="divide-y divide-neutral-100">
        <div
          v-for="row in standing.table"
          :key="row.team"
          class="grid grid-cols-[1fr_repeat(8,2rem)_6rem] items-center gap-x-2 px-4 py-3"
        >
          <div class="flex items-center gap-3 min-w-0">
            <span class="text-caption text-neutral-400 w-3 shrink-0">{{ row.position }}</span>
            <img
              v-if="row.crest"
              :src="row.crest"
              :alt="row.team"
              class="w-6 h-4 object-contain shrink-0 border border-neutral-200 rounded-sm"
            >
            <span class="text-body-sm text-neutral-800 truncate">{{ row.team }}</span>
          </div>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.played }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.won }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.draw }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.lost }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.goals_for }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.goals_against }}</span>
          <span class="text-body-sm text-neutral-600 text-center">{{ row.goal_diff }}</span>
          <span class="text-body-sm font-semibold text-neutral-900 text-center">{{ row.points }}</span>
          <div />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useStandings } from '../../composables/useStandings'
import AppButton from '../ui/AppButton.vue'
import SkeletonBlock from '../ui/SkeletonBlock.vue'

const { standings, loading, getStandings } = useStandings()

const columns = ['J', 'C', 'E', 'D', 'M', 'S', 'DG', 'Pts']

onMounted(() => {
  getStandings()
})
</script>

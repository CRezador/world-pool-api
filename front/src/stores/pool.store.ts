import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Pool } from '@/types/pool'

export const usePoolStore = defineStore('pool', () => {
  const currentPool = ref<Pool | null>(null)

  function setPool(pool: Pool) {
    currentPool.value = pool
  }

  function clear() {
    currentPool.value = null
  }

  return { currentPool, setPool, clear }
})

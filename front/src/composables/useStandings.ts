import { ref } from 'vue'
import api from '../services/api'
import type { Standing } from '../types/Standings'

export function useStandings() {
  const standings = ref<Standing[]>([])
  const loading = ref(false)

  async function getStandings(): Promise<void> {
    loading.value = true
    try {
      const response = await api.get('/standings')
      standings.value = response.data.data
    } finally {
      loading.value = false
    }
  }

  return { standings, loading, getStandings }
}

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/user'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAdmin = computed(() => user.value?.role === 'admin')

  function setToken(value: string) {
    token.value = value
    localStorage.setItem('token', value)
  }

  function setUser(value: User) {
    user.value = value
  }

  function clear() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isAdmin, setToken, setUser, clear }
})

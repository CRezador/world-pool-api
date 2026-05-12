import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { requiresGuest: true },
    },
    {
      path: '/login',
      redirect: '/',
    },
    {
      path: '/register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { requiresGuest: true },
    },

    // Pools
    {
      path: '/pools',
      component: () => import('@/views/pool/PoolListView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/new',
      component: () => import('@/views/pool/PoolCreateView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/join',
      component: () => import('@/views/pool/PoolJoinView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/:id',
      component: () => import('@/views/pool/PoolDetailView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/:id/members',
      component: () => import('@/views/pool/PoolMembersView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/:id/settings',
      component: () => import('@/views/pool/PoolSettingsView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/:id/guesses',
      component: () => import('@/views/pool/PoolGuessesView.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/pools/:id/leaderboard',
      component: () => import('@/views/pool/PoolLeaderboardView.vue'),
      meta: { requiresAuth: true },
    },

    // Admin
    {
      path: '/admin/matches',
      component: () => import('@/views/admin/AdminMatchesView.vue'),
      meta: { requiresAuth: true, requiresAdmin: true },
    },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.token) {
    return '/login'
  }

  if (to.meta.requiresGuest && auth.token) {
    return '/pools'
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return '/pools'
  }
})

export default router

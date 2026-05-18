import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import DashboardView from '../views/DashboardView.vue'
import { useAuth } from '../composables/useAuth'
import PoolsView from '../views/PoolsView.vue'
import GroupView from '../views/GroupView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    {
      path: '/public-pools',
      name: 'public-pools',
      component: PoolsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/my-pools',
      name: 'my-pools',
      component: PoolsView,
      meta: { requiresAuth: true },
    },
    {
      path: '/groups',
      name: 'groups',
      component: GroupView,
      meta: { requiresAuth: true },
    }
  ],
})

router.beforeEach(async (to) => {
  const { checkAuth, isLoggedIn } = useAuth();

  await checkAuth();

  if (to.meta.requiresAuth && !isLoggedIn.value) {
    return { name: 'home' };
  }

  if (to.name === 'home' && isLoggedIn.value) {
    return { name: 'dashboard' };
  }
});

export default router

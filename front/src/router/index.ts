import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const PUBLIC_ROUTES = ['login'];

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { hideTabs: true },
  },
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/HomeView.vue'),
    meta: { desktopLayout: true },
  },
  {
    path: '/create',
    name: 'create',
    component: () => import('@/views/CreatePoolView.vue'),
  },
  {
    path: '/explore',
    name: 'explore',
    component: () => import('@/views/ExplorePoolsView.vue'),
    meta: { desktopLayout: true },
  },
  {
    path: '/pools',
    name: 'my-pools',
    component: () => import('@/views/MyPoolsView.vue'),
    meta: { desktopLayout: true },
  },
  {
    path: '/pool/:id',
    name: 'pool',
    component: () => import('@/views/PoolDetailView.vue'),
    meta: { desktopLayout: true },
  },
  {
    path: '/matches',
    name: 'matches',
    component: () => import('@/views/MatchesView.vue'),
    meta: { desktopLayout: true },
  },
  {
    path: '/guess/:matchId',
    name: 'guess',
    component: () => import('@/views/GuessView.vue'),
  },
  {
    path: '/match/:matchId',
    name: 'match-detail',
    component: () => import('@/views/MatchDetailView.vue'),
  },
  {
    path: '/standings',
    name: 'standings',
    component: () => import('@/views/StandingsView.vue'),
    meta: { desktopLayout: true },
  },
  { path: '/:pathMatch(.*)*', redirect: '/' },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0 }; },
});

router.beforeEach(async (to) => {
  const { checkAuth, isLoggedIn } = useAuth();
  await checkAuth();
  if (!isLoggedIn.value && !PUBLIC_ROUTES.includes(to.name as string)) {
    return { name: 'login' };
  }
});

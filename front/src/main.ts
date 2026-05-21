import { createApp } from 'vue';
import App from './App.vue';
import { router } from './router';
import { useAuth } from './composables/useAuth';
import api from './services/api';
import './styles/base.css';

const { clearUser } = useAuth();

api.interceptors.response.use(
  (response) => response,
  (error) => {
    const isAuthCheck = error.config?.url === '/me';
    if (error.response?.status === 401 && !isAuthCheck) {
      clearUser();
      router.push({ name: 'login' });
    }
    return Promise.reject(error);
  }
);

createApp(App).use(router).mount('#app');

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import BottomTabs from '@/components/BottomTabs.vue';

const route = useRoute();
const showTabs = computed(() => route.meta.hideTabs !== true);
</script>

<template>
  <div class="grain mobile-shell">
    <main class="screen-scroll no-scrollbar">
      <RouterView v-slot="{ Component }">
        <Transition name="route-fade" mode="out-in">
          <component :is="Component" :key="$route.path" />
        </Transition>
      </RouterView>
    </main>
    <BottomTabs v-if="showTabs" />
  </div>
</template>

<style scoped>
.mobile-shell {
  width: 100%;
  max-width: 480px;
  margin: 0 auto;
  min-height: 100dvh;
  height: 100dvh;
  display: flex;
  flex-direction: column;
  background: var(--paper);
  color: var(--ink);
  position: relative;
  overflow: hidden;
  box-shadow: 0 0 0 1px rgba(0,0,0,0.04);
}

.screen-scroll {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}

@media (min-width: 1024px) {
  .mobile-shell {
    max-width: none;
    height: auto;
    min-height: 100dvh;
    box-shadow: none;
  }
}
</style>

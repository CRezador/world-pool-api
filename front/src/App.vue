<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import MobileShell from '@/layouts/MobileShell.vue';
import DesktopShell from '@/layouts/DesktopShell.vue';
import JoinCodeModal from '@/components/modals/JoinCodeModal.vue';
import GuessModal from '@/components/modals/GuessModal.vue';
import CreatePoolModal from '@/components/modals/CreatePoolModal.vue';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useJoinModal } from '@/composables/useJoinModal';
import { useGuessModal } from '@/composables/useGuessModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';

const route = useRoute();
const { isDesktop } = useBreakpoint();

const useDesktopShell = computed(() => isDesktop.value && route.meta.desktopLayout === true);
const useAuthCenter = computed(() => isDesktop.value && route.meta.desktopLayout !== true);

const join = useJoinModal();
const guess = useGuessModal();
const create = useCreatePoolModal();

const modalVariant = computed<'compact' | 'desktop'>(() => isDesktop.value ? 'desktop' : 'compact');
</script>

<template>
  <DesktopShell v-if="useDesktopShell" />
  <div v-else-if="useAuthCenter" class="auth-fill">
    <RouterView />
  </div>
  <MobileShell v-else />

  <JoinCodeModal
    :open="join.open.value"
    :variant="modalVariant"
    @close="join.hide()"
    @accept="join.hide()"
  />
  <GuessModal
    :match="guess.match.value"
    :variant="modalVariant"
    @close="guess.hide()"
    @submit="guess.hide()"
  />
  <CreatePoolModal
    :open="create.open.value"
    :variant="modalVariant"
    @close="create.hide()"
    @create="create.hide()"
  />
</template>

<style scoped>
.auth-fill {
  width: 100%;
  min-height: 100vh;
}
</style>

import { ref, onMounted, onBeforeUnmount } from 'vue';

const DESKTOP_MIN = 1024;

export function useBreakpoint() {
  const isDesktop = ref(false);

  function update() {
    isDesktop.value = window.innerWidth >= DESKTOP_MIN;
  }

  onMounted(() => {
    update();
    window.addEventListener('resize', update);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('resize', update);
  });

  return { isDesktop };
}

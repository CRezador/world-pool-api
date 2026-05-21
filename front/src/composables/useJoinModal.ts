import { ref } from 'vue';

const open = ref(false);

export function useJoinModal() {
  return {
    open,
    show() { open.value = true; },
    hide() { open.value = false; },
  };
}

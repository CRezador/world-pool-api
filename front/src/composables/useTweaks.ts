import { reactive, watchEffect } from 'vue';
import type { Tone } from '@/types';

export interface Tweaks {
  dark: boolean;
  accent: Extract<Tone, 'magenta' | 'cobalt' | 'lime'>;
}

const STORAGE_KEY = 'bolao-copa.tweaks';

function load(): Tweaks {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) return { dark: false, accent: 'magenta', ...JSON.parse(raw) };
  } catch {}
  return { dark: false, accent: 'magenta' };
}

const state = reactive<Tweaks>(load());

watchEffect(() => {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  } catch {}
  document.documentElement.classList.toggle('theme-dark', state.dark);
});

export function useTweaks() {
  function setTweak<K extends keyof Tweaks>(key: K, value: Tweaks[K]) {
    state[key] = value;
  }
  return { tweaks: state, setTweak };
}

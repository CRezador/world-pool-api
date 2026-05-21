<script setup lang="ts">
import { ref } from 'vue';
import { useTweaks } from '@/composables/useTweaks';

const { tweaks, setTweak } = useTweaks();
const open = ref(false);

const accents: Array<{ value: 'magenta' | 'cobalt' | 'lime'; label: string }> = [
  { value: 'magenta', label: 'Magenta' },
  { value: 'cobalt', label: 'Cobalto' },
  { value: 'lime', label: 'Lima' },
];
</script>

<template>
  <div class="tweaks-root">
    <button
      class="font-display tweaks-fab press"
      :aria-expanded="open"
      :title="open ? 'Fechar tweaks' : 'Abrir tweaks'"
      @click="open = !open"
    >
      <span :style="{ transform: open ? 'rotate(45deg)' : 'none', display: 'inline-block', transition: 'transform 0.18s ease' }">+</span>
    </button>

    <div v-if="open" class="tweaks-panel">
      <div class="tweaks-title font-display">TWEAKS · BOLÃO COPA</div>

      <section class="tweaks-section">
        <div class="font-mono tweaks-label">TEMA</div>
        <div class="tweaks-row">
          <button
            class="font-mono tweak-chip"
            :class="{ active: !tweaks.dark }"
            @click="setTweak('dark', false)"
          >CLARO</button>
          <button
            class="font-mono tweak-chip"
            :class="{ active: tweaks.dark }"
            @click="setTweak('dark', true)"
          >ESCURO</button>
        </div>
      </section>

      <section class="tweaks-section">
        <div class="font-mono tweaks-label">SOTAQUE</div>
        <div class="tweaks-row">
          <button
            v-for="a in accents"
            :key="a.value"
            class="font-mono tweak-chip"
            :class="{ active: tweaks.accent === a.value }"
            @click="setTweak('accent', a.value)"
          >{{ a.label.toUpperCase() }}</button>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.tweaks-root {
  position: fixed;
  bottom: 18px;
  right: 18px;
  z-index: 100;
}

.tweaks-fab {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1.5px solid var(--ink);
  background: var(--paper-2);
  color: var(--ink);
  font-size: 28px;
  line-height: 1;
  cursor: pointer;
  box-shadow: 3px 3px 0 var(--ink);
}

.tweaks-panel {
  position: absolute;
  bottom: 64px;
  right: 0;
  background: var(--paper);
  border: 1.5px solid var(--ink);
  box-shadow: 4px 4px 0 var(--ink);
  padding: 14px;
  min-width: 230px;
  border-radius: 4px;
}

.tweaks-title {
  font-size: 14px;
  letter-spacing: 0.04em;
  margin-bottom: 12px;
  text-transform: uppercase;
}

.tweaks-section + .tweaks-section { margin-top: 12px; }

.tweaks-label {
  font-size: 10px;
  letter-spacing: 0.14em;
  font-weight: 700;
  margin-bottom: 6px;
  opacity: 0.7;
}

.tweaks-row {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.tweak-chip {
  padding: 6px 10px;
  background: var(--paper-2);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  font-size: 11px;
  letter-spacing: 0.1em;
  font-weight: 700;
  cursor: pointer;
  border-radius: 3px;
}

.tweak-chip.active {
  background: var(--ink);
  color: var(--paper);
}
</style>

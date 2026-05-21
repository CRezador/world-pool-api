<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';

const props = withDefaults(defineProps<{
  open: boolean;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'accept', code: string): void;
}>();

const code = ref(['B','A','R','R','E','S']);
const inputs = ref<HTMLInputElement[]>([]);

function setRef(el: Element | any, i: number) {
  if (el) inputs.value[i] = el as HTMLInputElement;
}

function setChar(i: number, raw: string) {
  const v = (raw || '').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(-1);
  const next = [...code.value];
  next[i] = v;
  code.value = next;
  if (v && i < 5) nextTick(() => inputs.value[i + 1]?.focus());
}

function onKeyDown(i: number, e: KeyboardEvent) {
  if (e.key === 'Backspace' && !code.value[i] && i > 0) {
    inputs.value[i - 1]?.focus();
  }
}

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape') emit('close');
}

onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

watch(() => props.open, (v) => {
  if (v) nextTick(() => inputs.value[0]?.focus());
});

function accept() {
  emit('accept', code.value.join(''));
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="open"
        class="modal-backdrop"
        :style="{ padding: variant === 'desktop' ? '28px' : '14px' }"
        @click="$emit('close')"
      >
        <div
          class="perf-bottom modal-card"
          :class="variant"
          @click.stop
        >
          <div :style="{
            position: 'absolute', top: 0, right: 0,
            width: variant === 'desktop' ? '100px' : '70px',
            height: variant === 'desktop' ? '28px' : '22px',
            color: 'var(--magenta)', pointerEvents: 'none',
          }">
            <div class="halftone" :style="{ height: '100%' }" />
          </div>

          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
            <div>
              <div
                class="font-mono"
                :style="{
                  fontSize: variant === 'desktop' ? '10px' : '9px',
                  letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75,
                }"
              >VOUCHER Nº 0001 · CONVITE</div>
              <div
                class="font-display"
                :style="{
                  fontSize: variant === 'desktop' ? '36px' : '26px',
                  lineHeight: 0.95,
                  marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.01em',
                }"
              >Entrar com<br />código</div>
            </div>
            <button
              aria-label="Fechar"
              class="font-display press modal-close"
              @click="$emit('close')"
            >✕</button>
          </div>

          <div
            class="font-mono"
            :style="{
              fontSize: variant === 'desktop' ? '11px' : '10px',
              letterSpacing: '0.14em',
              marginTop: variant === 'desktop' ? '18px' : '14px',
              marginBottom: '8px',
            }"
          >DIGITE OS 6 CARACTERES DO BOLÃO</div>

          <div :style="{ display: 'flex', gap: variant === 'desktop' ? '10px' : '6px', marginBottom: '12px' }">
            <input
              v-for="(c, i) in code"
              :key="i"
              :ref="(el) => setRef(el, i)"
              class="font-display code-input"
              maxlength="1"
              :value="c"
              :style="{
                height: variant === 'desktop' ? '84px' : '60px',
                background: i === 5 ? 'var(--cobalt)' : 'var(--paper)',
                color: i === 5 ? 'var(--paper)' : 'var(--ink)',
                boxShadow: i === 5 ? '0 0 0 2px var(--cobalt)' : 'none',
                fontSize: variant === 'desktop' ? '44px' : '28px',
              }"
              @input="(e) => setChar(i, (e.target as HTMLInputElement).value)"
              @keydown="(e) => onKeyDown(i, e)"
            />
          </div>

          <div
            class="font-mono"
            :style="{
              fontSize: variant === 'desktop' ? '11px' : '10px',
              letterSpacing: '0.12em', opacity: 0.85,
              display: 'flex', justifyContent: 'space-between',
              alignItems: 'center', gap: '8px', flexWrap: 'wrap',
            }"
          >
            <span>código válido ✓</span>
            <span :style="{ color: 'var(--cobalt)' }">RESENHA DO BAR · 12 sócios</span>
          </div>

          <PerfDivider />

          <div :style="{ display: 'flex', gap: '8px', marginTop: '14px' }">
            <button class="font-display press modal-cancel" @click="$emit('close')">Cancelar</button>
            <PrintButton tone="cobalt" full @click="accept">Aceitar convite</PrintButton>
          </div>

          <div :style="{ marginTop: '14px', paddingTop: '12px', borderTop: '1px dashed var(--ink)' }">
            <span
              class="font-mono"
              :style="{
                fontSize: variant === 'desktop' ? '11px' : '10px',
                letterSpacing: '0.1em', opacity: 0.7,
              }"
            >
              Não tem código? <u :style="{ cursor: 'pointer' }">Ver bolões públicos</u>
              ·
              <u :style="{ cursor: 'pointer' }">Criar do zero</u>
            </span>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 200;
  background: rgba(20, 17, 14, 0.6);
  backdrop-filter: blur(3px);
  -webkit-backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-card {
  position: relative;
  background: var(--paper);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  border-radius: 4px;
  width: 100%;
}
.modal-card.compact {
  max-width: 360px;
  padding: 18px;
  box-shadow: 5px 5px 0 var(--cobalt), 5px 5px 0 1px var(--ink);
}
.modal-card.desktop {
  max-width: 560px;
  padding: 24px;
  box-shadow: 7px 7px 0 var(--cobalt), 7px 7px 0 1px var(--ink);
}

.modal-close {
  background: var(--paper-2);
  border: 1.5px solid var(--ink);
  box-shadow: 2px 2px 0 var(--ink);
  cursor: pointer;
  width: 32px;
  height: 32px;
  font-size: 14px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  border-radius: 3px;
  flex-shrink: 0;
  color: var(--ink);
}

.modal-cancel {
  background: var(--paper-2);
  color: var(--ink);
  border: 1.5px solid var(--ink);
  box-shadow: 3px 3px 0 var(--ink);
  padding: 10px 14px;
  font-size: 13px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  cursor: pointer;
  border-radius: 4px;
  flex-shrink: 0;
}
.modal-card.desktop .modal-cancel {
  padding: 12px 18px;
  font-size: 15px;
}

.code-input {
  flex: 1;
  min-width: 0;
  border: 1.5px solid var(--ink);
  text-align: center;
  letter-spacing: 0.02em;
  outline: none;
  padding: 0;
  text-transform: uppercase;
  transition: all 0.18s ease;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.18s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>

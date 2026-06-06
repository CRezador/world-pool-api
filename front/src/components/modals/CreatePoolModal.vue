<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import FormField from '@/components/FormField.vue';
import { useJoinModal } from '@/composables/useJoinModal';
import { createPool } from '@/composables/usePools';
import { router } from '@/router';

const props = withDefaults(defineProps<{
  open: boolean;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const join = useJoinModal();
const joinCode = () => {
emit('close');
join.show();
} 


const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'create', payload: { name: string; isPublic: boolean; code: string }): void;
}>();

const name = ref('Quinta dos amigos');
const isPublic = ref(false);

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.open) emit('close');
}

onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

function submit() {
  const createPoolRequest = createPool(name.value, isPublic.value).then((response: any) => {
    const pool = response.data.data.id;
    console.log(pool);
    router.push(`/pool/${pool}`);
    emit('close');
  }).catch((e: any) => {
    alert(e.response?.data?.message || 'Erro ao criar o bolão. Tente novamente.');
  });
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
            color: 'var(--lime)', pointerEvents: 'none',
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
              >FICHA Nº 001/26 · NOVO BOLÃO</div>
              <div
                class="font-display"
                :style="{
                  fontSize: variant === 'desktop' ? '36px' : '26px',
                  lineHeight: 0.95,
                  marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.01em',
                }"
              >Fundar<br />um bolão</div>
            </div>
            <button
              aria-label="Fechar"
              class="font-display press modal-close"
              @click="$emit('close')"
            >✕</button>
          </div>

          <div :style="{ marginTop: variant === 'desktop' ? '18px' : '14px' }">
            <FormField label="NOME DO BOLÃO" v-model="name" />
          </div>

          <div :style="{ marginTop: '14px' }">
            <div
              class="font-mono"
              :style="{ fontSize: '10px', letterSpacing: '0.14em', marginBottom: '6px' }"
            >VISIBILIDADE</div>
            <div :style="{ display: 'flex', gap: '8px' }">
              <ChipToggle :active="!isPublic" @click="isPublic = false">PRIVADO</ChipToggle>
              <ChipToggle :active="isPublic" @click="isPublic = true">PÚBLICO</ChipToggle>
            </div>
            <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7, marginTop: '6px' }">
              {{ isPublic
                ? 'Aparece na busca. Qualquer um entra.'
                : 'Só entra quem tem o código de 6 dígitos.' }}
            </div>
          </div>

          <PerfDivider />

          <div :style="{ display: 'flex', gap: '8px', marginTop: '16px' }">
            <button class="font-display press modal-cancel" @click="$emit('close')">Cancelar</button>
            <PrintButton tone="lime" full @click="submit">Fundar o bolão</PrintButton>
          </div>

          <div :style="{ marginTop: '14px', paddingTop: '12px', borderTop: '1px dashed var(--ink)' }">
            <span
              class="font-mono"
              :style="{
                fontSize: variant === 'desktop' ? '11px' : '10px',
                letterSpacing: '0.1em', opacity: 0.7,
              }"
            >
              Já tem código de outro bolão? <a @click="joinCode" :style="{ cursor: 'pointer' }"><u>Entrar com código</u></a>
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
  z-index: 300;
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
  box-shadow: 5px 5px 0 var(--lime), 5px 5px 0 1px var(--ink);
}
.modal-card.desktop {
  max-width: 560px;
  padding: 24px;
  box-shadow: 7px 7px 0 var(--lime), 7px 7px 0 1px var(--ink);
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

.code-regen {
  background: transparent;
  color: var(--lime);
  border: 1.5px solid var(--lime);
  padding: 6px 10px;
  font-size: 11px;
  letter-spacing: 0.12em;
  font-weight: 700;
  cursor: pointer;
  border-radius: 3px;
  flex-shrink: 0;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.18s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>

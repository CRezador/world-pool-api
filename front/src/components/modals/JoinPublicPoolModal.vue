<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import PrintButton from '@/components/PrintButton.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import { joinPublicPool } from '@/composables/usePools';
import type { Pool } from '@/types';
import { toneVar, toneFg } from '@/data/mock';

const props = withDefaults(defineProps<{
  open: boolean;
  pool: Pool | null;
  variant?: 'compact' | 'desktop';
}>(), {
  variant: 'desktop',
});

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'joined'): void;
}>();

const router = useRouter();
const loading = ref(false);
const error = ref('');

const fmt = new Intl.NumberFormat('pt-BR');

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape') emit('close');
}

onMounted(() => window.addEventListener('keydown', onKey));
onBeforeUnmount(() => window.removeEventListener('keydown', onKey));

async function join() {
  if (!props.pool || loading.value) return;
  loading.value = true;
  error.value = '';
  try {
    await joinPublicPool(props.pool.id);
    emit('joined');
    router.push(`/pool/${props.pool.id}`);
    emit('close');
  } catch (e: any) {
    error.value = e.response?.data?.message ?? 'Erro ao entrar no bolão.';
  } finally {
    loading.value = false;
  }
}

function viewPool() {
  if (!props.pool) return;
  router.push(`/pool/${props.pool.id}`);
  emit('close');
}
</script>

<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="open && pool"
        class="modal-backdrop"
        :style="{ padding: variant === 'desktop' ? '28px' : '14px' }"
        @click="$emit('close')"
      >
        <div
          class="perf-bottom modal-card"
          :class="variant"
          @click.stop
        >
          <!-- Corner halftone -->
          <div :style="{
            position: 'absolute', top: 0, right: 0,
            width: variant === 'desktop' ? '100px' : '70px',
            height: variant === 'desktop' ? '28px' : '22px',
            color: toneVar(pool.accent), pointerEvents: 'none',
          }">
            <div class="halftone" :style="{ height: '100%' }" />
          </div>

          <!-- Header -->
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: '12px' }">
            <div>
              <div
                class="font-mono"
                :style="{
                  fontSize: variant === 'desktop' ? '10px' : '9px',
                  letterSpacing: '0.2em', fontWeight: 700, opacity: 0.75,
                }"
              >BOLÃO PÚBLICO · CONVITE ABERTO</div>
              <div
                class="font-display"
                :style="{
                  fontSize: variant === 'desktop' ? '34px' : '26px',
                  lineHeight: 0.95,
                  marginTop: '4px', textTransform: 'uppercase', letterSpacing: '0.01em',
                }"
              >{{ pool.name }}</div>
            </div>
            <button
              aria-label="Fechar"
              class="font-display press modal-close"
              @click="$emit('close')"
            >✕</button>
          </div>

          <!-- Pool tile + stats -->
          <div :style="{
            display: 'flex', alignItems: 'center', gap: '14px',
            marginTop: variant === 'desktop' ? '18px' : '14px',
            padding: '14px',
            background: 'var(--paper-2)',
            border: '1.5px solid var(--ink)',
          }">
            <div
              class="font-display"
              :style="{
                width: variant === 'desktop' ? '52px' : '44px',
                height: variant === 'desktop' ? '52px' : '44px',
                flexShrink: 0,
                background: toneVar(pool.accent),
                color: toneFg(pool.accent),
                border: '1.5px solid var(--ink)',
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                fontSize: variant === 'desktop' ? '22px' : '18px',
                transform: 'rotate(-6deg)',
              }"
            >{{ pool.code.slice(0, 2) }}</div>

            <div :style="{ flex: 1, minWidth: 0 }">
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.6, marginBottom: '6px' }"
              >SÓCIOS · LÍDER</div>
              <div :style="{ display: 'flex', gap: '18px', flexWrap: 'wrap' }">
                <div>
                  <div
                    class="font-display"
                    :style="{ fontSize: variant === 'desktop' ? '28px' : '22px', lineHeight: 1 }"
                  >{{ fmt.format(pool.members) }}</div>
                  <div
                    class="font-mono"
                    :style="{ fontSize: '9px', letterSpacing: '0.12em', opacity: 0.6, marginTop: '2px' }"
                  >SÓCIOS</div>
                </div>
                <div :style="{ minWidth: 0 }">
                  <div
                    class="font-display"
                    :style="{
                      fontSize: variant === 'desktop' ? '20px' : '16px', lineHeight: 1,
                      whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
                    }"
                  >{{ pool.leader }}</div>
                  <div
                    class="font-mono"
                    :style="{ fontSize: '9px', letterSpacing: '0.12em', opacity: 0.6, marginTop: '2px' }"
                  >LÍDER ATUAL</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Already member state -->
          <div
            v-if="pool.isMember"
            :style="{
              marginTop: '14px', padding: '12px 14px',
              background: toneVar('lime'), color: toneFg('lime'),
              border: '1.5px solid var(--ink)',
              display: 'flex', alignItems: 'center', gap: '10px',
            }"
          >
            <span class="font-display" :style="{ fontSize: '20px' }">✓</span>
            <div>
              <div
                class="font-display"
                :style="{ fontSize: variant === 'desktop' ? '18px' : '15px', lineHeight: 1 }"
              >Você já é sócio deste bolão</div>
              <div
                class="font-mono"
                :style="{ fontSize: '9px', letterSpacing: '0.12em', marginTop: '3px', opacity: 0.8 }"
              >ACESSE PARA VER SEU RANKING E PALPITES</div>
            </div>
          </div>

          <!-- Join confirm text -->
          <div
            v-else
            class="font-mono"
            :style="{
              fontSize: variant === 'desktop' ? '11px' : '10px',
              letterSpacing: '0.12em', opacity: 0.75,
              marginTop: '14px', lineHeight: 1.5,
            }"
          >
            Ao entrar, você poderá palpitar nos jogos e competir no ranking deste bolão. Qualquer palpite já realizado por você nesta copa valerá retroativamente.
          </div>

          <!-- Error -->
          <div
            v-if="error"
            class="font-mono"
            :style="{
              marginTop: '10px', padding: '8px 12px',
              background: 'var(--coral)', color: 'var(--ink)',
              border: '1.5px solid var(--ink)',
              fontSize: '10px', letterSpacing: '0.1em',
            }"
          >{{ error }}</div>

          <PerfDivider />

          <!-- Actions -->
          <div :style="{ display: 'flex', gap: '8px', marginTop: '14px' }">
            <button class="font-display press modal-cancel" @click="$emit('close')">Cancelar</button>

            <PrintButton
              v-if="pool.isMember"
              :tone="pool.accent"
              full
              @click="viewPool"
            >Ver bolão →</PrintButton>

            <PrintButton
              v-else
              tone="cobalt"
              full
              :disabled="loading"
              @click="join"
            >{{ loading ? 'Entrando…' : 'Entrar no bolão →' }}</PrintButton>
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
  max-width: 380px;
  padding: 18px;
  box-shadow: 5px 5px 0 var(--cobalt), 5px 5px 0 1px var(--ink);
}
.modal-card.desktop {
  max-width: 500px;
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

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.18s ease;
}
.modal-enter-from, .modal-leave-to {
  opacity: 0;
}
</style>

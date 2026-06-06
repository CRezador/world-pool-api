<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePools } from '@/composables/usePools';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';

const route  = useRoute();
const router = useRouter();
const { pools } = usePools();
const join   = useJoinModal();
const create = useCreatePoolModal();

const showSheet = ref(false);

const tabs = [
  { id: 'home',      icon: '◧', label: 'Início',  path: '/'          },
  { id: 'matches',   icon: '⌖', label: 'Jogos',   path: '/matches'   },
  { id: 'pool',      icon: '◉', label: 'Bolão',   path: '/pools'     },
  { id: 'standings', icon: '⊞', label: 'Tabela',  path: '/standings' },
  { id: 'me',        icon: '◐', label: 'Eu',      path: '/eu'        },
];

const active = computed(() => {
  const m = route.matched[0]?.name;
  if (m === 'home')                                       return 'home';
  if (m === 'matches' || m === 'guess' || m === 'match-detail') return 'matches';
  if (m === 'my-pools' || m === 'pool' || m === 'explore') return 'pool';
  if (m === 'standings')                                  return 'standings';
  if (m === 'me')                                         return 'me';
  return '';
});

function handleTab(t: typeof tabs[0]) {
  if (t.id === 'pool') {
    showSheet.value = !showSheet.value;
  } else {
    showSheet.value = false;
    router.push(t.path);
  }
}

function goMyPools() {
  showSheet.value = false;
  router.push('/pools');
}
function goExplore() {
  showSheet.value = false;
  router.push('/explore');
}
async function openCreate() {
  showSheet.value = false;
  await nextTick();
  create.show();
}
async function openJoin() {
  showSheet.value = false;
  await nextTick();
  join.show();
}
</script>

<template>
  <!-- Bottom sheet overlay -->
  <Teleport to="body">
    <Transition name="sheet-overlay">
      <div
        v-if="showSheet"
        :style="{
          position: 'fixed', inset: 0, zIndex: 150,
          background: 'rgba(22,17,15,0.55)',
          display: 'flex', alignItems: 'flex-end',
        }"
        @click.self="showSheet = false"
      >
        <Transition name="sheet-slide" appear>
          <div
            v-if="showSheet"
            :style="{
              width: '100%', maxWidth: '480px', margin: '0 auto',
              background: 'var(--paper)',
              borderRadius: '18px 18px 0 0',
              borderTop: '2px solid var(--ink)',
              overflow: 'hidden',
            }"
          >
            <!-- drag handle -->
            <div :style="{ display: 'flex', justifyContent: 'center', padding: '10px 0 4px' }">
              <div :style="{
                width: '40px', height: '4px', borderRadius: '2px',
                background: 'var(--ink)', opacity: 0.2,
              }" />
            </div>

            <!-- header -->
            <div :style="{
              padding: '8px 18px 14px',
              borderBottom: '1.5px dashed var(--ink)',
              display: 'flex', alignItems: 'flex-end', justifyContent: 'space-between',
            }">
              <div>
                <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.55 }">
                  BOLÕES · ATALHOS
                </div>
                <div class="font-display" :style="{ fontSize: '28px', lineHeight: 0.95, marginTop: '4px', textTransform: 'uppercase' }">
                  ONDE VOCÊ JOGA
                </div>
              </div>
              <button
                class="font-display press"
                :style="{
                  width: '34px', height: '34px',
                  border: '1.5px solid var(--ink)', borderRadius: '50%',
                  background: 'var(--paper-2)', cursor: 'pointer',
                  fontSize: '16px', display: 'flex', alignItems: 'center', justifyContent: 'center',
                }"
                @click="showSheet = false"
              >✕</button>
            </div>

            <!-- pool options -->
            <div :style="{ padding: '14px 18px', display: 'flex', flexDirection: 'column', gap: '10px' }">

              <!-- Meus Bolões -->
              <div
                class="press"
                :style="{
                  display: 'flex', alignItems: 'center', gap: '14px',
                  padding: '14px 16px', cursor: 'pointer',
                  background: 'var(--paper-2)',
                  border: '1.5px solid var(--ink)',
                  boxShadow: '4px 4px 0 var(--magenta), 4px 4px 0 1px var(--ink)',
                  borderRadius: '5px',
                }"
                @click="goMyPools"
              >
                <div :style="{
                  width: '44px', height: '44px', borderRadius: '8px', flexShrink: 0,
                  background: 'var(--magenta)', border: '1.5px solid var(--ink)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                }">
                  <span class="font-display" :style="{ fontSize: '22px', color: 'var(--paper)' }">◉</span>
                </div>
                <div :style="{ flex: 1 }">
                  <div class="font-display" :style="{ fontSize: '20px', lineHeight: 1, textTransform: 'uppercase' }">MEUS BOLÕES</div>
                  <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.65, marginTop: '3px' }">
                    {{ pools.length }} QUE VOCÊ PARTICIPA
                  </div>
                </div>
                <span class="font-display" :style="{ fontSize: '18px', opacity: 0.4 }">→</span>
              </div>

              <!-- Bolões Públicos -->
              <div
                class="press"
                :style="{
                  display: 'flex', alignItems: 'center', gap: '14px',
                  padding: '14px 16px', cursor: 'pointer',
                  background: 'var(--paper-2)',
                  border: '1.5px solid var(--ink)',
                  boxShadow: '4px 4px 0 var(--cobalt), 4px 4px 0 1px var(--ink)',
                  borderRadius: '5px',
                }"
                @click="goExplore"
              >
                <div :style="{
                  width: '44px', height: '44px', borderRadius: '8px', flexShrink: 0,
                  background: 'var(--cobalt)', border: '1.5px solid var(--ink)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                }">
                  <span class="font-display" :style="{ fontSize: '22px', color: 'var(--paper)' }">◐</span>
                </div>
                <div :style="{ flex: 1 }">
                  <div class="font-display" :style="{ fontSize: '20px', lineHeight: 1, textTransform: 'uppercase' }">BOLÕES PÚBLICOS</div>
                  <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.65, marginTop: '3px' }">
                    EXPLORAR ABERTOS
                  </div>
                </div>
                <span class="font-display" :style="{ fontSize: '18px', opacity: 0.4 }">→</span>
              </div>
            </div>

            <!-- action buttons -->
            <div :style="{
              display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px',
              padding: '0 18px 32px',
            }">
              <button
                class="press"
                :style="{
                  display: 'flex', alignItems: 'center', gap: '10px',
                  padding: '12px 14px', cursor: 'pointer',
                  background: 'var(--lime)', color: 'var(--ink)',
                  border: '1.5px solid var(--ink)', boxShadow: '3px 3px 0 var(--ink)',
                  borderRadius: '4px', textAlign: 'left',
                }"
                @click="openCreate"
              >
                <span class="font-display" :style="{
                  width: '30px', height: '30px', flexShrink: 0,
                  background: 'rgba(0,0,0,0.15)', borderRadius: '5px',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: '18px',
                }">+</span>
                <div>
                  <div class="font-display" :style="{ fontSize: '14px', lineHeight: 1 }">+ Criar bolão</div>
                  <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.7, marginTop: '3px' }">Do zero, em 30s</div>
                </div>
              </button>

              <button
                class="press"
                :style="{
                  display: 'flex', alignItems: 'center', gap: '10px',
                  padding: '12px 14px', cursor: 'pointer',
                  background: 'var(--coral)', color: 'var(--paper)',
                  border: '1.5px solid var(--ink)', boxShadow: '3px 3px 0 var(--ink)',
                  borderRadius: '4px', textAlign: 'left',
                }"
                @click="openJoin"
              >
                <span class="font-display" :style="{
                  width: '30px', height: '30px', flexShrink: 0,
                  background: 'rgba(255,255,255,0.2)', borderRadius: '5px',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: '18px',
                }">⎘</span>
                <div>
                  <div class="font-display" :style="{ fontSize: '14px', lineHeight: 1 }">+ Entrar c/ código</div>
                  <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.85, marginTop: '3px' }">Tenho um convite</div>
                </div>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>

  <!-- Tab bar -->
  <div :style="{
    display: 'flex', justifyContent: 'space-around',
    padding: '8px 4px 22px',
    background: 'var(--paper)',
    borderTop: '1.5px solid var(--ink)',
    position: 'relative',
  }">
    <button
      v-for="t in tabs"
      :key="t.id"
      :style="{
        border: 'none', background: 'transparent', cursor: 'pointer',
        display: 'flex', flexDirection: 'column', alignItems: 'center',
        gap: '2px', padding: '6px 8px', minWidth: '56px',
        color: (active === t.id || (t.id === 'pool' && showSheet)) ? 'var(--magenta)' : 'var(--ink)',
        position: 'relative',
      }"
      @click="handleTab(t)"
    >
      <div class="font-display" :style="{ fontSize: '22px', lineHeight: 1 }">{{ t.icon }}</div>
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.1em', fontWeight: 700, textTransform: 'uppercase' }"
      >{{ t.label }}</div>
      <div
        v-if="active === t.id || (t.id === 'pool' && showSheet)"
        :style="{
          position: 'absolute', bottom: '2px', height: '3px', width: '22px',
          background: 'var(--magenta)',
        }"
      />
    </button>
  </div>
</template>

<style scoped>
/* backdrop fade */
.sheet-overlay-enter-active { transition: opacity 0.22s ease; }
.sheet-overlay-leave-active { transition: opacity 0.2s ease; }
.sheet-overlay-enter-from,
.sheet-overlay-leave-to    { opacity: 0; }

/* sheet slide up */
.sheet-slide-enter-active { transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1); }
.sheet-slide-leave-active { transition: transform 0.22s ease-in; }
.sheet-slide-enter-from,
.sheet-slide-leave-to     { transform: translateY(100%); }
.sheet-slide-enter-to,
.sheet-slide-leave-from   { transform: translateY(0); }
</style>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Avatar from '@/components/Avatar.vue';
import TodayMatchesCarousel from '@/components/match/TodayMatchesCarousel.vue';
import { MEMBERS } from '@/data/mock';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';
import { useAuth } from '@/composables/useAuth';
import { logout } from '@/services/auth.services';

const route = useRoute();
const router = useRouter();
const join = useJoinModal();
const create = useCreatePoolModal();

type MenuItem = { divider: true } | {
  divider?: false;
  label: string;
  sub: string;
  icon: string;
  tone: 'magenta' | 'cobalt' | 'lime' | 'coral';
  action?: 'create' | 'join';
  path?: string;
};

const nav = [
  { label: 'Bolões', path: '/pools', menu: [
    { label: 'Meus bolões',        sub: '3 que você participa', icon: '◉', tone: 'magenta', path: '/pools'   } as MenuItem,
    { label: 'Bolões públicos',    sub: '8.241 abertos',        icon: '◐', tone: 'cobalt',  path: '/explore' } as MenuItem,
    { divider: true } as MenuItem,
    { label: '+ Criar bolão',      sub: 'Do zero, em 30s',      icon: '+', tone: 'lime',    action: 'create' } as MenuItem,
    { label: '+ Entrar com código',sub: 'Tenho um convite',     icon: '⎘', tone: 'coral',   action: 'join'   } as MenuItem,
  ]},
  { label: 'Jogos',   path: '/matches'   },
  { label: 'Tabela',  path: '/standings' },
];

const toneVar: Record<string, string> = {
  magenta: 'var(--magenta)',
  cobalt:  'var(--cobalt)',
  lime:    'var(--lime)',
  coral:   'var(--coral)',
};

const openId = ref<string | null>(null);
const avatarMenuOpen = ref(false);
const { clearUser } = useAuth();

function toggleMenu(label: string) {
  openId.value = openId.value === label ? null : label;
  avatarMenuOpen.value = false;
}

function handleMenuAction(item: MenuItem) {
  if ('divider' in item) return;
  openId.value = null;
  if (item.action === 'create') create.show();
  else if (item.action === 'join') join.show();
  else if (item.path) router.push(item.path);
}

async function handleLogout() {
  avatarMenuOpen.value = false;
  try { await logout(); } catch {}
  clearUser();
  router.push('/login');
}

function handleOutsideClick(e: MouseEvent) {
  const nav = (e.target as HTMLElement).closest('.nav-item-wrapper');
  const avatar = (e.target as HTMLElement).closest('.avatar-wrapper');
  if (!nav) openId.value = null;
  if (!avatar) avatarMenuOpen.value = false;
}

onMounted(() => document.addEventListener('click', handleOutsideClick));
onUnmounted(() => document.removeEventListener('click', handleOutsideClick));

const activeIndex = computed(() => {
  if (route.path.startsWith('/pool') || route.path.startsWith('/explore')) return 0;
  if (route.path.startsWith('/matches')) return 1;
  if (route.path.startsWith('/standings')) return 2;
  return -1;
});

const isMeActive = computed(() => route.name === 'me');
</script>

<template>
  <div class="grain desktop-shell">
    <header class="top-bar">
      <div
        class="font-display"
        :style="{ fontSize: '26px', letterSpacing: '0.02em', cursor: 'pointer' }"
        @click="router.push('/pools')"
      >
        BOLÃO<span :style="{ color: 'var(--magenta)' }">·</span>COPA <span :style="{ color: 'var(--cobalt)' }">26</span>
      </div>
      <nav class="font-mono" :style="{ display: 'flex', gap: '18px', marginLeft: '24px' }">
        <div
          v-for="(n, i) in nav"
          :key="n.label"
          class="nav-item-wrapper"
        >
          <span
            :style="{
              fontSize: '12px', letterSpacing: '0.14em', fontWeight: 700,
              padding: '6px 4px',
              borderBottom: i === activeIndex ? '2px solid var(--magenta)' : '2px solid transparent',
              cursor: 'pointer',
              display: 'inline-flex', alignItems: 'center', gap: '4px',
              color: openId === n.label ? 'var(--magenta)' : 'var(--ink)',
              transition: 'color 0.14s ease',
            }"
            @click="n.menu ? toggleMenu(n.label) : router.push(n.path)"
          >
            {{ n.label.toUpperCase() }}
            <span
              v-if="n.menu"
              :style="{
                fontSize: '9px', opacity: 0.6,
                transform: openId === n.label ? 'rotate(180deg)' : 'rotate(0deg)',
                transition: 'transform 0.16s ease',
              }"
            >▾</span>
          </span>

          <!-- Dropdown Bolões -->
          <div
            v-if="n.menu && openId === n.label"
            class="nav-dropdown"
          >
            <div class="nav-dropdown-notch" />
            <div class="font-mono nav-dropdown-header">BOLÕES · ATALHOS</div>
            <template v-for="(item, idx) in n.menu" :key="idx">
              <div v-if="'divider' in item" class="nav-dropdown-divider" />
              <div
                v-else
                class="nav-dropdown-item"
                @click="handleMenuAction(item)"
              >
                <div
                  class="font-display nav-dropdown-icon"
                  :style="{
                    background: toneVar[item.tone],
                    color: item.tone === 'lime' ? 'var(--ink)' : 'var(--paper)',
                  }"
                >{{ item.icon }}</div>
                <div :style="{ flex: 1 }">
                  <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">{{ item.label }}</div>
                  <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.08em', opacity: 0.65, marginTop: '3px' }">{{ item.sub }}</div>
                </div>
                <span :style="{ opacity: 0.4, fontSize: '14px' }">→</span>
              </div>
            </template>
          </div>
        </div>
      </nav>
      <div :style="{ marginLeft: 'auto', display: 'flex', alignItems: 'center', gap: '12px' }">
        <button
          class="font-display press"
          :style="{
            background: 'transparent', color: 'var(--ink)',
            border: '1.5px solid var(--ink)',
            padding: '8px 14px', fontSize: '12px', letterSpacing: '0.06em',
            textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          }"
          @click="join.show()"
        >+ Entrar c/ código</button>
        <button
          class="font-display press"
          :style="{
            background: 'var(--magenta)', color: 'var(--paper)',
            border: '1.5px solid var(--ink)',
            boxShadow: '3px 3px 0 var(--ink)',
            padding: '9px 16px', fontSize: '13px', letterSpacing: '0.06em',
            textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
          }"
          @click="create.show()"
        >+ Novo bolão</button>
        <div class="avatar-wrapper" :style="{ position: 'relative' }">
          <div
            :style="{
              display: 'flex', alignItems: 'center', gap: '7px', cursor: 'pointer',
              paddingBottom: '2px',
              borderBottom: isMeActive ? '2px solid var(--magenta)' : '2px solid transparent',
            }"
            @click="avatarMenuOpen = !avatarMenuOpen; openId = null"
          >
            <Avatar :member="MEMBERS[2]" :size="38" />
            <span
              class="font-mono"
              :style="{
                fontSize: '12px', letterSpacing: '0.14em', fontWeight: 700,
                color: isMeActive ? 'var(--magenta)' : 'var(--ink)',
              }"
            >EU <span :style="{ fontSize: '9px', opacity: 0.5 }">▾</span></span>
          </div>

          <div v-if="avatarMenuOpen" class="nav-dropdown" :style="{ right: 0, left: 'auto' }">
            <div class="nav-dropdown-notch" :style="{ left: 'auto', right: '22px' }" />
            <div class="font-mono nav-dropdown-header">MINHA CONTA</div>
            <div class="nav-dropdown-item" @click="avatarMenuOpen = false; router.push('/eu')">
              <div class="font-display nav-dropdown-icon" :style="{ background: 'var(--cobalt)', color: 'var(--paper)' }">◐</div>
              <div :style="{ flex: 1 }">
                <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">Meu perfil</div>
                <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.08em', opacity: 0.65, marginTop: '3px' }">Estatísticas e histórico</div>
              </div>
              <span :style="{ opacity: 0.4, fontSize: '14px' }">→</span>
            </div>
            <div class="nav-dropdown-divider" />
            <div class="nav-dropdown-item" @click="handleLogout">
              <div class="font-display nav-dropdown-icon" :style="{ background: 'var(--coral)', color: 'var(--paper)' }">✕</div>
              <div :style="{ flex: 1 }">
                <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">Sair</div>
                <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.08em', opacity: 0.65, marginTop: '3px' }">Encerrar sessão</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <TodayMatchesCarousel />

    <main class="desktop-main">
      <RouterView v-slot="{ Component }">
        <Transition name="route-fade" mode="out-in">
          <component :is="Component" :key="$route.path" />
        </Transition>
      </RouterView>
    </main>
  </div>
</template>

<style scoped>
.desktop-shell {
  background: var(--paper);
  color: var(--ink);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
}

.top-bar {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 14px 28px;
  border-bottom: 2px solid var(--ink);
  background: var(--paper);
}

.desktop-main {
  flex: 1;
}

.nav-item-wrapper {
  position: relative;
}

/* ── Nav dropdown ── */
.nav-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: -12px;
  min-width: 280px;
  background: var(--paper);
  border: 1.5px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
  z-index: 100;
  animation: fadeUp 0.18s ease both;
}

.nav-dropdown-notch {
  position: absolute;
  top: -8px;
  left: 22px;
  width: 14px;
  height: 14px;
  background: var(--paper);
  border-top: 1.5px solid var(--ink);
  border-left: 1.5px solid var(--ink);
  transform: rotate(45deg);
}

.nav-dropdown-header {
  padding: 10px 16px 8px;
  font-size: 9px;
  letter-spacing: 0.2em;
  font-weight: 700;
  opacity: 0.55;
  border-bottom: 1.5px dashed var(--ink);
}

.nav-dropdown-divider {
  border-top: 1.5px dashed var(--ink);
  margin: 4px 0;
}

.nav-dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 16px;
  cursor: pointer;
  transition: background 0.14s ease;
}

.nav-dropdown-item:hover {
  background: var(--paper-2);
}

.nav-dropdown-icon {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  border: 1.5px solid var(--ink);
  flex-shrink: 0;
}
</style>

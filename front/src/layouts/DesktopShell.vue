<script setup lang="ts">
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import FlagImg from '@/components/FlagImg.vue';
import Avatar from '@/components/Avatar.vue';
import { MATCHES, MEMBERS } from '@/data/mock';
import { useJoinModal } from '@/composables/useJoinModal';
import { useCreatePoolModal } from '@/composables/useCreatePoolModal';

const route = useRoute();
const router = useRouter();
const join = useJoinModal();
const create = useCreatePoolModal();

const liveMatch = MATCHES.find(m => m.status === 'IN_PROGRESS');
const nextMatch = MATCHES.find(m => m.status === 'SCHEDULED');

const nav = [
  { label: 'Bolões', path: '/pools' },
  { label: 'Jogos', path: '/matches' },
  { label: 'Tabela', path: '/standings' },
  { label: 'Admin', path: '/' },
];

const activeIndex = computed(() => {
  if (route.path.startsWith('/pool') || route.path.startsWith('/explore')) return 0;
  if (route.path.startsWith('/matches')) return 1;
  if (route.path.startsWith('/standings')) return 2;
  return -1;
});
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
        <span
          v-for="(n, i) in nav"
          :key="n.label"
          :style="{
            fontSize: '12px', letterSpacing: '0.14em', fontWeight: 700,
            padding: '6px 4px',
            borderBottom: i === activeIndex ? '2px solid var(--magenta)' : '2px solid transparent',
            cursor: 'pointer',
          }"
          @click="router.push(n.path)"
        >{{ n.label.toUpperCase() }}</span>
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
        <Avatar :member="MEMBERS[2]" :size="38" />
      </div>
    </header>

    <div v-if="liveMatch" class="live-ticker">
      <span
        class="font-mono"
        :style="{ fontSize: '10px', letterSpacing: '0.18em', color: 'var(--coral)' }"
      >
        <span
          class="live-dot"
          :style="{
            display: 'inline-block', width: '6px', height: '6px', borderRadius: '50%',
            background: 'var(--coral)', marginRight: '6px',
          }"
        />AO VIVO
      </span>
      <span
        class="font-display"
        :style="{ fontSize: '16px', display: 'inline-flex', alignItems: 'center', gap: '8px' }"
      >
        <FlagImg :team="liveMatch.home" :size="16" :radius="2" />
        {{ liveMatch.home }} {{ liveMatch.homeScore }} × {{ liveMatch.awayScore }} {{ liveMatch.away }}
        <FlagImg :team="liveMatch.away" :size="16" :radius="2" />
      </span>
      <span class="font-mono" :style="{ fontSize: '11px', opacity: 0.7 }">
        {{ liveMatch.kickoff }} · MetLife
      </span>
      <span :style="{ marginLeft: '18px', opacity: 0.6 }">·</span>
      <span v-if="nextMatch" class="font-mono" :style="{ fontSize: '11px', opacity: 0.7 }">
        PRÓX · {{ nextMatch.home }} × {{ nextMatch.away }} {{ nextMatch.day.toLowerCase() }} {{ nextMatch.kickoff }}
      </span>
      <span
        class="font-mono"
        :style="{ marginLeft: 'auto', fontSize: '10px', letterSpacing: '0.16em' }"
      >MEUS BOLÕES ATIVOS · 3</span>
    </div>

    <main class="desktop-main">
      <RouterView v-slot="{ Component }">
        <Transition name="route-fade" mode="out-in">
          <component :is="Component" />
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

.live-ticker {
  background: var(--ink);
  color: var(--paper);
  padding: 8px 28px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.desktop-main {
  flex: 1;
}
</style>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import BackBar from '@/components/BackBar.vue';
import DesktopExplore from '@/components/pool/DesktopExplore.vue';
import JoinPublicPoolModal from '@/components/modals/JoinPublicPoolModal.vue';
import { toneVar, toneFg } from '@/data/mock';
import { usePools } from '@/composables/usePools';
import { useBreakpoint } from '@/composables/useBreakpoint';
import type { Pool } from '@/types';

const { isDesktop } = useBreakpoint();
const { publicPools, fetchPublicPools } = usePools();
const selectedPool = ref<Pool | null>(null);

const PER_PAGE = 10;
const page = ref(1);
const lastPage = ref(1);
const total = ref(0);
const loading = ref(false);

async function goPage(p: number) {
  loading.value = true;
  const meta = await fetchPublicPools(p, PER_PAGE);
  page.value = meta.current_page;
  lastPage.value = meta.last_page;
  total.value = meta.total;
  loading.value = false;
}

onMounted(() => goPage(1));

const fmt = new Intl.NumberFormat('pt-BR');

function tagBg(tag: string) {
  if (tag === 'NOVO') return 'var(--magenta)';
  if (tag === 'POPULAR') return 'var(--ink)';
  return 'var(--cobalt)';
}
</script>

<template>
  <DesktopExplore v-if="isDesktop" />
  <div v-else :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '18px', paddingBottom: '8px' }">
      <BackBar title="Bolões públicos" :kicker="`EXPLORAR · ${total} ABERTOS`" fallback="/" />

    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      :style="{ padding: '40px 18px', textAlign: 'center' }"
    >
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', opacity: 0.5 }">CARREGANDO…</span>
    </div>

    <!-- Simple list rows -->
    <div v-else :style="{ padding: '4px 18px 0', borderTop: '1.5px solid var(--ink)', margin: '4px 0 0' }">
      <div
        v-for="(p, i) in publicPools"
        :key="p.id"
        class="press"
        :style="{
          display: 'flex', alignItems: 'center', gap: '12px',
          padding: '13px 2px', cursor: 'pointer',
          borderBottom: '1px dashed var(--ink)',
        }"
        @click="selectedPool = p"
      >
        <!-- row number -->
        <div
          class="font-mono"
          :style="{
            width: '26px', flexShrink: 0, textAlign: 'right',
            fontSize: '12px', fontWeight: 700, opacity: 0.45,
          }"
        >{{ (page - 1) * PER_PAGE + i + 1 }}</div>

        <!-- accent tile -->
        <div
          class="font-display"
          :style="{
            width: '38px', height: '38px', flexShrink: 0,
            background: toneVar(p.accent),
            border: '1.5px solid var(--ink)',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
          }"
        >
          <span :style="{
            color: toneFg(p.accent),
            fontSize: '16px', transform: 'rotate(-6deg)',
          }">{{ p.code.slice(0, 2) }}</span>
        </div>

        <!-- name + meta -->
        <div :style="{ flex: 1, minWidth: 0 }">
          <div :style="{ display: 'flex', alignItems: 'center', gap: '6px' }">
            <div
              class="font-display"
              :style="{
                fontSize: '18px', lineHeight: 1,
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
              }"
            >{{ p.name }}</div>
          </div>
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.7, marginTop: '3px' }"
          >{{ p.code }} · {{ fmt.format(p.members) }} sócios</div>
        </div>

        <span
          v-if="p.isMember"
          class="font-mono"
          :style="{
            flexShrink: 0, fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700,
            padding: '3px 7px', background: 'var(--lime)', color: 'var(--ink)',
            border: '1.5px solid var(--ink)', borderRadius: '2px',
          }"
        >✓ SÓCIO</span>
        <span v-else class="font-display" :style="{ fontSize: '14px', opacity: 0.5, flexShrink: 0 }">→</span>
      </div>
    </div>

    <!-- Pagination -->
    <div :style="{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '10px',
      padding: '14px 18px 24px',
    }">
      <button
        class="font-display press"
        :disabled="page === 1"
        :style="{
          padding: '8px 14px', fontSize: '13px', letterSpacing: '0.04em', cursor: page === 1 ? 'default' : 'pointer',
          background: 'var(--paper-2)', border: '1.5px solid var(--ink)', borderRadius: '3px',
          boxShadow: page === 1 ? 'none' : '2px 2px 0 var(--ink)',
          opacity: page === 1 ? 0.4 : 1, textTransform: 'uppercase',
        }"
        @click="goPage(page - 1)"
      >← Ant.</button>

      <div :style="{ display: 'flex', alignItems: 'center', gap: '6px' }">
        <button
          v-for="p in lastPage"
          :key="p"
          class="font-display press"
          :style="{
            width: '30px', height: '30px', fontSize: '13px', cursor: 'pointer', borderRadius: '3px',
            border: '1.5px solid var(--ink)',
            background: p === page ? 'var(--ink)' : 'var(--paper)',
            color: p === page ? 'var(--paper)' : 'var(--ink)',
          }"
          @click="goPage(p)"
        >{{ p }}</button>
      </div>

      <button
        class="font-display press"
        :disabled="page >= lastPage"
        :style="{
          padding: '8px 14px', fontSize: '13px', letterSpacing: '0.04em',
          cursor: page >= lastPage ? 'default' : 'pointer',
          background: 'var(--paper-2)', border: '1.5px solid var(--ink)', borderRadius: '3px',
          boxShadow: page >= lastPage ? 'none' : '2px 2px 0 var(--ink)',
          opacity: page >= lastPage ? 0.4 : 1, textTransform: 'uppercase',
        }"
        @click="goPage(page + 1)"
      >Próx. →</button>
    </div>
    <JoinPublicPoolModal
      :open="selectedPool !== null"
      :pool="selectedPool"
      variant="compact"
      @close="selectedPool = null"
      @joined="goPage(page)"
    />
  </div>
</template>

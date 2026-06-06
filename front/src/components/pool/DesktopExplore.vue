<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { toneVar, toneFg } from '@/data/mock';
import { usePools } from '@/composables/usePools';
import DesktopHero from '@/components/DesktopHero.vue';
import JoinPublicPoolModal from '@/components/modals/JoinPublicPoolModal.vue';
import type { Pool } from '@/types';

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

const start = () => (page.value - 1) * PER_PAGE;
</script>

<template>
  <div :style="{ minHeight: '100%', display: 'flex', flexDirection: 'column' }">
    <DesktopHero
      :kicker="`BOLÕES PÚBLICOS · ${total} ABERTOS`"
      title="Onde a galera está"
      tone="cobalt"
      title-size="76px"
    />

    <!-- List area -->
    <div :style="{ padding: '20px 32px 8px', flex: 1 }">
      <div :style="{
        display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: '12px',
      }">
        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.2em', fontWeight: 700 }"
        >EXPLORAR · {{ total }} BOLÕES ABERTOS</div>
        <span
          v-if="!loading && total > 0"
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.14em', opacity: 0.6 }"
        >{{ start() + 1 }}–{{ Math.min(start() + PER_PAGE, total) }} DE {{ total }}</span>
      </div>

      <!-- Loading -->
      <div
        v-if="loading"
        :style="{ padding: '60px', textAlign: 'center' }"
      >
        <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.2em', opacity: 0.5 }">CARREGANDO…</span>
      </div>

      <template v-else>
        <!-- Table header -->
        <div
          class="font-mono"
          :style="{
            display: 'grid', gridTemplateColumns: '40px 1fr 130px 130px 120px',
            gap: '16px', padding: '0 16px 8px', alignItems: 'center',
            fontSize: '9px', letterSpacing: '0.14em', fontWeight: 700, opacity: 0.6,
            borderBottom: '1.5px solid var(--ink)',
          }"
        >
          <span>#</span>
          <span>BOLÃO</span>
          <span>SÓCIOS</span>
          <span>LÍDER</span>
          <span :style="{ textAlign: 'right' }">AÇÃO</span>
        </div>

        <!-- Rows -->
        <div>
          <div
            v-for="(p, i) in publicPools"
            :key="p.id"
            class="font-mono"
            :style="{
              display: 'grid', gridTemplateColumns: '40px 1fr 130px 130px 120px',
              gap: '16px', padding: '12px 16px', alignItems: 'center',
              borderBottom: '1px dashed var(--ink)',
            }"
          >
            <span class="font-display" :style="{ fontSize: '16px', opacity: 0.45 }">{{ start() + i + 1 }}</span>

            <!-- tile + name -->
            <div :style="{ display: 'flex', alignItems: 'center', gap: '12px', minWidth: 0 }">
              <div
                class="font-display"
                :style="{
                  width: '40px', height: '40px', flexShrink: 0,
                  background: toneVar(p.accent),
                  color: toneFg(p.accent),
                  border: '1.5px solid var(--ink)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: '17px', transform: 'rotate(-6deg)',
                }"
              >{{ p.code.slice(0, 2) }}</div>
              <div :style="{ minWidth: 0 }">
                <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
                  <span
                    class="font-display"
                    :style="{
                      fontSize: '20px', lineHeight: 1,
                      whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
                    }"
                  >{{ p.name }}</span>
                </div>
                <div :style="{ fontSize: '10px', letterSpacing: '0.1em', opacity: 0.6, marginTop: '3px' }">
                  CÓD {{ p.code }}
                </div>
              </div>
            </div>

            <!-- members -->
            <span class="font-display" :style="{ fontSize: '18px' }">{{ fmt.format(p.members) }}</span>

            <!-- leader -->
            <span :style="{
              fontSize: '12px', letterSpacing: '0.04em', opacity: 0.8,
              whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
            }">{{ p.leader }}</span>

            <!-- action -->
            <div :style="{ display: 'flex', justifyContent: 'flex-end' }">
              <button
                v-if="p.isMember"
                class="font-display press"
                :style="{
                  background: 'var(--lime)', color: 'var(--ink)',
                  border: '1.5px solid var(--ink)',
                  padding: '6px 14px', fontSize: '12px', letterSpacing: '0.06em',
                  textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
                }"
                @click="selectedPool = p"
              >✓ Sócio</button>
              <button
                v-else
                class="font-display press"
                :style="{
                  background: 'var(--ink)', color: 'var(--paper)',
                  border: '1.5px solid var(--ink)',
                  padding: '6px 14px', fontSize: '12px', letterSpacing: '0.06em',
                  textTransform: 'uppercase', cursor: 'pointer', borderRadius: '3px',
                }"
                @click="selectedPool = p"
              >Entrar</button>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Pagination -->
    <div :style="{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '10px',
      padding: '14px 32px 28px',
    }">
      <button
        class="font-display press"
        :disabled="page === 1"
        :style="{
          padding: '8px 14px', fontSize: '13px', letterSpacing: '0.04em',
          background: 'var(--paper-2)', border: '1.5px solid var(--ink)', borderRadius: '3px',
          boxShadow: page === 1 ? 'none' : '2px 2px 0 var(--ink)',
          opacity: page === 1 ? 0.4 : 1, textTransform: 'uppercase', cursor: page === 1 ? 'default' : 'pointer',
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
          background: 'var(--paper-2)', border: '1.5px solid var(--ink)', borderRadius: '3px',
          boxShadow: page >= lastPage ? 'none' : '2px 2px 0 var(--ink)',
          opacity: page >= lastPage ? 0.4 : 1, textTransform: 'uppercase',
          cursor: page >= lastPage ? 'default' : 'pointer',
        }"
        @click="goPage(page + 1)"
      >Próx. →</button>
    </div>
  </div>

  <JoinPublicPoolModal
    :open="selectedPool !== null"
    :pool="selectedPool"
    variant="desktop"
    @close="selectedPool = null"
    @joined="goPage(page)"
  />
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import SectionHead from '@/components/SectionHead.vue';
import { useStandings } from '@/composables/useStandings';

const { standings, fetchStandings } = useStandings();

const idx = ref(0);
const paused = ref(false);
const tableKey = ref(0);

const total = computed(() => standings.value.length);
const group = computed(() => standings.value[idx.value] ?? null);

function go(delta: number) {
  idx.value = (idx.value + delta + total.value) % total.value;
  tableKey.value++;
}
function jumpTo(i: number) {
  idx.value = i;
  tableKey.value++;
}

let timer: ReturnType<typeof setInterval> | null = null;
function startTimer() {
  timer = setInterval(() => {
    if (!paused.value) {
      idx.value = (idx.value + 1) % total.value;
      tableKey.value++;
    }
  }, 5000);
}

const loading = ref(true);

onMounted(async () => {
  try {
    if (standings.value.length === 0) await fetchStandings();
    startTimer();
  } catch {
    // suppress — rejection must not leak to the next page after navigation
  } finally {
    loading.value = false;
  }
});
onUnmounted(() => { if (timer) clearInterval(timer); });
</script>

<template>
  <!-- Standings skeleton -->
  <div v-if="loading">
    <div :style="{ marginBottom: '14px' }">
      <div class="skeleton" :style="{ height: '10px', width: '150px', marginBottom: '8px' }" />
      <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }">
        <div class="skeleton" :style="{ height: '22px', width: '200px' }" />
        <div :style="{ display: 'flex', gap: '6px' }">
          <div class="skeleton" :style="{ width: '28px', height: '28px' }" />
          <div class="skeleton" :style="{ width: '28px', height: '28px' }" />
        </div>
      </div>
    </div>
    <div :style="{ border: '1.5px solid var(--ink)' }">
      <div class="skeleton" :style="{ height: '28px', borderRadius: 0 }" />
      <div v-for="n in 4" :key="n" :style="{ padding: '6px 8px', borderTop: '1px solid var(--ink)' }">
        <div class="skeleton" :style="{ height: '22px' }" />
      </div>
    </div>
  </div>

  <div
    v-else-if="group"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
  >
    <SectionHead
      :kicker="`GRUPO ${group.group} · TABELA REAL · ${idx + 1}/${total}`"
      :title="group.group === 'C' ? 'Como está o grupo' : `Tabela do Grupo ${group.group}`"
    >
      <template #action>
        <div :style="{ display: 'flex', alignItems: 'center', gap: '6px' }">
          <button
            class="font-display press"
            :style="{
              width: '28px', height: '28px', padding: 0,
              background: 'var(--paper-2)', color: 'var(--ink)',
              border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
              fontSize: '15px', lineHeight: '1', cursor: 'pointer', borderRadius: '3px',
            }"
            @click="go(-1)"
          >‹</button>
          <button
            class="font-display press"
            :style="{
              width: '28px', height: '28px', padding: 0,
              background: 'var(--ink)', color: 'var(--paper)',
              border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
              fontSize: '15px', lineHeight: '1', cursor: 'pointer', borderRadius: '3px',
            }"
            @click="go(1)"
          >›</button>
        </div>
      </template>
    </SectionHead>

    <table
      :key="tableKey"
      class="fade-up"
      :style="{ width: '100%', borderCollapse: 'collapse', marginTop: '14px', border: '1.5px solid var(--ink)' }"
    >
      <thead>
        <tr :style="{ background: 'var(--ink)', color: 'var(--paper)' }">
          <th
            v-for="(h, i) in ['#', 'TIME', 'P', 'V', 'E', 'D', 'PTS']"
            :key="h"
            class="font-mono"
            :style="{
              padding: '6px 8px', fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700,
              textAlign: i < 2 ? 'left' : 'center',
            }"
          >{{ h }}</th>
        </tr>
      </thead>
      <tbody :style="{ background: 'var(--paper-2)' }">
        <tr
          v-for="(row, i) in group.table"
          :key="row.team"
          :style="{ borderBottom: i < group.table.length - 1 ? '1px solid var(--ink)' : 'none' }"
        >
          <td class="font-display" :style="{ padding: '8px 8px', fontSize: '16px' }">
            {{ row.position }}<span v-if="i < 2" :style="{ color: 'var(--lime)', marginLeft: '4px' }">●</span>
          </td>
          <td :style="{ padding: '8px 4px' }">
            <div :style="{ display: 'flex', alignItems: 'center', gap: '8px' }">
              <img
                :src="row.crest"
                :alt="row.team"
                :style="{ width: '18px', height: '14px', objectFit: 'cover', borderRadius: '2px', border: '1px solid var(--ink)' }"
              />
              <span class="font-display" :style="{ fontSize: '14px', whiteSpace: 'nowrap' }">{{ row.code }}</span>
            </div>
          </td>
          <td class="font-mono" :style="{ textAlign: 'center', fontSize: '12px', padding: '8px 0' }">{{ row.played }}</td>
          <td class="font-mono" :style="{ textAlign: 'center', fontSize: '12px', padding: '8px 0' }">{{ row.won }}</td>
          <td class="font-mono" :style="{ textAlign: 'center', fontSize: '12px', padding: '8px 0' }">{{ row.draw }}</td>
          <td class="font-mono" :style="{ textAlign: 'center', fontSize: '12px', padding: '8px 0' }">{{ row.lost }}</td>
          <td class="font-display" :style="{ textAlign: 'center', fontSize: '18px' }">{{ row.points }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Dots + status -->
    <div :style="{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginTop: '10px' }">
      <div :style="{ display: 'flex', gap: '4px', flexWrap: 'wrap' }">
        <button
          v-for="(g, i) in standings"
          :key="g.group"
          class="font-mono"
          :style="{
            minWidth: '18px', height: '18px', padding: 0,
            border: '1.5px solid var(--ink)',
            background: i === idx ? 'var(--ink)' : 'var(--paper)',
            color: i === idx ? 'var(--lime)' : 'var(--ink)',
            cursor: 'pointer', borderRadius: '2px',
            fontSize: '9px', letterSpacing: '0.04em', fontWeight: 700,
          }"
          @click="jumpTo(i)"
        >{{ g.group }}</button>
      </div>
      <span
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.55, whiteSpace: 'nowrap' }"
      >{{ paused ? 'PAUSADO' : 'AUTO 5s' }}</span>
    </div>
  </div>
</template>

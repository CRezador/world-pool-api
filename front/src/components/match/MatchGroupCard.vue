<script setup lang="ts">
import type { GroupFull } from '@/types';

defineProps<{ group: GroupFull }>();
defineEmits<{ (e: 'click'): void }>();
</script>

<template>
  <div
    class="press group-card"
    :style="{
      cursor: 'pointer', border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
      boxShadow: '3px 3px 0 var(--ink)', borderRadius: '3px', overflow: 'hidden',
      transition: 'box-shadow 0.15s ease, transform 0.15s ease',
    }"
    @click="$emit('click')"
  >
    <div :style="{
      display: 'flex', justifyContent: 'space-between', alignItems: 'center',
      background: 'var(--ink)', color: 'var(--paper)', padding: '6px 9px',
    }">
      <span class="font-display" :style="{ fontSize: '17px', letterSpacing: '0.03em', whiteSpace: 'nowrap' }">
        GRUPO {{ group.g }}
      </span>
      <span class="font-display" :style="{ fontSize: '14px', color: 'var(--magenta)' }">→</span>
    </div>
    <div>
      <div
        v-for="(row, i) in group.rows"
        :key="row.code"
        :style="{
          display: 'flex', alignItems: 'center', gap: '7px', padding: '5px 9px',
          borderBottom: i < 3 ? '1px dashed var(--ink)' : 'none',
        }"
      >
        <img
          :src="`https://flagcdn.com/40x30/${row.iso}.png`"
          alt=""
          :style="{
            width: '18px', height: '12px', border: '1px solid var(--ink)',
            borderRadius: '2px', objectFit: 'cover', flexShrink: 0,
          }"
        />
        <span
          class="font-display"
          :style="{
            fontSize: '13px', flex: 1, minWidth: 0,
            whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
          }"
        >{{ row.name }}</span>
      </div>
    </div>
  </div>
</template>

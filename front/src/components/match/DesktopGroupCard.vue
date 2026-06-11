<script setup lang="ts">
import type { GroupFull } from '@/types';

defineProps<{ group: GroupFull }>();
defineEmits<{ (e: 'click'): void }>();
</script>

<template>
  <div
    class="press group-card"
    :style="{
      cursor: 'pointer',
      border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
      boxShadow: '3px 3px 0 var(--ink)',
      borderRadius: '3px', overflow: 'hidden',
      display: 'flex', flexDirection: 'column',
      transition: 'box-shadow 0.15s ease, transform 0.15s ease',
    }"
    @click="$emit('click')"
  >
    <div :style="{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      background: 'var(--ink)', color: 'var(--paper)', padding: '8px 12px',
    }">
      <span class="font-display" :style="{ fontSize: '22px', letterSpacing: '0.04em' }">
        GRUPO {{ group.g }}
      </span>
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6 }">
        4 SELEÇÕES
      </span>
    </div>
    <div :style="{ flex: 1 }">
      <div
        v-for="(row, i) in group.rows"
        :key="row.code"
        :style="{
          display: 'flex', alignItems: 'center', gap: '10px', padding: '8px 12px',
          borderBottom: i < 3 ? '1px dashed var(--ink)' : 'none',
        }"
      >
        <img
          :src="`https://flagcdn.com/${row.iso}.svg`"
          alt=""
          :style="{
            width: '22px', height: '15px', border: '1px solid var(--ink)',
            borderRadius: '2px', objectFit: 'cover', flexShrink: 0,
          }"
        />
        <span
          class="font-display"
          :style="{
            fontSize: '15px', letterSpacing: '0.01em', flex: 1, minWidth: 0,
            whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
          }"
        >{{ row.name }}</span>
        <span class="font-mono" :style="{ fontSize: '10px', opacity: 0.55, flexShrink: 0 }">{{ row.code }}</span>
      </div>
    </div>
    <div :style="{
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      padding: '8px 12px', background: 'var(--paper-3, rgba(0,0,0,0.04))',
      borderTop: '1.5px solid var(--ink)',
    }">
      <span class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.16em', fontWeight: 700, opacity: 0.7 }">
        VER JOGOS
      </span>
      <span class="font-display" :style="{ fontSize: '16px', color: 'var(--magenta)' }">→</span>
    </div>
  </div>
</template>

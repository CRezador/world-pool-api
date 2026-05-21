<script setup lang="ts">
import { computed } from 'vue';
import { findMember, toneVar } from '@/data/mock';
import type { Member } from '@/types';

const props = withDefaults(defineProps<{
  member: Member | number;
  size?: number;
}>(), {
  size: 36,
});

const m = computed<Member | undefined>(() =>
  typeof props.member === 'number' ? findMember(props.member) : props.member,
);
</script>

<template>
  <div
    v-if="m"
    class="font-display"
    :style="{
      width: `${size}px`, height: `${size}px`, borderRadius: '50%',
      background: toneVar(m.tone), color: 'var(--paper)',
      display: 'flex', alignItems: 'center', justifyContent: 'center',
      fontSize: `${size * 0.42}px`, letterSpacing: '0.04em',
      border: '1.5px solid var(--ink)',
      boxShadow: '1.5px 1.5px 0 var(--ink)',
      flexShrink: 0,
    }"
  >{{ m.avatar }}</div>
</template>

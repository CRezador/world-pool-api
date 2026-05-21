<script setup lang="ts">
import Avatar from '@/components/Avatar.vue';
import Stamp from '@/components/Stamp.vue';
import { MEMBERS } from '@/data/mock';
</script>

<template>
  <div class="fade-up" :style="{ padding: '14px 18px 18px' }">
    <span
      class="font-mono"
      :style="{
        fontSize: '10px', letterSpacing: '0.16em', fontWeight: 700,
        display: 'block', marginBottom: '10px',
      }"
    >SÓCIOS · 12 ATIVOS</span>

    <div :style="{ display: 'flex', flexDirection: 'column', gap: 0 }">
      <div
        v-for="m in MEMBERS"
        :key="m.id"
        :style="{
          display: 'flex', alignItems: 'center', gap: '12px',
          padding: '12px 4px',
          borderBottom: '1px dashed var(--ink)',
        }"
      >
        <Avatar :member="m" :size="38" />
        <div :style="{ flex: 1 }">
          <div class="font-display" :style="{ fontSize: '16px', lineHeight: 1 }">
            {{ m.name }}
            <span
              v-if="m.isMe"
              class="font-mono"
              :style="{ fontSize: '9px', padding: '1px 4px', background: 'var(--lime)', marginLeft: '4px' }"
            >VOCÊ</span>
          </div>
          <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7, marginTop: '2px' }">{{ m.handle }}</div>
        </div>
        <Stamp v-if="m.role === 'OWNER'" tone="magenta" :rotate="4">DONO</Stamp>
        <Stamp v-else-if="m.role === 'ADMIN'" tone="cobalt" :rotate="-4">ADMIN</Stamp>
        <span
          v-else
          class="font-mono"
          :style="{ fontSize: '10px', opacity: 0.5, letterSpacing: '0.1em' }"
        >SÓCIO</span>
      </div>
    </div>
  </div>
</template>

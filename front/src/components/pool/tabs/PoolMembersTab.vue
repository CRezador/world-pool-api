<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { fetchPoolMembers } from '@/composables/usePools';
import type { PoolMemberItem } from '@/composables/usePools';
import { useAuth } from '@/composables/useAuth';
import { toneVar } from '@/data/mock';
import type { Tone } from '@/types';

const props = defineProps<{ poolId: string }>();

const { user } = useAuth();
const loading = ref(true);
const members = ref<PoolMemberItem[]>([]);

onMounted(async () => {
  try {
    members.value = await fetchPoolMembers(props.poolId);
  } finally {
    loading.value = false;
  }
});

const TONES: Tone[] = ['magenta', 'cobalt', 'lime', 'coral'];
function avatarTone(userId: number): string {
  return toneVar(TONES[userId % TONES.length]);
}
function avatarFg(userId: number): string {
  return TONES[userId % TONES.length] === 'lime' ? 'var(--ink)' : 'var(--paper)';
}
function initials(name: string): string {
  return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}
function isMe(m: PoolMemberItem): boolean {
  return user.value?.id === m.userId;
}

const roleOrder: Record<string, number> = { OWNER: 0, ADMIN: 1, MEMBER: 2 };
const sorted = computed(() =>
  [...members.value].sort((a, b) => (roleOrder[a.role] ?? 2) - (roleOrder[b.role] ?? 2)),
);
</script>

<template>
  <div class="fade-up">
    <!-- header -->
    <div
      class="font-mono"
      :style="{
        padding: '12px 18px 8px',
        fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6,
      }"
    >{{ members.length }} SÓCIOS NO BOLÃO</div>

    <!-- table header -->
    <div
      class="font-mono"
      :style="{
        display: 'grid', gridTemplateColumns: '1fr 70px',
        padding: '7px 18px', gap: '8px',
        background: 'var(--ink)', color: 'var(--paper)',
        fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700,
      }"
    >
      <span>SÓCIO</span>
      <span :style="{ textAlign: 'right' }">PAPEL</span>
    </div>

    <!-- skeleton -->
    <template v-if="loading">
      <div
        v-for="n in 6"
        :key="n"
        :style="{ padding: '10px 18px', borderBottom: '1px dashed var(--ink)' }"
      >
        <div class="skeleton" :style="{ height: '36px' }" />
      </div>
    </template>

    <!-- rows -->
    <template v-else>
      <div
        v-for="(m, i) in sorted"
        :key="m.memberId"
        :style="{
          display: 'grid', gridTemplateColumns: '1fr 70px',
          padding: '11px 18px', gap: '8px', alignItems: 'center',
          borderBottom: i < sorted.length - 1 ? '1px dashed var(--ink)' : 'none',
          background: isMe(m) ? 'var(--lime)' : 'transparent',
        }"
      >
        <!-- avatar + name -->
        <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
          <div
            class="font-display"
            :style="{
              width: '34px', height: '34px', borderRadius: '50%', flexShrink: 0,
              background: avatarTone(m.userId), color: avatarFg(m.userId),
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              fontSize: '13px', border: '1.5px solid var(--ink)',
              boxShadow: '1.5px 1.5px 0 var(--ink)',
            }"
          >{{ initials(m.name) }}</div>
          <div>
            <div class="font-display" :style="{ fontSize: '15px', lineHeight: 1 }">
              {{ m.name }}<span v-if="isMe(m)" class="font-mono" :style="{ fontSize: '9px', opacity: 0.6 }"> · VOCÊ</span>
            </div>
            <div class="font-mono" :style="{ fontSize: '9px', opacity: 0.5, marginTop: '2px' }">
              desde {{ m.joinedAt ? new Date(m.joinedAt).toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' }) : '—' }}
            </div>
          </div>
        </div>

        <!-- role badge -->
        <div :style="{ textAlign: 'right' }">
          <span
            class="font-mono"
            :style="{
              fontSize: '9px', letterSpacing: '0.1em', fontWeight: 700,
              padding: '3px 8px',
              background: m.role === 'OWNER' ? 'var(--magenta)'
                        : m.role === 'ADMIN'  ? 'var(--cobalt)'
                        : 'transparent',
              color: m.role === 'MEMBER' ? 'var(--ink)' : 'var(--paper)',
              border: '1.5px solid var(--ink)',
            }"
          >{{ m.role === 'OWNER' ? 'DONO' : m.role === 'ADMIN' ? 'ADMIN' : 'SÓCIO' }}</span>
        </div>
      </div>
    </template>
  </div>
</template>

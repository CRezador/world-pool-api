<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { parseApiDate } from '@/utils/date';
import type { ApiMatch } from '@/types';

const props = defineProps<{ match: ApiMatch }>();
const router = useRouter();

function open() {
  if (props.match.groupId != null) {
    router.push(`/matches/${props.match.groupId}`);
  } else {
    router.push({ path: '/matches', query: { phase: 'knockout' } });
  }
}

function parseKickoff(raw: string | null): { day: string; time: string } {
  if (!raw) return { day: '—', time: '—' };
  // A API entrega "DD/MM/YYYY HH:MM"; parseApiDate cuida desse formato.
  const d = parseApiDate(raw);
  if (isNaN(d.getTime())) return { day: '', time: raw };
  const today = new Date();
  const tomorrow = new Date(today);
  tomorrow.setDate(today.getDate() + 1);
  let day = d.toLocaleDateString('pt-BR', { weekday: 'short' }).toUpperCase().replace('.', '');
  if (d.toDateString() === today.toDateString()) day = 'HOJE';
  else if (d.toDateString() === tomorrow.toDateString()) day = 'AMANHÃ';
  const time = d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  return { day, time };
}

const kickoff = computed(() => parseKickoff(props.match.kickoff));
const dateLabel = computed(() =>
  kickoff.value.day ? `${kickoff.value.day} · ${kickoff.value.time}` : kickoff.value.time
);

const groupLabel = computed(() => {
  const { stage, group } = props.match;
  if (stage === 'GROUP_STAGE' && group) return `GRUPO ${group}`;
  const labels: Record<string, string> = {
    ROUND_OF_16: 'OITAVAS', QUARTER_FINALS: 'QUARTAS',
    SEMI_FINALS: 'SEMI', THIRD_PLACE: '3º LUGAR', FINAL: 'FINAL',
  };
  return labels[stage] ?? stage;
});
</script>

<template>
  <div
    :style="{
      position: 'relative', cursor: 'pointer',
      background: 'var(--paper-2)',
      border: '1.5px solid var(--ink)',
      boxShadow: '4px 4px 0 var(--cobalt), 4px 4px 0 1px var(--ink)',
      borderRadius: '6px', padding: '14px 16px',
    }"
    @click="open()"
  >
    <!-- Top: group pill + date -->
    <div :style="{
      display: 'flex', justifyContent: 'space-between',
      alignItems: 'center', marginBottom: '18px',
    }">
      <span
        class="font-mono"
        :style="{
          background: 'var(--cobalt)', color: 'var(--paper)',
          padding: '5px 11px',
          fontSize: '11px', fontWeight: 700, letterSpacing: '0.14em',
          borderRadius: '999px',
          border: '1.5px solid var(--ink)',
          boxShadow: '2px 2px 0 var(--ink)',
        }"
      >{{ groupLabel }}</span>
      <span class="font-mono" :style="{ fontSize: '12px', letterSpacing: '0.08em', fontWeight: 600 }">
        {{ dateLabel }}
      </span>
    </div>

    <!-- Teams -->
    <div :style="{
      display: 'flex', alignItems: 'center', gap: '12px',
      padding: '4px 6px 14px',
    }">
      <div :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center' }">
        <div :style="{
          width: '56px', height: '56px', borderRadius: '50%',
          border: '2px solid var(--ink)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          overflow: 'hidden',
          boxShadow: '2px 2px 0 var(--cobalt)',
          flexShrink: 0,
        }">
          <img
            v-if="match.home.flag_url"
            :src="match.home.flag_url"
            :alt="match.home.name"
            :style="{ width: '64px', height: '64px', objectFit: 'cover', borderRadius: '50%' }"
          />
        </div>
        <div class="font-display" :style="{ fontSize: '22px', marginTop: '10px', letterSpacing: '0.02em' }">
          {{ match.home.code }}
        </div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.6, marginTop: '2px' }">
          {{ match.home.name.toUpperCase() }}
        </div>
      </div>

      <div class="font-display" :style="{ fontSize: '26px', opacity: 0.4 }">×</div>

      <div :style="{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center' }">
        <div :style="{
          width: '56px', height: '56px', borderRadius: '50%',
          border: '2px solid var(--ink)',
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          overflow: 'hidden',
          boxShadow: '2px 2px 0 var(--coral)',
          flexShrink: 0,
        }">
          <img
            v-if="match.away.flag_url"
            :src="match.away.flag_url"
            :alt="match.away.name"
            :style="{ width: '64px', height: '64px', objectFit: 'cover', borderRadius: '50%' }"
          />
        </div>
        <div class="font-display" :style="{ fontSize: '22px', marginTop: '10px', letterSpacing: '0.02em' }">
          {{ match.away.code }}
        </div>
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.6, marginTop: '2px' }">
          {{ match.away.name.toUpperCase() }}
        </div>
      </div>
    </div>

    <!-- CTA bar -->
    <div :style="{
      padding: '11px 14px',
      background: 'var(--ink)', color: 'var(--paper)',
      display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      borderRadius: '4px',
    }">
      <span class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.1em' }">
        PALPITAR ANTES DAS {{ dateLabel }}
      </span>
      <span class="font-display" :style="{ fontSize: '15px', color: 'var(--lime)', letterSpacing: '0.04em' }">
        → APITAR
      </span>
    </div>
  </div>
</template>

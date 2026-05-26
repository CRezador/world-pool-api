<script setup lang="ts">
import { onMounted, computed } from 'vue';
import { useMatch } from '@/composables/useMatch';
import MatchCarouselCard from './MatchCarouselCard.vue';
import SectionHead from '@/components/SectionHead.vue';

const { upcomingMatches, fetchUpcomingMatches } = useMatch();
onMounted(fetchUpcomingMatches);

const gameDay = computed(() => upcomingMatches.value[0]?.gameDay);
const count = computed(() => upcomingMatches.value.length);
</script>

<template>
  <template v-if="upcomingMatches.length">
    <div :style="{ padding: '18px 18px 0' }">
      <SectionHead
        :kicker="`RODADA ${gameDay} · ${count} JOGO${count !== 1 ? 'S' : ''}`"
        title="Próximas partidas"
        tone="cobalt"
      />
    </div>
    <div :style="{
      display: 'flex',
      gap: '12px',
      padding: '12px 18px 18px',
      overflowX: 'auto',
      scrollSnapType: 'x mandatory',
      WebkitOverflowScrolling: 'touch',
      scrollbarWidth: 'none',
    }">
      <MatchCarouselCard
        v-for="match in upcomingMatches"
        :key="match.id"
        :match="match"
      />
    </div>
  </template>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import BackBar from '@/components/BackBar.vue';
import TicketCard from '@/components/TicketCard.vue';
import FormField from '@/components/FormField.vue';
import ChipToggle from '@/components/ChipToggle.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import PrintButton from '@/components/PrintButton.vue';

const router = useRouter();
const name = ref('');
const isPublic = ref(false);
</script>

<template>
  <div :style="{ background: 'var(--paper)', minHeight: '100%', padding: '18px' }">
    <BackBar title="Novo bolão" kicker="ASSINAR ESTATUTO" fallback="/" />

    <TicketCard accent="lime" :style="{ marginTop: '12px' }">
      <div :style="{ padding: '18px' }">
        <div
          class="font-mono"
          :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, marginBottom: '14px' }"
        >FICHA DE INSCRIÇÃO · 001/26</div>

        <FormField label="NOME DO BOLÃO" v-model="name" />

        <div :style="{ marginTop: '14px' }">
          <div
            class="font-mono"
            :style="{ fontSize: '10px', letterSpacing: '0.14em', marginBottom: '6px' }"
          >VISIBILIDADE</div>
          <div :style="{ display: 'flex', gap: '8px' }">
            <ChipToggle :active="!isPublic" @click="isPublic = false">PRIVADO</ChipToggle>
            <ChipToggle :active="isPublic" @click="isPublic = true">PÚBLICO</ChipToggle>
          </div>
          <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7, marginTop: '6px' }">
            {{ isPublic
              ? 'Aparece na busca. Qualquer um entra.'
              : 'Só entra quem tem o código de 6 dígitos.' }}
          </div>
        </div>

        <PerfDivider />

        <div
          class="font-mono"
          :style="{ fontSize: '10px', letterSpacing: '0.14em', marginBottom: '6px' }"
        >CÓDIGO GERADO</div>
        <div :style="{
          background: 'var(--ink)', color: 'var(--paper)',
          padding: '14px 16px', borderRadius: '3px',
          display: 'flex', justifyContent: 'space-between', alignItems: 'center',
        }">
          <div class="font-display" :style="{ fontSize: '32px', letterSpacing: '0.16em' }">
            QUINTA
          </div>
          <span class="font-mono" :style="{ fontSize: '10px', color: 'var(--lime)' }">↻ TROCAR</span>
        </div>

        <div :style="{ marginTop: '18px' }">
          <PrintButton tone="lime" full @click="router.push('/pool/pool-resenha')">
            Fundar o bolão
          </PrintButton>
        </div>
      </div>
    </TicketCard>
  </div>
</template>

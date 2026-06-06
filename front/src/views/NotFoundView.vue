<script setup lang="ts">
import { useRouter } from 'vue-router';
import PrintButton from '@/components/PrintButton.vue';

const router = useRouter();
</script>

<template>
  <div :style="{
    minHeight: '100vh', background: 'var(--paper)', color: 'var(--ink)',
    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
    padding: '32px 24px', textAlign: 'center',
  }">
    <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.5 }">
      ERRO · PÁGINA NÃO ENCONTRADA
    </div>

    <div
      class="font-display misprint-coral"
      :style="{ fontSize: 'clamp(96px, 22vw, 180px)', lineHeight: 0.85, margin: '12px 0 4px' }"
    >
      404
    </div>

    <div
      class="font-display"
      :style="{ fontSize: 'clamp(22px, 5vw, 36px)', textTransform: 'uppercase', letterSpacing: '0.02em', opacity: 0.85 }"
    >
      Fora de campo
    </div>

    <div
      class="font-mono"
      :style="{ fontSize: '12px', letterSpacing: '0.08em', opacity: 0.55, marginTop: '12px', maxWidth: '340px', lineHeight: 1.6 }"
    >
      Essa página não existe no bolão. Talvez o link esteja errado ou o conteúdo foi removido.
    </div>

    <div :style="{ marginTop: '32px', display: 'flex', gap: '12px', flexWrap: 'wrap', justifyContent: 'center' }">
      <PrintButton tone="magenta" @click="router.push('/')">Ir para o início</PrintButton>
      <PrintButton tone="cobalt" @click="router.back()">Voltar</PrintButton>
    </div>
  </div>
</template>

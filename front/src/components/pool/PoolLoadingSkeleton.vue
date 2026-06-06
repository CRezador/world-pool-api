<template>
  <div :style="{ background: 'var(--paper)', minHeight: '100%' }">
    <div :style="{ padding: '20px 16px 16px', borderBottom: '1.5px solid var(--ink)' }">
      <div class="skeleton" :style="{ height: '11px', width: '90px', marginBottom: '10px' }" />
      <div class="skeleton" :style="{ height: '28px', width: '60%', marginBottom: '12px' }" />
      <div :style="{ display: 'flex', gap: '24px' }">
        <div v-for="n in 3" :key="n" :style="{ display: 'flex', flexDirection: 'column', gap: '4px' }">
          <div class="skeleton" :style="{ height: '18px', width: '36px' }" />
          <div class="skeleton" :style="{ height: '10px', width: '52px' }" />
        </div>
      </div>
    </div>
    <div :style="{ display: 'flex', borderBottom: '1.5px solid var(--ink)', padding: '0 4px' }">
      <div
        v-for="n in 4" :key="n"
        :style="{ flex: 1, padding: '13px 8px', display: 'flex', justifyContent: 'center' }"
      >
        <div class="skeleton" :style="{ height: '10px', width: '48px' }" />
      </div>
    </div>
    <div :style="{ padding: '12px 16px', display: 'flex', flexDirection: 'column', gap: '10px' }">
      <div
        v-for="n in 6" :key="n"
        class="skeleton"
        :style="{ height: '52px', borderRadius: '4px' }"
      />
    </div>
  </div>
</template>

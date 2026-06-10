<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useBreakpoint } from '@/composables/useBreakpoint';
import { useAuth } from '@/composables/useAuth';
import TicketCard from '@/components/TicketCard.vue';
import PrintButton from '@/components/PrintButton.vue';
import Stamp from '@/components/Stamp.vue';
import FormField from '@/components/FormField.vue';
import PerfDivider from '@/components/PerfDivider.vue';
import { login, register } from '@/services/auth.services';

const router = useRouter();
const { isDesktop } = useBreakpoint();
const { checkAuth, clearUser } = useAuth();

const mode = ref<'login' | 'register'>('login');
const email = ref('');
const password = ref('');
const name = ref('');
const showPassword = ref(false);
const remember = ref(true);
const loading = ref(false);
const error = ref('');

async function enter() {
  error.value = '';
  loading.value = true;
  try {
    if (mode.value === 'login') {
      await login(email.value, password.value, remember.value);
      clearUser();
      await checkAuth();
      router.replace('/pools');
    } else {
      await register(name.value, email.value, password.value);
      clearUser();
      await checkAuth();
      router.replace('/pools');
    }
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Erro ao entrar. Verifique seus dados.';
  } finally {
    loading.value = false;
  }
}

const stats = [
  { l: 'BOLÕES ABERTOS', v: '8.241', sub: '124k sócios' },
  { l: 'PALPITES HJ',    v: '38.4k', sub: 'desde 00:00' },
  { l: 'PRÓXIMO JOGO',   v: 'BRA × CRO', sub: 'ao vivo · 76\'' },
];
</script>

<template>
  <!-- ════════════════════════════════════════════
       DESKTOP — split-screen editorial
       ════════════════════════════════════════════ -->
  <div v-if="isDesktop" class="grain desktop-login">

    <!-- LEFT: editorial poster -->
    <div class="left-panel">
      <!-- color bands -->
      <div class="color-bands">
        <div style="background: var(--magenta)" />
        <div style="background: var(--cobalt)" />
        <div style="background: var(--lime)" />
        <div style="background: var(--coral)" />
      </div>

      <!-- top meta row -->
      <div class="left-meta">
        <div class="font-display" style="font-size: 22px; letter-spacing: 0.02em;">
          BOLÃO<span style="color: var(--magenta)">·</span>COPA
          <span style="color: var(--cobalt)"> 26</span>
        </div>
        <div class="font-mono" style="font-size: 10px; letter-spacing: 0.18em; opacity: 0.7;">
          EDIÇÃO Nº 04 · BILHETERIA OFICIAL
        </div>
      </div>

      <!-- big masthead -->
      <div class="masthead-block">
        <div class="font-mono" style="font-size: 11px; letter-spacing: 0.24em; font-weight: 700; color: var(--lime); margin-bottom: 12px;">
          A PARTIDA COMEÇA AQUI ·
        </div>
        <h1 class="font-display masthead-title">
          Apita<br />aí,<br />maestro.
        </h1>
        <div class="font-mono" style="font-size: 13px; letter-spacing: 0.18em; margin-top: 22px; display: flex; gap: 16px;">
          <span>USA</span>
          <span style="color: var(--magenta)">·</span>
          <span>CAN</span>
          <span style="color: var(--cobalt)">·</span>
          <span>MEX</span>
          <span style="margin-left: auto; opacity: 0.6;">VOL. III · 2026</span>
        </div>
      </div>

      <!-- bottom stats -->
      <div class="stats-strip">
        <div v-for="(s, i) in stats" :key="i">
          <div class="font-mono" style="font-size: 9px; letter-spacing: 0.16em; font-weight: 700; opacity: 0.6;">{{ s.l }}</div>
          <div class="font-display" :style="{ fontSize: '28px', lineHeight: '1', marginTop: '4px', color: i === 2 ? 'var(--coral)' : 'var(--paper)' }">{{ s.v }}</div>
          <div class="font-mono" style="font-size: 10px; letter-spacing: 0.1em; opacity: 0.55; margin-top: 2px;">{{ s.sub }}</div>
        </div>
      </div>

      <!-- decorative halftone -->
      <div class="deco-halftone">
        <div class="halftone-lg" style="width: 100%; height: 100%; mask-image: radial-gradient(circle, black 30%, transparent 70%); -webkit-mask-image: radial-gradient(circle, black 30%, transparent 70%);" />
      </div>

      <!-- rotated stamp -->
      <div class="deco-stamp">
        <Stamp tone="coral" :rotate="-8" style="font-size: 16px; padding: 8px 16px; border-width: 3px;">OFICIAL · 2026</Stamp>
      </div>
    </div>

    <!-- RIGHT: form ticket -->
    <div class="right-panel">
      <!-- top row: language + mode toggle -->
      <div class="right-top-row">
        <span class="font-mono" style="font-size: 10px; letter-spacing: 0.16em; opacity: 0.7;">
          🇧🇷 PORTUGUÊS-BR · ●
          <span style="margin-left: 10px; opacity: 0.5;">ENGLISH</span>
        </span>
        <div class="mode-toggle">
          <button
            v-for="t in [{ id: 'login', label: 'Entrar' }, { id: 'register', label: 'Cadastrar' }]"
            :key="t.id"
            class="font-display"
            :style="{
              padding: '8px 16px', fontSize: '13px', letterSpacing: '0.06em',
              background: mode === t.id ? 'var(--ink)' : 'transparent',
              color: mode === t.id ? 'var(--paper)' : 'var(--ink)',
              border: 'none', cursor: 'pointer', textTransform: 'uppercase',
            }"
            @click="mode = (t.id as 'login' | 'register')"
          >{{ t.label }}</button>
        </div>
      </div>

      <!-- headline -->
      <div class="font-mono" style="font-size: 11px; letter-spacing: 0.22em; font-weight: 700;">
        BILHETE Nº 0001 · {{ mode === 'login' ? 'ADMISSÃO DE SÓCIO' : 'NOVO CADASTRO' }}
      </div>
      <div class="font-display misprint-magenta" style="font-size: 56px; line-height: 0.92; margin-top: 6px; text-transform: uppercase;">
        {{ mode === 'login' ? 'Bem-vindo de volta' : 'Faça seu cadastro' }}
      </div>
      <div class="font-mono" style="font-size: 12px; letter-spacing: 0.12em; margin-top: 8px; opacity: 0.7;">
        {{ mode === 'login'
          ? 'Entre pra ver seus bolões, palpites e onde você tá no ranking.'
          : 'Tenha seu cadastro grátis. Crie ou entre em bolões em segundos.' }}
      </div>

      <!-- form ticket card -->
      <div class="form-ticket perf-bottom">
        <div class="form-ticket-serial font-mono">
          Nº 26·0001·{{ mode === 'login' ? 'IN' : 'RG' }}
        </div>

        <FormField v-if="mode === 'register'" label="NOME COMPLETO" v-model="name" />
        <div :style="{ marginTop: mode === 'register' ? '14px' : '0' }">
          <FormField label="E-MAIL" v-model="email" />
        </div>
        <div style="margin-top: 14px;">
          <div class="font-mono" style="font-size: 10px; letter-spacing: 0.14em; margin-bottom: 4px; display: flex; justify-content: space-between;">
            <span>SENHA</span>
            <span v-if="mode === 'login'" style="text-decoration: underline; cursor: pointer; opacity: 0.7;">esqueci a senha</span>
          </div>
          <div style="display: flex; gap: 8px;">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              placeholder="••••••••••"
              style="flex: 1; padding: 12px 14px; background: var(--paper); border: 1.5px solid var(--ink); font-size: 18px; letter-spacing: 0.3em; color: var(--ink); outline: none; font-family: 'JetBrains Mono', monospace;"
            />
            <button
              class="font-mono press"
              style="padding: 0 12px; font-size: 10px; letter-spacing: 0.14em; font-weight: 700; background: var(--paper); border: 1.5px solid var(--ink); cursor: pointer; border-radius: 2px;"
              @click="showPassword = !showPassword"
            >VER</button>
          </div>
          <div v-if="mode === 'register'" class="font-mono" style="font-size: 10px; opacity: 0.6; margin-top: 6px; letter-spacing: 0.06em;">
            mínimo 8 caracteres · com 1 número
          </div>
        </div>

        <!-- remember + stamp -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;" @click="remember = !remember">
            <div :style="{ width: '18px', height: '18px', border: '1.5px solid var(--ink)', background: remember ? 'var(--ink)' : 'transparent', color: 'var(--lime)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '13px' }">{{ remember ? '✓' : '' }}</div>
            <span class="font-mono" style="font-size: 11px; letter-spacing: 0.12em;">LEMBRAR DE MIM</span>
          </label>
        </div>

        <PerfDivider />

        <div v-if="error" class="font-mono" style="font-size: 11px; color: var(--coral); letter-spacing: 0.08em; margin-bottom: 12px;">
          ⚠ {{ error }}
        </div>

        <PrintButton tone="magenta" full size="md" :disabled="loading" @click="enter">
          {{ loading ? 'AGUARDE...' : (mode === 'login' ? 'Entrar no estádio →' : 'Apitar meu cadastro →') }}
        </PrintButton>

        <!-- or divider -->
        <div style="display: flex; align-items: center; gap: 12px; margin: 18px 0 14px; opacity: 0.5;">
          <div style="flex: 1; border-top: 1px dashed var(--ink);" />
          <span class="font-mono" style="font-size: 10px; letter-spacing: 0.18em;">OU</span>
          <div style="flex: 1; border-top: 1px dashed var(--ink);" />
        </div>

        <!-- social buttons -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <button class="font-display press social-btn" style="box-shadow: 3px 3px 0 var(--cobalt);">
            <span class="social-glyph" style="background: var(--cobalt);">G</span>
            Continuar com Google
          </button>
          <button class="font-display press social-btn" style="box-shadow: 3px 3px 0 var(--ink);">
            <span class="social-glyph" style="background: var(--ink);"></span>
            Continuar com Apple
          </button>
        </div>
      </div>

      <!-- footer -->
      <div class="font-mono" style="margin-top: auto; padding-top: 22px; font-size: 11px; letter-spacing: 0.12em; display: flex; justify-content: space-between; align-items: center;">
        <span style="opacity: 0.6;">
          {{ mode === 'login' ? 'NÃO TEM CONTA?' : 'JÁ É SÓCIO?' }}
          <span
            style="text-decoration: underline; cursor: pointer; opacity: 1; margin-left: 4px;"
            @click="mode = mode === 'login' ? 'register' : 'login'"
          >{{ mode === 'login' ? 'CRIAR CADASTRO →' : 'ENTRAR →' }}</span>
        </span>
        <span style="opacity: 0.5;">· TERMOS · PRIVACIDADE · v3.0.4 ·</span>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       MOBILE — ticket card
       ════════════════════════════════════════════ -->
  <div v-else :style="{ background: 'var(--paper)', minHeight: '100%', position: 'relative' }">
    <div :style="{ height: '6px', background: 'var(--magenta)' }" />
    <div :style="{ height: '6px', background: 'var(--cobalt)' }" />
    <div :style="{ height: '6px', background: 'var(--lime)' }" />
    <div :style="{ height: '6px', background: 'var(--coral)' }" />

    <div :style="{ padding: '40px 22px 22px' }">
      <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.18em', fontWeight: 700 }">A PARTIDA COMEÇA AQUI ·</div>
      <h1
        class="font-display misprint-magenta"
        :style="{ fontSize: '72px', lineHeight: 0.88, margin: '8px 0 4px', letterSpacing: '0.005em', textTransform: 'uppercase' }"
      >Bolão<br />Copa<br />2026</h1>
      <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.16em', marginTop: '14px' }">
        USA <span :style="{ color: 'var(--magenta)' }">·</span>
        CAN <span :style="{ color: 'var(--cobalt)' }">·</span>
        MEX
      </div>
    </div>

    <div class="halftone-lg" :style="{ color: 'var(--cobalt)', height: '60px' }" />

    <div :style="{ padding: '20px 22px 0' }">
      <TicketCard accent="magenta">
        <div :style="{ padding: '18px' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.2em', fontWeight: 700, marginBottom: '14px' }">
            {{ mode === 'login' ? 'ENTRADA · ADMITIR UM' : 'NOVO CADASTRO · SÓCIO' }}
          </div>
          <div v-if="mode === 'register'" :style="{ marginBottom: '12px' }">
            <FormField label="NOME COMPLETO" v-model="name" />
          </div>
          <div :style="{ marginBottom: '12px' }">
            <FormField label="E-MAIL" v-model="email" />
          </div>
          <div :style="{ marginBottom: '16px' }">
            <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.14em', marginBottom: '4px' }">SENHA</div>
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              placeholder="••••••••"
              :style="{
                width: '100%', padding: '10px 12px', background: 'var(--paper)',
                border: '1.5px solid var(--ink)', fontSize: '18px', letterSpacing: '0.4em',
                color: 'var(--ink)', outline: 'none', fontFamily: 'JetBrains Mono, monospace',
              }"
            />
            <div v-if="mode === 'register'" class="font-mono" :style="{ fontSize: '10px', opacity: 0.6, marginTop: '6px', letterSpacing: '0.06em' }">
              mínimo 8 caracteres · com 1 número
            </div>
          </div>
          <label v-if="mode === 'login'" :style="{ display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer', marginBottom: '14px' }" @click="remember = !remember">
            <div :style="{ width: '18px', height: '18px', border: '1.5px solid var(--ink)', background: remember ? 'var(--ink)' : 'transparent', color: 'var(--lime)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '13px' }">{{ remember ? '✓' : '' }}</div>
            <span class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">LEMBRAR DE MIM</span>
          </label>
          <div v-if="error" class="font-mono" :style="{ fontSize: '10px', color: 'var(--coral)', letterSpacing: '0.08em', marginBottom: '10px' }">
            ⚠ {{ error }}
          </div>
          <PrintButton tone="magenta" full :disabled="loading" @click="enter">
            {{ loading ? 'AGUARDE...' : (mode === 'login' ? 'Entrar no estádio' : 'Apitar meu cadastro') }}
          </PrintButton>
          <div :style="{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '14px' }">
            <span v-if="mode === 'login'" class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em' }">Esqueceu a senha?</span>
            <span v-else class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.12em', opacity: 0.6 }">Já é sócio?</span>
            <span
              class="font-mono"
              :style="{ fontSize: '10px', letterSpacing: '0.12em', textDecoration: 'underline', cursor: 'pointer' }"
              @click="mode = mode === 'login' ? 'register' : 'login'"
            >{{ mode === 'login' ? 'Cadastrar →' : 'Entrar →' }}</span>
          </div>
        </div>
      </TicketCard>
    </div>

    <div :style="{ padding: '24px 22px' }">
      <Stamp tone="cobalt" :rotate="-4">OFICIAL · NÃO TRANSFERÍVEL</Stamp>
    </div>
  </div>
</template>

<style scoped>
/* ── Desktop ── */
.desktop-login {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  min-height: 100vh;
  background: var(--paper);
  color: var(--ink);
  position: relative;
}

.left-panel {
  position: relative;
  background: var(--ink);
  color: var(--paper);
  padding: 32px 40px 36px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.color-bands {
  display: flex;
  margin-bottom: 24px;
}
.color-bands > div {
  flex: 1;
  height: 10px;
}

.left-meta {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 16px;
}

.masthead-block {
  margin-top: auto;
}

.masthead-title {
  font-size: 132px;
  line-height: 0.84;
  margin: 0;
  letter-spacing: 0.005em;
  text-transform: uppercase;
  text-shadow: 5px 5px 0 var(--magenta), 10px 10px 0 var(--cobalt);
}

.stats-strip {
  margin-top: 28px;
  padding-top: 18px;
  border-top: 1.5px dashed rgba(242, 233, 210, 0.4);
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 18px;
}

.deco-halftone {
  position: absolute;
  top: 36px;
  right: -40px;
  width: 240px;
  height: 240px;
  color: var(--magenta);
  transform: rotate(-12deg);
  opacity: 0.5;
  pointer-events: none;
}

.deco-stamp {
  position: absolute;
  bottom: 240px;
  right: 36px;
  transform: rotate(-8deg);
  pointer-events: none;
}

.right-panel {
  padding: 32px 48px;
  display: flex;
  flex-direction: column;
  background: var(--paper);
}

.right-top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 18px;
}

.mode-toggle {
  display: inline-flex;
  border: 1.5px solid var(--ink);
  border-radius: 3px;
  overflow: hidden;
}

.form-ticket {
  margin-top: 24px;
  padding: 24px;
  background: var(--paper-2);
  border: 1.5px solid var(--ink);
  box-shadow: 6px 6px 0 var(--magenta), 6px 6px 0 1px var(--ink);
  display: flex;
  flex-direction: column;
}

.form-ticket-serial {
  align-self: flex-end;
  font-size: 9px;
  letter-spacing: 0.18em;
  opacity: 0.5;
  margin-bottom: 8px;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 12px 14px;
  background: var(--paper);
  border: 1.5px solid var(--ink);
  cursor: pointer;
  border-radius: 3px;
  font-size: 14px;
  letter-spacing: 0.04em;
  color: var(--ink);
  text-transform: uppercase;
}

.social-glyph {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: var(--paper);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}
</style>

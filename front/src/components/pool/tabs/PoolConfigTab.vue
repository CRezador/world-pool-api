<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import PrintButton from '@/components/PrintButton.vue';
import { toneVar } from '@/data/mock';
import { fetchPoolMembers, updateMemberRole, updatePool, regenerateCode, leavePool, deletePool } from '@/composables/usePools';
import type { PoolMemberItem } from '@/composables/usePools';
import type { Pool } from '@/types';

type Role = 'OWNER' | 'ADMIN' | 'MEMBER';

const props = defineProps<{
  pool: Pool;
  myRole: Role;
  joinedAt: string;
}>();

const router = useRouter();

const loading = ref(true);
const members = ref<PoolMemberItem[]>([]);

const isOwner = computed(() => props.myRole === 'OWNER');
const isAdmin = computed(() => props.myRole === 'OWNER' || props.myRole === 'ADMIN');

const manageableMembers = computed(() =>
  members.value.filter(m => m.role !== 'OWNER'),
);

onMounted(async () => {
  try {
    if (isOwner.value) {
      members.value = await fetchPoolMembers(props.pool.id);
    }
  } finally {
    loading.value = false;
  }
});

// — Gerenciar roles —
const togglingRole = ref<number | null>(null);

async function toggleRole(m: PoolMemberItem) {
  if (togglingRole.value === m.memberId) return;
  togglingRole.value = m.memberId;
  const newRole = m.role === 'ADMIN' ? 'MEMBER' : 'ADMIN';
  try {
    await updateMemberRole(props.pool.id, m.memberId, newRole);
    m.role = newRole;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao alterar papel.');
  } finally {
    togglingRole.value = null;
  }
}

// — Editar nome —
const editingName = ref(false);
const nameInput   = ref('');
const savingName  = ref(false);

function startEditName() {
  nameInput.value  = props.pool.name;
  editingName.value = true;
}

async function saveName() {
  if (!nameInput.value.trim() || savingName.value) return;
  savingName.value = true;
  try {
    await updatePool(props.pool.id, { name: nameInput.value.trim() });
    props.pool.name = nameInput.value.trim();
    editingName.value = false;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao salvar nome.');
  } finally {
    savingName.value = false;
  }
}

// — Visibilidade —
const togglingVisibility = ref(false);

async function toggleVisibility() {
  if (togglingVisibility.value) return;
  togglingVisibility.value = true;
  try {
    await updatePool(props.pool.id, { is_public: !props.pool.isPublic });
    props.pool.isPublic = !props.pool.isPublic;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao alterar visibilidade.');
  } finally {
    togglingVisibility.value = false;
  }
}

// — Regenerar código —
const regenerating   = ref(false);
const confirmRegen   = ref(false);

async function doRegenerate() {
  regenerating.value = true;
  confirmRegen.value = false;
  try {
    const res = await regenerateCode(props.pool.id);
    const raw = res.data.data ?? res.data;
    props.pool.code = raw.join_code;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao regenerar código.');
  } finally {
    regenerating.value = false;
  }
}

// — Copiar código —
const copied = ref(false);
function copyCode() {
  navigator.clipboard.writeText(props.pool.code).then(() => {
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
  });
}

// — Sair do bolão —
const confirmLeave = ref(false);
const leaving      = ref(false);

async function doLeave() {
  leaving.value = true;
  try {
    await leavePool(props.pool.id);
    router.push('/pools');
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao sair do bolão.');
    leaving.value = false;
    confirmLeave.value = false;
  }
}

// — Encerrar bolão —
const confirmDelete = ref(false);
const deleting      = ref(false);

async function doDelete() {
  deleting.value = true;
  try {
    await deletePool(props.pool.id);
    router.push('/pools');
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao encerrar bolão.');
    deleting.value = false;
    confirmDelete.value = false;
  }
}
</script>

<template>
  <!-- Skeleton -->
  <div v-if="loading" :style="{ padding: '14px 18px 18px', display: 'flex', flexDirection: 'column', gap: '10px' }">
    <div class="skeleton" :style="{ height: '28px', width: '160px', marginBottom: '4px' }" />
    <div v-for="n in 4" :key="n" class="skeleton" :style="{ height: '60px' }" />
  </div>

  <div v-else class="fade-up" :style="{ padding: '14px 18px 32px', display: 'flex', flexDirection: 'column', gap: '20px' }">

    <!-- Código de acesso (ADMIN + OWNER) -->
    <section v-if="isAdmin">
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }"
      >CÓDIGO DE ACESSO</div>

      <!-- Código display -->
      <div :style="{
        padding: '16px 18px',
        background: 'var(--ink)', color: 'var(--paper)',
        border: '1.5px solid var(--ink)',
        display: 'flex', alignItems: 'center', justifyContent: 'space-between',
      }">
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.5, marginBottom: '6px' }">CÓDIGO ATUAL</div>
          <div class="font-display" :style="{ fontSize: '32px', letterSpacing: '0.16em' }">{{ pool.code }}</div>
        </div>
        <div
          class="halftone"
          :style="{ width: '60px', height: '48px', color: 'var(--magenta)', opacity: 0.4, flexShrink: 0 }"
        />
      </div>

      <!-- Ações: Copiar + Regenerar -->
      <div v-if="!confirmRegen" :style="{ display: 'flex', gap: '0', marginTop: '8px' }">
        <button
          class="font-mono press"
          :style="{
            flex: 1, padding: '12px',
            background: copied ? toneVar('lime') : 'var(--paper-2)',
            color: 'var(--ink)',
            border: '1.5px solid var(--ink)',
            fontSize: '11px', letterSpacing: '0.14em', fontWeight: 700,
            cursor: 'pointer', transition: 'background 0.18s',
          }"
          @click="copyCode"
        >{{ copied ? '✓ COPIADO' : '⎘ COPIAR' }}</button>
        <button
          class="font-mono press"
          :style="{
            flex: 1, padding: '12px',
            background: 'var(--paper-2)', color: 'var(--ink)',
            border: '1.5px solid var(--ink)', borderLeft: 'none',
            fontSize: '11px', letterSpacing: '0.14em', fontWeight: 700,
            cursor: 'pointer',
          }"
          @click="confirmRegen = true"
        >↻ REGENERAR</button>
      </div>

      <!-- Confirm regenerar -->
      <div v-else :style="{
        marginTop: '8px', padding: '12px 14px',
        border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
        display: 'flex', flexDirection: 'column', gap: '8px',
      }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em', lineHeight: 1.5 }">
          O código atual deixará de funcionar. Novos sócios precisarão do novo código.
        </div>
        <div :style="{ display: 'flex', gap: '8px' }">
          <button
            class="font-display press"
            :style="{
              flex: 1, padding: '9px', fontSize: '13px', cursor: 'pointer',
              background: 'var(--paper)', border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
            }"
            @click="confirmRegen = false"
          >Cancelar</button>
          <PrintButton tone="magenta" full :disabled="regenerating" @click="doRegenerate">
            {{ regenerating ? 'Gerando…' : 'Gerar novo código' }}
          </PrintButton>
        </div>
      </div>
    </section>

    <!-- Configurações do bolão (OWNER only) -->
    <section v-if="isOwner">
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }"
      >CONFIGURAÇÕES</div>

      <!-- Editar nome -->
      <div :style="{
        padding: '14px', border: '1.5px solid var(--ink)',
        background: 'var(--paper-2)', marginBottom: '8px',
      }">
        <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6, marginBottom: '6px' }">NOME DO BOLÃO</div>
        <div v-if="!editingName" :style="{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '12px' }">
          <div class="font-display" :style="{ fontSize: '20px' }">{{ pool.name }}</div>
          <button
            class="font-mono press"
            :style="{
              padding: '5px 10px', fontSize: '10px', letterSpacing: '0.12em', fontWeight: 700,
              background: 'var(--paper)', border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)', cursor: 'pointer',
            }"
            @click="startEditName"
          >✎ EDITAR</button>
        </div>
        <div v-else :style="{ display: 'flex', gap: '8px', alignItems: 'center' }">
          <input
            v-model="nameInput"
            class="font-display"
            :style="{
              flex: 1, padding: '8px 10px', fontSize: '18px',
              border: '1.5px solid var(--ink)', background: 'var(--paper)',
              outline: 'none',
            }"
            maxlength="60"
            @keydown.enter="saveName"
            @keydown.escape="editingName = false"
          />
          <button
            class="font-mono press"
            :style="{
              padding: '8px 12px', fontSize: '10px', letterSpacing: '0.1em', fontWeight: 700,
              background: 'var(--lime)', color: 'var(--ink)',
              border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
              cursor: 'pointer',
            }"
            :disabled="savingName"
            @click="saveName"
          >{{ savingName ? '…' : 'OK' }}</button>
          <button
            class="font-mono press"
            :style="{
              padding: '8px 10px', fontSize: '10px',
              background: 'var(--paper)', border: '1.5px solid var(--ink)',
              cursor: 'pointer',
            }"
            @click="editingName = false"
          >✕</button>
        </div>
      </div>

      <!-- Visibilidade toggle -->
      <button
        class="press"
        :disabled="togglingVisibility"
        :style="{
          width: '100%', padding: '14px',
          display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '12px',
          border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
          cursor: 'pointer', textAlign: 'left',
        }"
        @click="toggleVisibility"
      >
        <div>
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.14em', opacity: 0.6, marginBottom: '4px' }">VISIBILIDADE</div>
          <div class="font-display" :style="{ fontSize: '18px' }">{{ pool.isPublic ? 'Público' : 'Privado' }}</div>
          <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.65, marginTop: '2px' }">
            {{ pool.isPublic ? 'Aparece na busca de bolões' : 'Apenas quem tem o código pode entrar' }}
          </div>
        </div>
        <!-- Toggle pill -->
        <div :style="{
          width: '44px', height: '24px', borderRadius: '12px', flexShrink: 0,
          background: pool.isPublic ? toneVar('lime') : 'var(--paper-3)',
          border: '1.5px solid var(--ink)',
          position: 'relative', transition: 'background 0.2s',
        }">
          <div :style="{
            position: 'absolute', top: '2px',
            left: pool.isPublic ? '20px' : '2px',
            width: '16px', height: '16px', borderRadius: '50%',
            background: 'var(--ink)', transition: 'left 0.2s',
          }" />
        </div>
      </button>
    </section>

    <!-- Gerenciar admins (OWNER only) -->
    <section v-if="isOwner && manageableMembers.length">
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }"
      >GERENCIAR ADMINS</div>

      <div :style="{ border: '1.5px solid var(--ink)' }">
        <div
          v-for="(m, i) in manageableMembers"
          :key="m.memberId"
          :style="{
            display: 'flex', alignItems: 'center', gap: '12px',
            padding: '11px 14px',
            borderBottom: i < manageableMembers.length - 1 ? '1px dashed var(--ink)' : 'none',
            background: m.role === 'ADMIN' ? 'rgba(92, 157, 247, 0.08)' : 'var(--paper-2)',
          }"
        >
          <!-- Iniciais -->
          <div
            class="font-display"
            :style="{
              width: '32px', height: '32px', borderRadius: '50%', flexShrink: 0,
              background: m.role === 'ADMIN' ? toneVar('cobalt') : toneVar('paper-3'),
              color: m.role === 'ADMIN' ? 'var(--paper)' : 'var(--ink)',
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              fontSize: '12px', border: '1.5px solid var(--ink)',
            }"
          >{{ m.name[0].toUpperCase() }}</div>

          <div :style="{ flex: 1, minWidth: 0 }">
            <div
              class="font-display"
              :style="{
                fontSize: '15px', lineHeight: 1,
                whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis',
              }"
            >{{ m.name }}</div>
            <div
              class="font-mono"
              :style="{
                fontSize: '9px', letterSpacing: '0.12em', fontWeight: 700, marginTop: '3px',
                color: m.role === 'ADMIN' ? toneVar('cobalt') : 'var(--ink)',
                opacity: m.role === 'ADMIN' ? 1 : 0.45,
              }"
            >{{ m.role === 'ADMIN' ? 'ADMIN' : 'SÓCIO' }}</div>
          </div>

          <button
            class="font-mono press"
            :disabled="togglingRole === m.memberId"
            :style="{
              padding: '6px 10px', fontSize: '10px', letterSpacing: '0.1em', fontWeight: 700,
              cursor: 'pointer', flexShrink: 0,
              background: m.role === 'ADMIN' ? 'var(--paper)' : toneVar('cobalt'),
              color: m.role === 'ADMIN' ? 'var(--ink)' : 'var(--paper)',
              border: '1.5px solid var(--ink)',
              boxShadow: '2px 2px 0 var(--ink)',
              opacity: togglingRole === m.memberId ? 0.5 : 1,
            }"
            @click="toggleRole(m)"
          >
            {{ togglingRole === m.memberId ? '…' : m.role === 'ADMIN' ? 'Remover admin' : 'Tornar admin' }}
          </button>
        </div>
      </div>
    </section>

    <!-- Zona de saída / perigo -->
    <section>
      <div
        class="font-mono"
        :style="{ fontSize: '9px', letterSpacing: '0.18em', fontWeight: 700, opacity: 0.6, marginBottom: '8px' }"
      >{{ isOwner ? 'ZONA DE PERIGO' : 'SAIR' }}</div>

      <!-- Sair do bolão (não-owners) -->
      <div v-if="!isOwner">
        <div v-if="confirmLeave" :style="{
          padding: '12px 14px', border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
          display: 'flex', flexDirection: 'column', gap: '8px',
        }">
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em' }">
            Você perderá acesso ao bolão e ao seu histórico de palpites.
          </div>
          <div :style="{ display: 'flex', gap: '8px' }">
            <button
              class="font-display press"
              :style="{
                flex: 1, padding: '9px', fontSize: '13px', cursor: 'pointer',
                background: 'var(--paper)', border: '1.5px solid var(--ink)',
                boxShadow: '2px 2px 0 var(--ink)',
              }"
              @click="confirmLeave = false"
            >Cancelar</button>
            <PrintButton tone="magenta" full :disabled="leaving" @click="doLeave">
              {{ leaving ? 'Saindo…' : 'Confirmar saída' }}
            </PrintButton>
          </div>
        </div>

        <button
          v-else
          class="font-display press"
          :style="{
            width: '100%', padding: '14px 16px',
            display: 'flex', alignItems: 'center', gap: '12px',
            border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
            cursor: 'pointer', color: 'var(--magenta)',
          }"
          @click="confirmLeave = true"
        >
          <span :style="{ fontSize: '22px' }">→</span>
          <div :style="{ textAlign: 'left' }">
            <div :style="{ fontSize: '16px' }">Sair do bolão</div>
            <div class="font-mono" :style="{ fontSize: '10px', opacity: 0.7, marginTop: '2px', color: 'var(--ink)' }">
              Entrou em {{ joinedAt ? new Date(joinedAt).toLocaleDateString('pt-BR') : '—' }}

            </div>
          </div>
        </button>
      </div>

      <!-- Encerrar bolão (OWNER only) -->
      <div v-if="isOwner">
        <div v-if="confirmDelete" :style="{
          padding: '12px 14px', border: '1.5px solid var(--magenta)', background: 'var(--paper-2)',
          display: 'flex', flexDirection: 'column', gap: '8px',
        }">
          <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.1em' }">
            Todos os palpites e o histórico do bolão serão apagados permanentemente.
          </div>
          <div :style="{ display: 'flex', gap: '8px' }">
            <button
              class="font-display press"
              :style="{
                flex: 1, padding: '9px', fontSize: '13px', cursor: 'pointer',
                background: 'var(--paper)', border: '1.5px solid var(--ink)',
                boxShadow: '2px 2px 0 var(--ink)',
              }"
              @click="confirmDelete = false"
            >Cancelar</button>
            <PrintButton tone="magenta" full :disabled="deleting" @click="doDelete">
              {{ deleting ? 'Encerrando…' : 'Encerrar de vez' }}
            </PrintButton>
          </div>
        </div>

        <button
          v-else
          class="font-display press"
          :style="{
            width: '100%', padding: '14px 16px',
            display: 'flex', alignItems: 'center', gap: '12px',
            border: '1.5px solid var(--magenta)', background: 'var(--paper-2)',
            cursor: 'pointer', color: 'var(--magenta)',
          }"
          @click="confirmDelete = true"
        >
          <span :style="{ fontSize: '22px' }">✕</span>
          <div :style="{ fontSize: '16px' }">Encerrar bolão</div>
        </button>
      </div>
    </section>

  </div>
</template>

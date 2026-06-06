<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import PrintButton from '@/components/PrintButton.vue';
import { toneVar } from '@/data/mock';
import {
  fetchPoolMembers, updateMemberRole, updatePool,
  regenerateCode, leavePool, deletePool,
} from '@/composables/usePools';
import type { PoolMemberItem } from '@/composables/usePools';
import { useAuth } from '@/composables/useAuth';
import type { Pool } from '@/types';

type Role = 'OWNER' | 'ADMIN' | 'MEMBER';

const props = defineProps<{
  pool: Pool;
  myRole: Role;
  joinedAt: string;
}>();

const router = useRouter();
const { user } = useAuth();

const isOwner = computed(() => props.myRole === 'OWNER');
const isAdmin = computed(() => props.myRole === 'OWNER' || props.myRole === 'ADMIN');
const isMe = (m: PoolMemberItem) => user.value?.id === m.userId;

// — Members —
const loading = ref(true);
const members = ref<PoolMemberItem[]>([]);
const manageableMembers = computed(() => members.value.filter(m => m.role !== 'OWNER'));

onMounted(async () => {
  try {
    if (isOwner.value) members.value = await fetchPoolMembers(props.pool.id);
  } finally {
    loading.value = false;
  }
});

// — Toggle role —
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

// — Edit name —
const editingName = ref(false);
const nameInput   = ref('');
const savingName  = ref(false);

function startEditName() {
  nameInput.value   = props.pool.name;
  editingName.value = true;
}
async function saveName() {
  if (!nameInput.value.trim() || savingName.value) return;
  savingName.value = true;
  try {
    await updatePool(props.pool.id, { name: nameInput.value.trim() });
    props.pool.name   = nameInput.value.trim();
    editingName.value = false;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao salvar nome.');
  } finally {
    savingName.value = false;
  }
}

// — Visibility —
const togglingVis = ref(false);
async function toggleVisibility() {
  if (togglingVis.value) return;
  togglingVis.value = true;
  try {
    await updatePool(props.pool.id, { is_public: !props.pool.isPublic });
    props.pool.isPublic = !props.pool.isPublic;
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao alterar visibilidade.');
  } finally {
    togglingVis.value = false;
  }
}

// — Regenerate code —
const regenerating = ref(false);
const confirmRegen = ref(false);
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

// — Copy code —
const copied = ref(false);
function copyCode() {
  navigator.clipboard.writeText(props.pool.code).then(() => {
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
  });
}

// — Leave pool —
const confirmLeave = ref(false);
const leaving      = ref(false);
async function doLeave() {
  leaving.value = true;
  try {
    await leavePool(props.pool.id);
    router.push('/pools');
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao sair do bolão.');
    leaving.value      = false;
    confirmLeave.value = false;
  }
}

// — Delete pool —
const confirmDelete = ref(false);
const deleting      = ref(false);
async function doDelete() {
  deleting.value = true;
  try {
    await deletePool(props.pool.id);
    router.push('/pools');
  } catch (e: any) {
    alert(e.response?.data?.message ?? 'Erro ao encerrar bolão.');
    deleting.value      = false;
    confirmDelete.value = false;
  }
}
</script>

<template>
  <div :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr', flex: 1, minHeight: 0 }">

    <!-- LEFT: code + settings -->
    <div :style="{ padding: '28px 32px', borderRight: '1.5px solid var(--ink)', overflowY: 'auto' }">

      <!-- Code section -->
      <section v-if="isAdmin">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.55, marginBottom: '10px' }">
          CÓDIGO DE ACESSO
        </div>

        <!-- Dark code card -->
        <div :style="{
          position: 'relative', overflow: 'hidden',
          background: 'var(--ink)', color: 'var(--paper)',
          border: '1.5px solid var(--ink)', padding: '20px 24px',
        }">
          <div
            class="halftone"
            :style="{
              position: 'absolute', top: 0, right: 0, width: '120px', height: '100%',
              color: 'var(--coral)', opacity: 0.5, pointerEvents: 'none',
            }"
          />
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.22em', opacity: 0.55 }">CÓDIGO ATUAL</div>
          <div class="font-display" :style="{ fontSize: '56px', lineHeight: 0.95, letterSpacing: '0.1em', marginTop: '8px' }">{{ pool.code }}</div>
        </div>

        <!-- Copy / Regenerate -->
        <div v-if="!confirmRegen" :style="{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px', marginTop: '12px' }">
          <button
            class="font-display press"
            :style="{
              padding: '14px 8px', cursor: 'pointer',
              background: copied ? 'var(--lime)' : 'var(--paper-2)',
              color: 'var(--ink)', border: '1.5px solid var(--ink)',
              boxShadow: '3px 3px 0 var(--ink)', fontSize: '15px',
              textTransform: 'uppercase', transition: 'background 0.18s ease',
            }"
            @click="copyCode"
          >{{ copied ? '✓ Copiado' : '⎘ Copiar' }}</button>
          <button
            class="font-display press"
            :style="{
              padding: '14px 8px', cursor: 'pointer',
              background: 'var(--paper-2)', color: 'var(--ink)',
              border: '1.5px solid var(--ink)', boxShadow: '3px 3px 0 var(--ink)',
              fontSize: '15px', textTransform: 'uppercase',
            }"
            @click="confirmRegen = true"
          >↻ Regenerar</button>
        </div>

        <!-- Confirm regenerate -->
        <div v-else :style="{
          marginTop: '12px', padding: '14px',
          border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
          display: 'flex', flexDirection: 'column', gap: '10px',
        }">
          <div class="font-mono" :style="{ fontSize: '11px', letterSpacing: '0.08em', lineHeight: 1.5, opacity: 0.8 }">
            O código atual deixará de funcionar. Novos sócios precisarão do novo código.
          </div>
          <div :style="{ display: 'flex', gap: '10px' }">
            <button
              class="font-display press"
              :style="{
                flex: 1, padding: '10px', fontSize: '14px', cursor: 'pointer',
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

      <!-- Settings section (OWNER only) -->
      <section v-if="isOwner" :style="{ marginTop: '28px' }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.55, marginBottom: '10px' }">
          CONFIGURAÇÕES
        </div>

        <!-- Pool name -->
        <div :style="{ padding: '16px 18px', background: 'var(--paper-2)', border: '1.5px solid var(--ink)', marginBottom: '12px' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', opacity: 0.6, marginBottom: '6px' }">NOME DO BOLÃO</div>
          <div v-if="!editingName" :style="{ display: 'flex', alignItems: 'center', gap: '12px' }">
            <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1, flex: 1 }">{{ pool.name }}</div>
            <button
              class="font-display press"
              :style="{
                flexShrink: 0, padding: '7px 14px', cursor: 'pointer', fontSize: '13px',
                background: 'var(--paper)', border: '1.5px solid var(--ink)',
                boxShadow: '2px 2px 0 var(--ink)', textTransform: 'uppercase',
              }"
              @click="startEditName"
            >✎ Editar</button>
          </div>
          <div v-else :style="{ display: 'flex', gap: '10px', alignItems: 'center' }">
            <input
              v-model="nameInput"
              class="font-display"
              :style="{
                flex: 1, fontSize: '22px', border: 'none',
                borderBottom: '2px solid var(--ink)', background: 'transparent',
                outline: 'none', padding: '2px 0',
              }"
              maxlength="60"
              @keydown.enter="saveName"
              @keydown.escape="editingName = false"
            />
            <button
              class="font-display press"
              :style="{
                padding: '7px 14px', cursor: 'pointer', fontSize: '13px',
                background: 'var(--ink)', color: 'var(--paper)',
                border: '1.5px solid var(--ink)', textTransform: 'uppercase',
              }"
              :disabled="savingName"
              @click="saveName"
            >{{ savingName ? '…' : 'OK' }}</button>
          </div>
        </div>

        <!-- Visibility toggle -->
        <div :style="{ padding: '16px 18px', background: 'var(--paper-2)', border: '1.5px solid var(--ink)' }">
          <div :style="{ display: 'flex', alignItems: 'center', gap: '12px' }">
            <div :style="{ flex: 1 }">
              <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.18em', opacity: 0.6, marginBottom: '6px' }">VISIBILIDADE</div>
              <div class="font-display" :style="{ fontSize: '26px', lineHeight: 1 }">{{ pool.isPublic ? 'Público' : 'Privado' }}</div>
            </div>
            <!-- Toggle pill -->
            <button
              class="press"
              :disabled="togglingVis"
              :style="{
                flexShrink: 0, width: '52px', height: '30px', borderRadius: '30px',
                border: '1.5px solid var(--ink)', cursor: 'pointer', padding: 0,
                background: pool.isPublic ? 'var(--lime)' : 'var(--paper)',
                position: 'relative', transition: 'background 0.18s ease',
              }"
              @click="toggleVisibility"
            >
              <span :style="{
                position: 'absolute', top: '2px', left: pool.isPublic ? '24px' : '2px',
                width: '22px', height: '22px', borderRadius: '50%',
                background: 'var(--ink)', transition: 'left 0.18s ease', display: 'block',
              }" />
            </button>
          </div>
          <div class="font-mono" :style="{ fontSize: '11px', opacity: 0.65, marginTop: '10px', lineHeight: 1.5 }">
            {{ pool.isPublic ? 'Aparece na busca pública.' : 'Apenas quem tem o código pode entrar.' }}
          </div>
        </div>
      </section>

      <!-- Leave pool (non-owners) -->
      <section v-if="!isOwner" :style="{ marginTop: '28px' }">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.55, marginBottom: '10px' }">
          SAIR DO BOLÃO
        </div>
        <div v-if="confirmLeave" :style="{
          padding: '14px', border: '1.5px solid var(--ink)', background: 'var(--paper-2)',
          display: 'flex', flexDirection: 'column', gap: '10px',
        }">
          <div class="font-mono" :style="{ fontSize: '11px', lineHeight: 1.5 }">
            Você perderá acesso ao bolão e ao seu histórico de palpites.
          </div>
          <div :style="{ display: 'flex', gap: '10px' }">
            <button class="font-display press" :style="{
              flex: 1, padding: '10px', fontSize: '14px', cursor: 'pointer',
              background: 'var(--paper)', border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
            }" @click="confirmLeave = false">Cancelar</button>
            <PrintButton tone="magenta" full :disabled="leaving" @click="doLeave">
              {{ leaving ? 'Saindo…' : 'Confirmar saída' }}
            </PrintButton>
          </div>
        </div>
        <button v-else class="font-display press" :style="{
          width: '100%', padding: '14px 16px', cursor: 'pointer', textAlign: 'left',
          background: 'var(--paper-2)', border: '1.5px solid var(--magenta)',
          boxShadow: '2px 2px 0 var(--magenta)', fontSize: '15px', color: 'var(--magenta)',
        }" @click="confirmLeave = true">→ Sair do bolão</button>
      </section>
    </div>

    <!-- RIGHT: admins + danger zone -->
    <div :style="{ padding: '28px 32px', background: 'var(--paper-2)', overflowY: 'auto' }">

      <section v-if="isOwner">
        <div class="font-mono" :style="{ fontSize: '10px', letterSpacing: '0.22em', fontWeight: 700, opacity: 0.55, marginBottom: '14px' }">
          GERENCIAR ADMINS
        </div>

        <!-- Table header -->
        <div
          class="font-mono"
          :style="{
            display: 'grid', gridTemplateColumns: '1fr 100px 110px', gap: '10px',
            padding: '8px 12px', fontSize: '9px', letterSpacing: '0.14em',
            fontWeight: 700, opacity: 0.55, borderBottom: '1.5px solid var(--ink)',
          }"
        >
          <span>SÓCIO</span>
          <span :style="{ textAlign: 'center' }">PAPEL</span>
          <span :style="{ textAlign: 'right' }">AÇÃO</span>
        </div>

        <!-- Loading skeleton -->
        <template v-if="loading">
          <div v-for="n in 4" :key="n" :style="{ padding: '12px', borderBottom: '1px dashed var(--ink)' }">
            <div class="skeleton" :style="{ height: '36px' }" />
          </div>
        </template>

        <!-- Member rows -->
        <template v-else>
          <div
            v-for="m in manageableMembers"
            :key="m.memberId"
            :style="{
              display: 'grid', gridTemplateColumns: '1fr 100px 110px', gap: '10px',
              padding: '12px', alignItems: 'center',
              borderBottom: '1px dashed var(--ink)',
              background: isMe(m) ? 'var(--paper)' : 'transparent',
            }"
          >
            <!-- Avatar + name -->
            <div :style="{ display: 'flex', alignItems: 'center', gap: '10px' }">
              <div
                class="font-display"
                :style="{
                  width: '36px', height: '36px', borderRadius: '50%', flexShrink: 0,
                  background: m.role === 'ADMIN' ? 'var(--cobalt)' : toneVar('coral'),
                  color: 'var(--paper)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: '14px', border: '1.5px solid var(--ink)',
                  boxShadow: '1.5px 1.5px 0 var(--ink)',
                }"
              >{{ m.name[0].toUpperCase() }}</div>
              <div>
                <div class="font-display" :style="{ fontSize: '16px', lineHeight: 1 }">
                  {{ m.name }}{{ isMe(m) ? ' (você)' : '' }}
                </div>
              </div>
            </div>

            <!-- Role badge -->
            <div :style="{ textAlign: 'center' }">
              <span
                class="font-mono"
                :style="{
                  fontSize: '9px', padding: '3px 8px',
                  background: m.role === 'ADMIN' ? 'var(--cobalt)' : 'var(--paper)',
                  color: m.role === 'ADMIN' ? 'var(--paper)' : 'var(--ink)',
                  border: '1.5px solid var(--ink)', fontWeight: 700, letterSpacing: '0.12em',
                }"
              >{{ m.role === 'ADMIN' ? 'ADMIN' : 'SÓCIO' }}</span>
            </div>

            <!-- Action -->
            <div :style="{ textAlign: 'right' }">
              <span v-if="isMe(m)" class="font-mono" :style="{ fontSize: '9px', opacity: 0.45 }">VOCÊ</span>
              <button
                v-else
                class="font-mono press"
                :disabled="togglingRole === m.memberId"
                :style="{
                  cursor: 'pointer', borderRadius: '3px',
                  padding: '7px 10px', fontSize: '10px', fontWeight: 700,
                  border: '1.5px solid var(--ink)',
                  background: m.role === 'ADMIN' ? 'var(--paper)' : 'var(--cobalt)',
                  color: m.role === 'ADMIN' ? 'var(--ink)' : 'var(--paper)',
                  boxShadow: '2px 2px 0 var(--ink)',
                  opacity: togglingRole === m.memberId ? 0.5 : 1,
                }"
                @click="toggleRole(m)"
              >{{ togglingRole === m.memberId ? '…' : m.role === 'ADMIN' ? 'Remover' : 'Admin' }}</button>
            </div>
          </div>
        </template>
      </section>

      <!-- Danger zone (OWNER only) -->
      <section v-if="isOwner" :style="{ marginTop: '28px' }">
        <div :style="{ padding: '16px 18px', border: '1.5px solid var(--ink)', background: 'var(--paper)' }">
          <div class="font-mono" :style="{ fontSize: '9px', letterSpacing: '0.22em', fontWeight: 700, color: 'var(--magenta)', marginBottom: '12px' }">
            ZONA DE PERIGO
          </div>

          <!-- Recalculate -->
          <button
            class="font-display press"
            :style="{
              width: '100%', padding: '12px 16px', cursor: 'pointer',
              textAlign: 'left', background: 'var(--paper-2)',
              border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
              fontSize: '15px', marginBottom: '10px',
            }"
          >↺ Recalcular pontuação</button>

          <!-- Delete / confirm delete -->
          <div v-if="confirmDelete" :style="{
            padding: '12px 14px', border: '1.5px solid var(--magenta)', background: 'var(--paper-2)',
            display: 'flex', flexDirection: 'column', gap: '10px',
          }">
            <div class="font-mono" :style="{ fontSize: '10px', lineHeight: 1.5, opacity: 0.8 }">
              Todos os palpites e o histórico do bolão serão apagados permanentemente.
            </div>
            <div :style="{ display: 'flex', gap: '10px' }">
              <button
                class="font-display press"
                :style="{
                  flex: 1, padding: '10px', fontSize: '14px', cursor: 'pointer',
                  background: 'var(--paper)', border: '1.5px solid var(--ink)', boxShadow: '2px 2px 0 var(--ink)',
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
              width: '100%', padding: '12px 16px', cursor: 'pointer',
              textAlign: 'left', background: 'var(--paper-2)',
              border: '1.5px solid var(--magenta)', boxShadow: '2px 2px 0 var(--magenta)',
              fontSize: '15px', color: 'var(--magenta)',
            }"
            @click="confirmDelete = true"
          >✕ Encerrar bolão</button>
        </div>
      </section>

    </div>
  </div>
</template>

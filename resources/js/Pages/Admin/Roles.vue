<template>
  <Head title="Admin · Roles" />

  <AppSidebar :auth="$page.props.auth">

  <div class="dash-root">
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="dash-main">

      <!-- ═══════════════ HEADER ═══════════════ -->
      <section class="admin-header">
        <div>
          <div class="card-eyebrow">Admin Panel</div>
          <h1 class="admin-title">Roles</h1>
        </div>
        <AdminNav current="roles" />
      </section>

      <!-- ═══════════════ FLASH MESSAGE ═══════════════ -->
      <transition name="fade">
        <div v-if="flashMessage" class="flash-banner" :class="flashType">
          {{ flashMessage }}
        </div>
      </transition>

      <!-- ═══════════════ ROLES CARDS ═══════════════ -->
      <section class="roles-grid">
        <div v-for="r in localRoles" :key="r.name" class="role-card" :class="{ 'role-card-locked': r.name === 'admin' }">
          <div class="role-card-header">
            <span class="role-badge" :class="roleClass(r.name)">{{ r.name }}</span>
            <span v-if="r.name === 'admin'" class="locked-pill">Locked</span>
          </div>

          <div class="role-field">
            <label class="role-label">Daily Limit</label>
            <input
              type="number" min="0" class="filter-input"
              v-model.number="r.daily_limit"
              :disabled="r.name === 'admin'"
            />
          </div>

          <div class="role-field">
            <label class="role-label">Max Video Seconds <span class="role-hint">(0 = unlimited)</span></label>
            <input
              type="number" min="0" class="filter-input"
              v-model.number="r.max_video_seconds"
              :disabled="r.name === 'admin'"
            />
          </div>

          <div class="role-toggles">
            <label class="toggle-row">
              <input type="checkbox" v-model="r.can_watermark" :disabled="r.name === 'admin'" />
              <span>Watermark</span>
            </label>
            <label class="toggle-row">
              <input type="checkbox" v-model="r.can_subtitle" :disabled="r.name === 'admin'" />
              <span>Subtitle</span>
            </label>
            <label class="toggle-row">
              <input type="checkbox" v-model="r.can_voiceover" :disabled="r.name === 'admin'" />
              <span>Voiceover</span>
            </label>
          </div>

          <button
            v-if="r.name !== 'admin'"
            class="btn-primary btn-block"
            :disabled="savingRole === r.name"
            @click="saveRole(r)"
          >
            {{ savingRole === r.name ? 'Saving…' : 'Save Changes' }}
          </button>
        </div>
      </section>

    </main>
  </div>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AdminNav from '@/Components/Admin/AdminNav.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  roles: Array,
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.success || page.props.flash?.error || '');
const flashType = computed(() => page.props.flash?.error ? 'flash-error' : 'flash-success');

// Local editable copy so unsaved edits don't get lost on flash re-render
const localRoles = ref(props.roles.map((r) => ({
  name: r.name,
  daily_limit: r.daily_limit,
  max_video_seconds: r.max_video_seconds,
  can_watermark: !!r.can_watermark,
  can_subtitle: !!r.can_subtitle,
  can_voiceover: !!r.can_voiceover,
})));

const ROLE_CLASS = {
  tester: 'role-tester', normal: 'role-normal', pro: 'role-pro', vip: 'role-vip', admin: 'role-admin',
};
const roleClass = (name) => ROLE_CLASS[name] || 'role-tester';

const savingRole = ref(null);

const saveRole = (r) => {
  savingRole.value = r.name;
  router.post(route('admin.roles.update', r.name), {
    daily_limit: r.daily_limit,
    max_video_seconds: r.max_video_seconds,
    can_watermark: r.can_watermark,
    can_subtitle: r.can_subtitle,
    can_voiceover: r.can_voiceover,
  }, {
    preserveScroll: true,
    onFinish: () => { savingRole.value = null; },
  });
};
</script>

<style scoped>
.dash-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  position: relative;
  overflow: hidden;
}

.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 560px; height: 560px; background: rgba(124,58,237,0.18); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6,182,212,0.14);  top: 400px; right: -160px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.08); bottom: -100px; left: 30%; }

.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 20px;
}

.card-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: #7C3AED; margin-bottom: 10px;
}

.admin-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.admin-title { font-size: 26px; font-weight: 800; letter-spacing: -0.4px; color: #F1F5F9; }

/* ─── Flash ─── */
.flash-banner { border-radius: 14px; padding: 12px 18px; font-size: 13.5px; font-weight: 600; }
.flash-success { background: rgba(52,211,153,0.12); color: #34D399; border: 1px solid rgba(52,211,153,0.25); }
.flash-error   { background: rgba(239,68,68,0.12); color: #F87171; border: 1px solid rgba(239,68,68,0.25); }

/* ─── Roles grid ─── */
.roles-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;
}

.role-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px; padding: 22px;
  display: flex; flex-direction: column; gap: 14px;
}
.role-card-locked { opacity: 0.65; }

.role-card-header { display: flex; align-items: center; justify-content: space-between; }
.locked-pill {
  font-size: 11px; font-weight: 700; color: #64748B;
  background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 100px;
}

.role-field { display: flex; flex-direction: column; gap: 6px; }
.role-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B; }
.role-hint { text-transform: none; font-weight: 500; color: #475569; letter-spacing: 0; }

.filter-input {
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; color: #F1F5F9; font-size: 13.5px; padding: 10px 14px;
  font-family: inherit; width: 100%;
}
.filter-input:disabled { opacity: 0.5; cursor: not-allowed; }
.filter-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }

.role-toggles { display: flex; flex-direction: column; gap: 8px; }
.toggle-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #CBD5E1; cursor: pointer; }
.toggle-row input[type="checkbox"] {
  width: 16px; height: 16px; accent-color: #7C3AED; cursor: pointer;
}
.toggle-row input:disabled { cursor: not-allowed; opacity: 0.5; }

.role-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 100px;
  border: 1px solid transparent; text-transform: capitalize;
}
.role-tester { background: rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); color: #CBD5E1; }
.role-normal { background: rgba(6,182,212,0.15); border-color: rgba(6,182,212,0.35); color: #67E8F9; }
.role-pro    { background: rgba(124,58,237,0.18); border-color: rgba(124,58,237,0.4); color: #C4B5FD; }
.role-vip    { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.4); color: #FCD34D; }
.role-admin  { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.4); color: #FCA5A5; }

.btn-primary {
  font-size: 13.5px; font-weight: 700; border-radius: 12px; padding: 10px 18px;
  cursor: pointer; font-family: inherit; border: none;
  background: #7C3AED; color: #fff; transition: background 0.2s, opacity 0.2s;
}
.btn-primary:hover { background: #6D28D9; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-block { width: 100%; margin-top: 4px; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 16px; }
}

.filter-input:focus-visible,
.btn-primary:focus-visible,
.toggle-row input:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}
</style>
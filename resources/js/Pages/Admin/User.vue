<template>
  <Head title="Admin · Users" />

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
          <h1 class="admin-title">Users</h1>
        </div>
        <AdminNav current="users" />
      </section>

      <!-- ═══════════════ FLASH MESSAGE ═══════════════ -->
      <transition name="fade">
        <div v-if="flashMessage" class="flash-banner" :class="flashType">
          {{ flashMessage }}
        </div>
      </transition>

      <!-- ═══════════════ USERS TABLE ═══════════════ -->
      <section class="posts-section">
        <div class="posts-header">
          <div>
            <div class="card-eyebrow">User Management</div>
            <h2 class="posts-title">All Users</h2>
          </div>

          <div class="posts-filters">
            <div class="search-wrap">
              <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
              </svg>
              <input
                v-model="search"
                type="text"
                placeholder="Search username or email..."
                class="filter-input search-input"
              />
            </div>

            <select v-model="roleFilter" class="filter-input select-input">
              <option value="">All Roles</option>
              <option v-for="r in roles" :key="r.name" :value="r.name">
                {{ r.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Desktop table -->
        <div class="posts-table-wrap">
          <table class="posts-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Recap Limit</th>
                <th>Today</th>
                <th>Expires</th>
                <th>Total Used</th>
                <th class="th-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in users.data" :key="u.id">
                <td class="td-title">
                  {{ u.username }}
                  <div class="td-sub">{{ u.email }}</div>
                </td>
                <td><span class="role-badge" :class="roleClass(u.role_name)">{{ u.role_name }}</span></td>
                <td>
                  <span class="status-pill" :class="u.is_active ? 'status-active' : 'status-inactive'">
                    <span class="status-dot"></span>
                    {{ u.is_active ? 'Active' : 'Disabled' }}
                  </span>
                </td>
                <td>{{ u.recap_limit > 0 ? u.recap_limit : '∞' }}</td>
                <td>{{ u.today_used }} / {{ u.daily_limit ?? '—' }}</td>
                <td class="td-date">{{ formatDate(u.plan_expires_at) }}</td>
                <td>{{ u.total_recap_used }}</td>
                <td class="td-actions">
                  <button class="action-link action-edit" @click="openRenew(u)">Renew</button>
                  <Link :href="route('admin.history', u.username)" class="action-link action-view">History</Link>
                  <button class="action-link action-edit" @click="openPassword(u)">Password</button>
                  <button
                    class="action-link"
                    :class="u.is_active ? 'action-delete' : 'action-view'"
                    @click="toggleActive(u)"
                  >
                    {{ u.is_active ? 'Disable' : 'Enable' }}
                  </button>
                  <button class="action-link action-delete" @click="confirmDelete(u)">Delete</button>
                </td>
              </tr>
              <tr v-if="users.data.length === 0">
                <td colspan="8" class="empty-row">No users match this filter.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile cards -->
        <div class="posts-cards">
          <div v-for="u in users.data" :key="u.id" class="post-card">
            <div class="post-card-body">
              <h3 class="post-card-title">{{ u.username }}</h3>
              <div class="td-sub">{{ u.email }}</div>
              <div class="post-card-meta">
                <span class="role-badge" :class="roleClass(u.role_name)">{{ u.role_name }}</span>
                <span class="status-pill" :class="u.is_active ? 'status-active' : 'status-inactive'">
                  <span class="status-dot"></span>
                  {{ u.is_active ? 'Active' : 'Disabled' }}
                </span>
              </div>
              <div class="post-card-meta">
                <span class="td-date">Limit: {{ u.recap_limit > 0 ? u.recap_limit : '∞' }}</span>
                <span class="td-date">Today: {{ u.today_used }}/{{ u.daily_limit ?? '—' }}</span>
              </div>
              <div class="td-date" style="margin-bottom: 10px;">Expires: {{ formatDate(u.plan_expires_at) }}</div>
              <div class="post-card-actions">
                <button class="action-link action-edit" @click="openRenew(u)">Renew</button>
                <Link :href="route('admin.history', u.username)" class="action-link action-view">History</Link>
                <button class="action-link action-edit" @click="openPassword(u)">Password</button>
              </div>
              <div class="post-card-actions" style="margin-top: 8px;">
                <button
                  class="action-link"
                  :class="u.is_active ? 'action-delete' : 'action-view'"
                  @click="toggleActive(u)"
                >
                  {{ u.is_active ? 'Disable' : 'Enable' }}
                </button>
                <button class="action-link action-delete" @click="confirmDelete(u)">Delete</button>
              </div>
            </div>
          </div>
          <p v-if="users.data.length === 0" class="empty-row empty-row-mobile">No users match this filter.</p>
        </div>

        <div class="pagination-wrap">
          <Pagination v-if="users.links" :links="users.links" />
        </div>
      </section>

    </main>

    <!-- ═══════════════ RENEW MODAL ═══════════════ -->
    <transition name="fade">
      <div v-if="renewTarget" class="modal-overlay" @click.self="closeRenew">
        <div class="modal-card">
          <h3 class="modal-title">Renew Plan</h3>

          <div class="modal-field">
            <label class="modal-label">Username</label>
            <input class="filter-input" :value="renewTarget.username" disabled />
          </div>

          <div class="modal-field">
            <label class="modal-label">Current Role</label>
            <span class="role-badge" :class="roleClass(renewTarget.role_name)">{{ renewTarget.role_name }}</span>
          </div>

          <div class="modal-field">
            <label class="modal-label">New Role</label>
            <select v-model="renewForm.new_role" class="filter-input select-input" style="width: 100%;">
              <option value="normal">normal</option>
              <option value="pro">pro</option>
              <option value="vip">vip</option>
            </select>
          </div>

          <div class="modal-field">
            <label class="modal-label">Days {{ renewForm.new_role === 'vip' ? '(0 = no expiry)' : '' }}</label>
            <input
              v-model.number="renewForm.days"
              type="number"
              min="0"
              class="filter-input"
              style="width: 100%;"
            />
          </div>

          <p class="modal-preview">
            <template v-if="selectedRoleDailyLimit">
              Will set: <strong>{{ renewPreviewLimit }} recaps</strong>
              ({{ selectedRoleDailyLimit }}x/day × {{ renewForm.days }} day{{ renewForm.days === 1 ? '' : 's' }})
              <template v-if="renewForm.days === 0"> — no expiry</template>
            </template>
          </p>

          <div class="modal-field">
            <label class="modal-label">Renewed By</label>
            <input class="filter-input" :value="adminUsername" disabled />
          </div>

          <div v-if="renewError" class="modal-error">{{ renewError }}</div>

          <div class="modal-actions">
            <button class="btn-secondary" @click="closeRenew">Cancel</button>
            <button class="btn-primary" :disabled="renewing" @click="submitRenew">
              {{ renewing ? 'Renewing…' : 'Renew' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ═══════════════ PASSWORD MODAL ═══════════════ -->
    <transition name="fade">
      <div v-if="passwordTarget" class="modal-overlay" @click.self="closePassword">
        <div class="modal-card">
          <h3 class="modal-title">Reset Password</h3>
          <p class="modal-sub">For user <strong>{{ passwordTarget.username }}</strong></p>

          <div class="modal-field">
            <label class="modal-label">New Password</label>
            <input v-model="passwordForm.password" type="password" class="filter-input" style="width: 100%;" />
          </div>
          <div class="modal-field">
            <label class="modal-label">Confirm Password</label>
            <input v-model="passwordForm.password_confirmation" type="password" class="filter-input" style="width: 100%;" />
          </div>

          <div v-if="passwordError" class="modal-error">{{ passwordError }}</div>

          <div class="modal-actions">
            <button class="btn-secondary" @click="closePassword">Cancel</button>
            <button class="btn-primary" :disabled="resettingPassword" @click="submitPassword">
              {{ resettingPassword ? 'Saving…' : 'Reset Password' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AdminNav from '@/Components/Admin/AdminNav.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object,
});

const page = usePage();
const adminUsername = computed(() => page.props.auth?.user?.username || '');

/* ───────── Filters ───────── */
const search = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role || '');

watch([search, roleFilter], debounce(function ([newSearch, newRole]) {
  router.get(route('admin.users'), {
    search: newSearch,
    role: newRole,
  }, {
    preserveState: true,
    replace: true,
  });
}, 300));

/* ───────── Flash messages ───────── */
const flashMessage = computed(() => page.props.flash?.success || page.props.flash?.error || '');
const flashType = computed(() => page.props.flash?.error ? 'flash-error' : 'flash-success');

/* ───────── Role display helpers ───────── */
const ROLE_CLASS = {
  tester: 'role-tester', normal: 'role-normal', pro: 'role-pro', vip: 'role-vip', admin: 'role-admin',
};
const roleClass = (name) => ROLE_CLASS[name] || 'role-tester';

const formatDate = (val) => {
  if (!val) return '—';
  const d = new Date(val);
  if (isNaN(d.getTime())) return '—';
  return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
};

/* ───────── Renew modal ───────── */
const renewTarget = ref(null);
const renewForm = ref({ new_role: 'normal', days: 7 });
const renewing = ref(false);
const renewError = ref('');

const openRenew = (u) => {
  renewTarget.value = u;
  renewForm.value = {
    new_role: ['normal', 'pro', 'vip'].includes(u.role_name) ? u.role_name : 'normal',
    days: 7,
  };
  renewError.value = '';
};
const closeRenew = () => {
  renewTarget.value = null;
};

const selectedRoleDailyLimit = computed(() => {
  const r = props.roles.find((r) => r.name === renewForm.value.new_role);
  return r?.daily_limit ?? null;
});

const renewPreviewLimit = computed(() => {
  if (!selectedRoleDailyLimit.value) return 0;
  return renewForm.value.days * selectedRoleDailyLimit.value;
});

const submitRenew = () => {
  if (!renewTarget.value) return;
  renewing.value = true;
  renewError.value = '';

  router.post(route('admin.users.renew', renewTarget.value.id), {
    new_role: renewForm.value.new_role,
    days: renewForm.value.days,
  }, {
    preserveScroll: true,
    onSuccess: () => { closeRenew(); },
    onError: (errors) => {
      renewError.value = Object.values(errors)[0] || 'Failed to renew plan.';
    },
    onFinish: () => { renewing.value = false; },
  });
};

/* ───────── Password modal ───────── */
const passwordTarget = ref(null);
const passwordForm = ref({ password: '', password_confirmation: '' });
const resettingPassword = ref(false);
const passwordError = ref('');

const openPassword = (u) => {
  passwordTarget.value = u;
  passwordForm.value = { password: '', password_confirmation: '' };
  passwordError.value = '';
};
const closePassword = () => {
  passwordTarget.value = null;
};

const submitPassword = () => {
  if (!passwordTarget.value) return;
  resettingPassword.value = true;
  passwordError.value = '';

  router.post(route('admin.users.password', passwordTarget.value.id), {
    password: passwordForm.value.password,
    password_confirmation: passwordForm.value.password_confirmation,
  }, {
    preserveScroll: true,
    onSuccess: () => { closePassword(); },
    onError: (errors) => {
      passwordError.value = Object.values(errors)[0] || 'Failed to reset password.';
    },
    onFinish: () => { resettingPassword.value = false; },
  });
};

/* ───────── Toggle / Delete ───────── */
const toggleActive = (u) => {
  router.post(route('admin.users.toggle', u.id), {}, { preserveScroll: true });
};

const confirmDelete = (u) => {
  if (confirm(`Delete user "${u.username}"? This also removes their usage history and cannot be undone.`)) {
    router.delete(route('admin.users.delete', u.id), { preserveScroll: true });
  }
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
  max-width: 1280px; margin: 0 auto;
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
.flash-banner {
  border-radius: 14px; padding: 12px 18px; font-size: 13.5px; font-weight: 600;
}
.flash-success { background: rgba(52,211,153,0.12); color: #34D399; border: 1px solid rgba(52,211,153,0.25); }
.flash-error   { background: rgba(239,68,68,0.12); color: #F87171; border: 1px solid rgba(239,68,68,0.25); }

/* ─── Posts section (reused pattern) ─── */
.posts-section {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 22px; padding: 28px;
}
.posts-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.posts-title { font-size: 22px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.4px; }
.posts-filters { display: flex; gap: 10px; flex-wrap: wrap; }
.filter-input {
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; color: #F1F5F9; font-size: 13.5px; padding: 10px 14px;
  font-family: inherit;
}
.filter-input:disabled { opacity: 0.6; }
.filter-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }
.search-wrap { position: relative; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #475569; }
.search-input { padding-left: 36px; min-width: 220px; }
.select-input { min-width: 150px; }

/* Desktop table */
.posts-table-wrap { overflow-x: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; }
.posts-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.posts-table thead { background: rgba(255,255,255,0.03); }
.posts-table th {
  text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.6px; color: #64748B; white-space: nowrap;
}
.th-right { text-align: right; }
.posts-table td { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
.posts-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.td-title { font-weight: 600; color: #E2E8F0; }
.td-sub { font-size: 12px; color: #64748B; margin-top: 2px; }
.td-date { color: #64748B; }
.td-actions { text-align: right; white-space: nowrap; }
.action-link {
  font-size: 12.5px; font-weight: 600; margin-left: 12px;
  text-decoration: none; background: none; border: none; cursor: pointer; font-family: inherit;
}
.action-view   { color: #34D399; }
.action-edit   { color: #67E8F9; }
.action-delete { color: #F87171; }
.action-link:hover { text-decoration: underline; }
.empty-row { text-align: center; padding: 40px 16px; color: #64748B; font-size: 14px; }

/* Mobile cards */
.posts-cards { display: none; flex-direction: column; gap: 14px; }
.post-card {
  display: flex; gap: 14px; background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 14px;
}
.post-card-body { flex: 1; min-width: 0; }
.post-card-title { font-size: 14px; font-weight: 700; color: #E2E8F0; margin-bottom: 4px; }
.post-card-meta { display: flex; align-items: center; gap: 10px; margin: 10px 0; flex-wrap: wrap; }
.post-card-actions { display: flex; gap: 14px; flex-wrap: wrap; }
.empty-row-mobile { text-align: center; padding: 30px 0; color: #64748B; font-size: 14px; }

.pagination-wrap { margin-top: 24px; }

/* ─── Role/status pills (shared) ─── */
.role-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 700; padding: 5px 12px; border-radius: 100px;
  border: 1px solid transparent; text-transform: capitalize;
}
.role-tester { background: rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); color: #CBD5E1; }
.role-normal { background: rgba(6,182,212,0.15); border-color: rgba(6,182,212,0.35); color: #67E8F9; }
.role-pro    { background: rgba(124,58,237,0.18); border-color: rgba(124,58,237,0.4); color: #C4B5FD; }
.role-vip    { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.4); color: #FCD34D; }
.role-admin  { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.4); color: #FCA5A5; }

.status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 100px; }
.status-active   { background: rgba(52,211,153,0.12); color: #34D399; }
.status-inactive { background: rgba(239,68,68,0.12); color: #F87171; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* ─── Modal ─── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 50;
  background: rgba(2,4,10,0.7); backdrop-filter: blur(6px);
  display: flex; align-items: center; justify-content: center; padding: 20px;
}
.modal-card {
  background: #0D1220; border: 1px solid rgba(255,255,255,0.1);
  border-radius: 22px; padding: 28px; width: 100%; max-width: 420px;
  display: flex; flex-direction: column; gap: 14px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.modal-title { font-size: 18px; font-weight: 800; color: #F1F5F9; }
.modal-sub { font-size: 13px; color: #94A3B8; margin-top: -8px; }
.modal-sub strong { color: #E2E8F0; }
.modal-field { display: flex; flex-direction: column; gap: 6px; }
.modal-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B; }
.modal-preview { font-size: 12.5px; color: #94A3B8; background: rgba(255,255,255,0.03); border-radius: 10px; padding: 10px 12px; }
.modal-preview strong { color: #C4B5FD; }
.modal-error { font-size: 12.5px; color: #F87171; background: rgba(239,68,68,0.1); border-radius: 10px; padding: 8px 12px; }
.modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 6px; }

.btn-secondary, .btn-primary {
  font-size: 13.5px; font-weight: 700; border-radius: 12px; padding: 10px 18px;
  cursor: pointer; font-family: inherit; border: 1px solid transparent; transition: opacity 0.2s, background 0.2s;
}
.btn-secondary { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: #94A3B8; }
.btn-secondary:hover { background: rgba(255,255,255,0.08); }
.btn-primary { background: #7C3AED; color: #fff; }
.btn-primary:hover { background: #6D28D9; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 16px; }
  .posts-section { padding: 20px; }
  .posts-table-wrap { display: none; }
  .posts-cards { display: flex; }
  .search-input { min-width: 0; flex: 1; }
  .posts-filters { width: 100%; }
  .search-wrap { flex: 1; }
}

/* ─── Accessibility ─── */
.action-link:focus-visible,
.filter-input:focus-visible,
.btn-primary:focus-visible,
.btn-secondary:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}
</style>
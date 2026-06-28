<template>
  <Head :title="`Admin · History · ${user.username}`" />

  <AppSidebar :auth="$page.props.auth">

  <div class="dash-root">
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="dash-main">

      <!-- ═══════════════ HEADER ═══════════════ -->
      <section class="admin-header">
        <div>
          <Link :href="route('admin.users')" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Back to Users
          </Link>
          <div class="card-eyebrow" style="margin-top: 14px;">Plan History</div>
          <h1 class="admin-title">{{ user.username }}</h1>
          <span class="role-badge" :class="roleClass(user.role_name)" style="margin-top: 8px;">
            {{ user.role_name }}
          </span>
        </div>
        <AdminNav current="users" />
      </section>

      <!-- ═══════════════ HISTORY TABLE ═══════════════ -->
      <section class="posts-section">
        <div class="posts-header">
          <div>
            <div class="card-eyebrow">Renewal Log</div>
            <h2 class="posts-title">All Plan Changes</h2>
          </div>
        </div>

        <!-- Desktop table -->
        <div class="posts-table-wrap">
          <table class="posts-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Old Role</th>
                <th>New Role</th>
                <th>Recap Limit Set</th>
                <th>Expires At</th>
                <th>Renewed By</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="h in history.data" :key="h.id">
                <td class="td-date">{{ formatDateTime(h.renewed_at) }}</td>
                <td><span class="role-badge" :class="roleClass(h.old_role)">{{ h.old_role }}</span></td>
                <td><span class="role-badge" :class="roleClass(h.new_role)">{{ h.new_role }}</span></td>
                <td>{{ h.recap_limit }}</td>
                <td class="td-date">{{ h.plan_expires_at ? formatDate(h.plan_expires_at) : 'No expiry' }}</td>
                <td>
                  <span class="renewed-by" :class="{ 'renewed-system': h.renewed_by === 'system_auto_rollback' }">
                    {{ h.renewed_by }}
                  </span>
                </td>
              </tr>
              <tr v-if="history.data.length === 0">
                <td colspan="6" class="empty-row">No plan history for this user yet.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile cards -->
        <div class="posts-cards">
          <div v-for="h in history.data" :key="h.id" class="post-card">
            <div class="post-card-body">
              <div class="post-card-meta">
                <span class="role-badge" :class="roleClass(h.old_role)">{{ h.old_role }}</span>
                <span>→</span>
                <span class="role-badge" :class="roleClass(h.new_role)">{{ h.new_role }}</span>
              </div>
              <div class="td-date">{{ formatDateTime(h.renewed_at) }}</div>
              <div class="td-date">Limit set: {{ h.recap_limit }}</div>
              <div class="td-date">Expires: {{ h.plan_expires_at ? formatDate(h.plan_expires_at) : 'No expiry' }}</div>
              <div class="td-date">
                By: <span class="renewed-by" :class="{ 'renewed-system': h.renewed_by === 'system_auto_rollback' }">
                  {{ h.renewed_by }}
                </span>
              </div>
            </div>
          </div>
          <p v-if="history.data.length === 0" class="empty-row empty-row-mobile">No plan history for this user yet.</p>
        </div>

        <div class="pagination-wrap">
          <Pagination v-if="history.links" :links="history.links" />
        </div>
      </section>

    </main>
  </div>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AdminNav from '@/Components/Admin/AdminNav.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
  user: Object,
  history: Object,
});

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

const formatDateTime = (val) => {
  if (!val) return '—';
  const d = new Date(val);
  if (isNaN(d.getTime())) return '—';
  return d.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
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

.back-link {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; font-weight: 600; color: #94A3B8; text-decoration: none;
}
.back-link:hover { color: #C4B5FD; }

/* ─── Section / table (shared pattern) ─── */
.posts-section { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 22px; padding: 28px; }
.posts-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.posts-title { font-size: 22px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.4px; }

.posts-table-wrap { overflow-x: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; }
.posts-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.posts-table thead { background: rgba(255,255,255,0.03); }
.posts-table th {
  text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.6px; color: #64748B; white-space: nowrap;
}
.posts-table td { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
.posts-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.td-date { color: #64748B; }
.empty-row { text-align: center; padding: 40px 16px; color: #64748B; font-size: 14px; }

.posts-cards { display: none; flex-direction: column; gap: 14px; }
.post-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 14px; }
.post-card-body { display: flex; flex-direction: column; gap: 6px; }
.post-card-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px; }
.empty-row-mobile { text-align: center; padding: 30px 0; color: #64748B; font-size: 14px; }

.pagination-wrap { margin-top: 24px; }

.renewed-by { font-weight: 600; color: #E2E8F0; }
.renewed-system { color: #FCD34D; font-style: italic; }

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

@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 16px; }
  .posts-section { padding: 20px; }
  .posts-table-wrap { display: none; }
  .posts-cards { display: flex; }
}

.back-link:focus-visible { outline: 2px solid #06B6D4; outline-offset: 2px; }
</style>
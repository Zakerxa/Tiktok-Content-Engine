<template>
  <Head title="Admin Dashboard" />

  <AppSidebar :auth="$page.props.auth">

  <div class="dash-root">
    <!-- Ambient brand orbs -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="dash-main">

      <!-- ═══════════════ HEADER ═══════════════ -->
      <section class="admin-header">
        <div>
          <div class="card-eyebrow">Admin Panel</div>
          <h1 class="admin-title">Dashboard</h1>
        </div>
        <AdminNav current="index" />
      </section>

      <!-- ═══════════════ STAT CARDS ═══════════════ -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon stat-icon-violet">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-body">
            <span class="stat-num">{{ stats.total_users }}</span>
            <span class="stat-label">Total Users</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon stat-icon-cyan">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 4L12 14.01l-3-3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="stat-body">
            <span class="stat-num">{{ stats.active_users }}</span>
            <span class="stat-label">Active Users</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon stat-icon-red">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path d="M4.93 4.93l14.14 14.14" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="stat-body">
            <span class="stat-num">{{ stats.disabled_users }}</span>
            <span class="stat-label">Disabled Users</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon stat-icon-gold">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
          </div>
          <div class="stat-body">
            <span class="stat-num">{{ stats.total_recaps_today }}</span>
            <span class="stat-label">Recaps Today</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon stat-icon-violet">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="8.5" cy="7" r="4"/>
              <path d="M20 8v6M23 11h-6" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="stat-body">
            <span class="stat-num">{{ stats.new_users_today }}</span>
            <span class="stat-label">New Users Today</span>
          </div>
        </div>
      </section>

      <!-- ═══════════════ USERS BY ROLE ═══════════════ -->
      <section class="posts-section">
        <div class="posts-header">
          <div>
            <div class="card-eyebrow">Breakdown</div>
            <h2 class="posts-title">Users by Role</h2>
          </div>
        </div>

        <div class="role-bars">
          <div v-for="role in roleBreakdown" :key="role.name" class="role-bar-row">
            <span class="role-bar-label">
              <span class="role-badge" :class="roleClass(role.name)">
                {{ roleLabel(role.name) }}
              </span>
            </span>
            <div class="role-bar-track">
              <div
                class="role-bar-fill"
                :class="roleClass(role.name)"
                :style="{ width: barWidth(role.count) + '%' }"
              ></div>
            </div>
            <span class="role-bar-count">{{ role.count }}</span>
          </div>

          <p v-if="roleBreakdown.length === 0" class="empty-row">
            No users found yet.
          </p>
        </div>
      </section>

    </main>
  </div>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AdminNav from '@/Components/Admin/AdminNav.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  stats: Object,
});

const ROLE_META = {
  tester: { label: 'Tester', cls: 'role-tester' },
  normal: { label: 'Normal', cls: 'role-normal' },
  pro:    { label: 'Pro',    cls: 'role-pro' },
  vip:    { label: 'VIP',    cls: 'role-vip' },
  admin:  { label: 'Admin',  cls: 'role-admin' },
};

const roleLabel = (name) => ROLE_META[name]?.label || name;
const roleClass = (name) => ROLE_META[name]?.cls || 'role-tester';

const roleBreakdown = computed(() => {
  const byRole = props.stats?.users_by_role || {};
  return Object.entries(byRole).map(([name, count]) => ({ name, count: Number(count) }));
});

const maxCount = computed(() => {
  return Math.max(1, ...roleBreakdown.value.map((r) => r.count));
});

const barWidth = (count) => Math.round((count / maxCount.value) * 100);
</script>

<style scoped>
.dash-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  position: relative;
  overflow: hidden;
}

/* ─── Ambient orbs ─── */
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 560px; height: 560px; background: rgba(124,58,237,0.18); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6,182,212,0.14);  top: 400px; right: -160px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.08); bottom: -100px; left: 30%; }

.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 28px;
}

.card-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: #7C3AED; margin-bottom: 10px;
}

/* ─── Admin header ─── */
.admin-header {
  display: flex; align-items: flex-end; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
}
.admin-title { font-size: 26px; font-weight: 800; letter-spacing: -0.4px; color: #F1F5F9; }

/* ─── Stat cards ─── */
.stats-grid {
  display: grid; grid-template-columns: repeat(5, 1fr); gap: 18px;
}
@media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

.stat-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 18px; padding: 20px;
  display: flex; align-items: center; gap: 14px;
}
.stat-icon {
  width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.stat-icon-violet { background: rgba(124,58,237,0.15); color: #C4B5FD; }
.stat-icon-cyan    { background: rgba(6,182,212,0.15);  color: #67E8F9; }
.stat-icon-red     { background: rgba(239,68,68,0.15);  color: #F87171; }
.stat-icon-gold    { background: rgba(245,158,11,0.15); color: #FCD34D; }
.stat-body { display: flex; flex-direction: column; min-width: 0; }
.stat-num { font-size: 24px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.5px; line-height: 1.1; }
.stat-label { font-size: 12px; color: #64748B; margin-top: 2px; }

/* ─── Role breakdown section ─── */
.posts-section {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 22px; padding: 28px;
}
.posts-header {
  display: flex; align-items: flex-end; justify-content: space-between;
  flex-wrap: wrap; gap: 16px; margin-bottom: 24px;
}
.posts-title { font-size: 22px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.4px; }

.role-bars { display: flex; flex-direction: column; gap: 16px; }
.role-bar-row { display: flex; align-items: center; gap: 16px; }
.role-bar-label { width: 110px; flex-shrink: 0; }
.role-bar-track {
  flex: 1; height: 10px; border-radius: 100px;
  background: rgba(255,255,255,0.06); overflow: hidden;
}
.role-bar-fill { height: 100%; border-radius: 100px; transition: width 0.6s ease; }
.role-bar-count { width: 40px; text-align: right; font-weight: 700; color: #E2E8F0; font-size: 14px; }

.role-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; font-weight: 700; padding: 6px 12px; border-radius: 100px;
  border: 1px solid transparent;
}
.role-tester { background: rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); color: #CBD5E1; }
.role-normal { background: rgba(6,182,212,0.15); border-color: rgba(6,182,212,0.35); color: #67E8F9; }
.role-pro    { background: rgba(124,58,237,0.18); border-color: rgba(124,58,237,0.4); color: #C4B5FD; }
.role-vip    { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.4); color: #FCD34D; }
.role-admin  { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.4); color: #FCA5A5; }

.role-bar-fill.role-tester { background: #94A3B8; }
.role-bar-fill.role-normal { background: #06B6D4; }
.role-bar-fill.role-pro    { background: #7C3AED; }
.role-bar-fill.role-vip    { background: #F59E0B; }
.role-bar-fill.role-admin  { background: #EF4444; }

.empty-row { text-align: center; padding: 30px 16px; color: #64748B; font-size: 14px; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 20px; }
  .posts-section { padding: 20px; }
  .role-bar-label { width: 90px; }
}

@media (prefers-reduced-motion: reduce) {
  .role-bar-fill { transition: none !important; }
}
</style>
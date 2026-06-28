<template>
  <Head title="Dashboard" />

  <AppSidebar :auth="$page.props.auth">

  <div class="dash-root">
    <!-- Ambient brand orbs -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="dash-main">

      <!-- ═══════════════ PROFILE HEADER ═══════════════ -->
      <section class="profile-strip">
        <div class="profile-left">
          <div class="avatar-wrap">
            <img v-if="user.avatar" :src="user.avatar" :alt="user.username || 'User'" class="avatar-img" />
            <span v-else class="avatar-fallback">{{ avatarInitial }}</span>
            <span class="avatar-ring"></span>
          </div>
          <div class="profile-info">
            <h1 class="profile-name">{{ user.username || 'No username set' }}</h1>
            <p class="profile-email">{{ user.email }}</p>
          </div>
        </div>

        <div class="profile-right">
          <span class="role-badge" :class="roleClass">
            <span class="role-icon">{{ roleMeta.icon }}</span>
            {{ roleMeta.label }}
          </span>
          <span class="status-pill" :class="user.is_active ? 'status-active' : 'status-inactive'">
            <span class="status-dot"></span>
            {{ user.is_active ? 'Active' : 'Suspended' }}
          </span>
          <Link href="/logout" method="post" as="button" class="logout-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16 17l5-5-5-5M21 12H9" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Logout
          </Link>
        </div>
      </section>

      <!-- ═══════════════ USAGE + PLAN ═══════════════ -->
      <section class="stats-grid">

        <!-- Usage ring gauge -->
        <div class="stat-card usage-card">
          <div class="card-eyebrow">Today's Usage</div>
          <div class="usage-body">
            <svg class="usage-ring" viewBox="0 0 140 140">
              <circle cx="70" cy="70" r="58" class="ring-track" />
              <circle
                cx="70" cy="70" r="58"
                class="ring-progress"
                :class="usageRingClass"
                :stroke-dasharray="ringCircumference"
                :stroke-dashoffset="ringOffset"
              />
            </svg>
            <div class="usage-center">
              <span class="usage-num">{{ usedToday }}</span>
              <span class="usage-slash">/ {{ recapLimitDisplay }}</span>
              <span class="usage-label">generations</span>
            </div>
          </div>
          <p class="usage-footnote" v-if="recapLimitNum > 0 && usedToday >= recapLimitNum">
            Limit reached — upgrade your plan for more generations.
          </p>
          <p class="usage-footnote" v-else-if="recapLimitNum === 0">
            No generation limit set on this account.
          </p>
        </div>

        <!-- Plan expiry / countdown -->
        <div class="stat-card plan-card">
          <div class="card-eyebrow">Plan Status</div>

          <div v-if="user.plan_expires_at" class="plan-body">
            <div class="countdown-row">
              <div class="countdown-block" v-for="seg in countdownSegments" :key="seg.label">
                <span class="countdown-num">{{ seg.value }}</span>
                <span class="countdown-label">{{ seg.label }}</span>
              </div>
            </div>
            <p class="plan-expiry-date">
              {{ isExpired ? 'Expired on' : 'Expires on' }}
              <strong>{{ formattedExpiry }}</strong>
            </p>
            <p v-if="isExpired" class="plan-warning">
              Your plan has expired. Renew to keep your current limits.
            </p>
          </div>

          <div v-else class="plan-body plan-body-free">
            <p class="plan-free-text">
              You're on the free <strong>{{ roleMeta.label }}</strong> plan — no expiry date.
            </p>
            <a href="/#pricing" class="plan-upgrade-link">View plans →</a>
          </div>
        </div>

      </section>

      <!-- ═══════════════ MY POSTS ═══════════════ -->
      <section class="posts-section">
        <div class="posts-header">
          <div>
            <div class="card-eyebrow">Content Library</div>
            <h2 class="posts-title">Your Posts</h2>
          </div>

          <div class="posts-filters">
            <div class="search-wrap">
              <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
              </svg>
              <input
                v-model="search"
                type="text"
                placeholder="Search by title..."
                class="filter-input search-input"
              />
            </div>

            <select v-model="topic" class="filter-input select-input">
              <option value="">All Categories</option>
              <option v-for="stat in stats" :key="stat.clean" :value="stat.clean">
                {{ stat.clean }} ({{ stat.total }})
              </option>
            </select>
          </div>
        </div>

        <!-- Desktop table -->
        <div class="posts-table-wrap">
          <table class="posts-table">
            <thead>
              <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Topic</th>
                <th>Date</th>
                <th class="th-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="post in posts.data" :key="post.id">
                <td>
                  <img v-if="post.image_path" :src="post.image_path" class="post-thumb" />
                  <div v-else class="post-thumb post-thumb-empty">No Img</div>
                </td>
                <td class="td-title">{{ post.title }}</td>
                <td><span class="topic-chip">{{ post.topic }}</span></td>
                <td class="td-date">{{ post.created_at }}</td>
                <td class="td-actions">
                  <a :href="route('posts.show', post.slug || post.id)" target="_blank" class="action-link action-view">View</a>
                  <Link :href="route('posts.edit', post.id)" class="action-link action-edit">Edit</Link>
                  <button @click="deletePost(post.id)" class="action-link action-delete">Delete</button>
                </td>
              </tr>
              <tr v-if="posts.data.length === 0">
                <td colspan="5" class="empty-row">
                  You haven't generated any posts yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile cards -->
        <div class="posts-cards">
          <div v-for="post in posts.data" :key="post.id" class="post-card">
            <img v-if="post.image_path" :src="post.image_path" class="post-card-img" />
            <div v-else class="post-card-img post-thumb-empty">No Img</div>
            <div class="post-card-body">
              <h3 class="post-card-title">{{ post.title }}</h3>
              <div class="post-card-meta">
                <span class="topic-chip">{{ post.topic }}</span>
                <span class="td-date">{{ post.created_at }}</span>
              </div>
              <div class="post-card-actions">
                <a :href="route('posts.show', post.slug || post.id)" target="_blank" class="action-link action-view">View</a>
                <Link :href="route('posts.edit', post.id)" class="action-link action-edit">Edit</Link>
                <button @click="deletePost(post.id)" class="action-link action-delete">Delete</button>
              </div>
            </div>
          </div>
          <p v-if="posts.data.length === 0" class="empty-row empty-row-mobile">
            You haven't generated any posts yet.
          </p>
        </div>

        <div class="pagination-wrap">
          <Pagination v-if="posts.links" :links="posts.links" />
        </div>
      </section>

    </main>
  </div>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
  posts: Object,
  stats: Array,
  filters: Object,
  user: Object,        // logged-in user's own profile/usage data
});

/* ───────── Filters (scoped to this user's own posts) ───────── */
const search = ref(props.filters?.search || '');
const topic = ref(props.filters?.topic || '');

watch([search, topic], debounce(function ([newSearch, newTopic]) {
  router.get(route('dashboard'), {
    search: newSearch,
    topic: newTopic,
  }, {
    preserveState: true,
    replace: true,
  });
}, 300));

const deletePost = (id) => {
  if (confirm('Are you sure you want to delete this post and its image?')) {
    router.delete(route('posts.destroy', id), {
      preserveScroll: true,
    });
  }
};

/* ───────── Profile / role ───────── */
const user = computed(() => props.user || {});

const avatarInitial = computed(() => {
  const name = user.value.username || user.value.email || '?';
  return name.charAt(0).toUpperCase();
});

const ROLE_META = {
  tester: { label: 'Tester', icon: '🧪', cls: 'role-tester' },
  normal: { label: 'Normal', icon: '⚡', cls: 'role-normal' },
  pro:    { label: 'Pro',    icon: '🔥', cls: 'role-pro' },
  vip:    { label: 'VIP',    icon: '👑', cls: 'role-vip' },
};

const roleMeta = computed(() => {
  const key = (user.value.role_name || 'tester').toLowerCase();
  return ROLE_META[key] || { label: user.value.role_name || 'Tester', icon: '🧪', cls: 'role-tester' };
});

const roleClass = computed(() => roleMeta.value.cls);

/* ───────── Usage ring ───────── */
const usedToday = computed(() => Number(user.value.total_recap_used || 0));
const recapLimitNum = computed(() => Number(user.value.recap_limit ?? 0));
const recapLimitDisplay = computed(() => recapLimitNum.value > 0 ? recapLimitNum.value : '∞');

const usagePercent = computed(() => {
  if (recapLimitNum.value <= 0) return 0;
  return Math.min(usedToday.value / recapLimitNum.value, 1);
});

const RADIUS = 58;
const ringCircumference = 2 * Math.PI * RADIUS;
const ringOffset = computed(() => ringCircumference - usagePercent.value * ringCircumference);

const usageRingClass = computed(() => {
  if (recapLimitNum.value > 0 && usedToday.value >= recapLimitNum.value) return 'ring-danger';
  if (usagePercent.value > 0.7) return 'ring-warning';
  return 'ring-ok';
});

/* ───────── Plan countdown ───────── */
const now = ref(new Date());
let tickHandle = null;

onMounted(() => {
  tickHandle = setInterval(() => { now.value = new Date(); }, 1000);
});
onUnmounted(() => {
  if (tickHandle) clearInterval(tickHandle);
});

const expiresAt = computed(() => {
  if (!user.value.plan_expires_at) return null;
  const d = new Date(user.value.plan_expires_at);
  return isNaN(d.getTime()) ? null : d;
});

const isExpired = computed(() => {
  if (!expiresAt.value) return false;
  return expiresAt.value.getTime() <= now.value.getTime();
});

const countdownSegments = computed(() => {
  if (!expiresAt.value) return [];
  let diff = expiresAt.value.getTime() - now.value.getTime();
  if (diff < 0) diff = 0;

  const totalSeconds = Math.floor(diff / 1000);
  const days = Math.floor(totalSeconds / 86400);
  const hours = Math.floor((totalSeconds % 86400) / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  return [
    { value: String(days).padStart(2, '0'), label: 'days' },
    { value: String(hours).padStart(2, '0'), label: 'hrs' },
    { value: String(minutes).padStart(2, '0'), label: 'min' },
    { value: String(seconds).padStart(2, '0'), label: 'sec' },
  ];
});

const formattedExpiry = computed(() => {
  if (!expiresAt.value) return '';
  return expiresAt.value.toLocaleString(undefined, {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
});
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

/* ─── Shared card chrome ─── */
.card-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: #7C3AED; margin-bottom: 10px;
}

/* ─── Profile strip ─── */
.profile-strip {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 20px;
  background: rgba(255,255,255,0.035);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px; padding: 28px 32px;
  backdrop-filter: blur(18px);
}
.profile-left { display: flex; align-items: center; gap: 18px; }
.avatar-wrap { position: relative; width: 64px; height: 64px; flex-shrink: 0; }
.avatar-img, .avatar-fallback {
  width: 64px; height: 64px; border-radius: 18px; object-fit: cover;
}
.avatar-fallback {
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  font-size: 24px; font-weight: 800; color: #fff;
}
.avatar-ring {
  position: absolute; inset: -3px; border-radius: 21px;
  border: 2px solid rgba(124,58,237,0.4); pointer-events: none;
}
.profile-name { font-size: 20px; font-weight: 800; letter-spacing: -0.3px; color: #F1F5F9; }
.profile-email { font-size: 13px; color: #64748B; margin-top: 2px; }

.profile-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.role-badge {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 700; padding: 7px 14px; border-radius: 100px;
  border: 1px solid transparent;
}
.role-icon { font-size: 14px; }
.role-tester { background: rgba(148,163,184,0.15); border-color: rgba(148,163,184,0.35); color: #CBD5E1; }
.role-normal { background: rgba(6,182,212,0.15); border-color: rgba(6,182,212,0.35); color: #67E8F9; }
.role-pro    { background: rgba(124,58,237,0.18); border-color: rgba(124,58,237,0.4); color: #C4B5FD; }
.role-vip    { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.4); color: #FCD34D; }

.status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 100px; }
.status-active   { background: rgba(52,211,153,0.12); color: #34D399; }
.status-inactive { background: rgba(239,68,68,0.12); color: #F87171; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.logout-btn {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; font-weight: 600; color: #94A3B8;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  padding: 7px 14px; border-radius: 100px; cursor: pointer; font-family: inherit;
  transition: background 0.2s, border-color 0.2s, color 0.2s;
}
.logout-btn:hover { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #F87171; }

/* ─── Stats grid ─── */
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 22px; padding: 28px;
}

/* Usage ring */
.usage-body { display: flex; align-items: center; justify-content: center; position: relative; margin: 8px 0; }
.usage-ring { width: 160px; height: 160px; transform: rotate(-90deg); }
.ring-track { fill: none; stroke: rgba(255,255,255,0.07); stroke-width: 10; }
.ring-progress { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset 0.6s ease; }
.ring-ok       { stroke: #06B6D4; }
.ring-warning  { stroke: #F59E0B; }
.ring-danger   { stroke: #F87171; }
.usage-center {
  position: absolute; inset: 0; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
}
.usage-num { font-size: 32px; font-weight: 800; color: #F1F5F9; letter-spacing: -1px; }
.usage-slash { font-size: 14px; color: #64748B; margin-top: -2px; }
.usage-label { font-size: 11px; color: #475569; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
.usage-footnote { text-align: center; font-size: 12.5px; color: #94A3B8; margin-top: 14px; }

/* Plan card */
.plan-body { display: flex; flex-direction: column; align-items: center; gap: 14px; padding-top: 8px; }
.countdown-row { display: flex; gap: 10px; }
.countdown-block {
  display: flex; flex-direction: column; align-items: center; gap: 4px;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px; padding: 10px 14px; min-width: 56px;
}
.countdown-num { font-size: 22px; font-weight: 800; color: #F1F5F9; font-family: monospace; letter-spacing: -0.5px; }
.countdown-label { font-size: 10px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; }
.plan-expiry-date { font-size: 13px; color: #94A3B8; }
.plan-expiry-date strong { color: #E2E8F0; }
.plan-warning { font-size: 12.5px; color: #F87171; text-align: center; }
.plan-body-free { align-items: center; text-align: center; padding: 20px 0; }
.plan-free-text { font-size: 14px; color: #94A3B8; }
.plan-free-text strong { color: #E2E8F0; }
.plan-upgrade-link { font-size: 13px; font-weight: 700; color: #A78BFA; text-decoration: none; }
.plan-upgrade-link:hover { text-decoration: underline; }

/* ─── Posts section ─── */
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
.posts-filters { display: flex; gap: 10px; flex-wrap: wrap; }
.filter-input {
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; color: #F1F5F9; font-size: 13.5px; padding: 10px 14px;
}
.filter-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }
.search-wrap { position: relative; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #475569; }
.search-input { padding-left: 36px; min-width: 220px; }
.select-input { min-width: 170px; }

/* Desktop table */
.posts-table-wrap { overflow-x: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; }
.posts-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.posts-table thead { background: rgba(255,255,255,0.03); }
.posts-table th {
  text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.6px; color: #64748B;
}
.th-right { text-align: right; }
.posts-table td { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
.posts-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.post-thumb { width: 64px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08); }
.post-thumb-empty { display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.04); color: #475569; font-size: 11px; }
.td-title { font-weight: 600; color: #E2E8F0; }
.td-date { color: #64748B; }
.topic-chip {
  display: inline-block; font-size: 11.5px; font-weight: 600;
  background: rgba(124,58,237,0.15); color: #C4B5FD;
  padding: 4px 10px; border-radius: 100px;
}
.td-actions { text-align: right; white-space: nowrap; }
.action-link {
  font-size: 12.5px; font-weight: 600; margin-left: 14px;
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
.post-card-img { width: 80px; height: 64px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
.post-card-body { flex: 1; min-width: 0; }
.post-card-title { font-size: 14px; font-weight: 700; color: #E2E8F0; margin-bottom: 8px; }
.post-card-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
.post-card-actions { display: flex; gap: 14px; }
.empty-row-mobile { text-align: center; padding: 30px 0; color: #64748B; font-size: 14px; }

.pagination-wrap { margin-top: 24px; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 20px; }
  .profile-strip { padding: 22px 20px; flex-direction: column; align-items: flex-start; }
  .stat-card { padding: 22px; }
  .posts-section { padding: 20px; }
  .posts-table-wrap { display: none; }
  .posts-cards { display: flex; }
  .search-input { min-width: 0; flex: 1; }
  .posts-filters { width: 100%; }
  .search-wrap { flex: 1; }
}

@media (max-width: 420px) {
  .countdown-row { gap: 6px; }
  .countdown-block { min-width: 46px; padding: 8px 10px; }
  .countdown-num { font-size: 18px; }
}

/* ─── Accessibility ─── */
.action-link:focus-visible,
.filter-input:focus-visible,
.plan-upgrade-link:focus-visible,
.logout-btn:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .ring-progress { transition: none !important; }
}
</style>
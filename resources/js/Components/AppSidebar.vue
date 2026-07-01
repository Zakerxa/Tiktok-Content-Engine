<template>
  <!-- Sidebar overlay (mobile) -->
  <div
    v-if="sidebarOpen"
    class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm lg:hidden"
    @click="sidebarOpen = false"
  />

  <!-- ═══ SIDEBAR ═══ -->
  <aside
    :class="[
      'fixed top-0 left-0 z-40 h-full flex flex-col',
      'transition-transform duration-300 ease-in-out',
      sidebarOpen ? 'translate-x-0' : '-translate-x-full',
      'lg:translate-x-0',
    ]"
    style="width:240px; background: rgba(8,11,20,0.97); border-right: 1px solid rgba(255,255,255,0.07); backdrop-filter: blur(20px);"
  >
    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 pt-6 pb-5" style="border-bottom:1px solid rgba(255,255,255,0.06);">
      <a href="/" class="flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center">
      <img src="/favicon.png" alt="icon" srcset="">
      </a>
      <span style="font-size:20px;font-weight:800;letter-spacing:1px;color:#F1F5F9;position: relative;left: -10px;">Z.<span class="pc">A</span>.K.<span class="pc">E</span>.R.<span class="pc">X</span><span>.A</span></span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto flex flex-col gap-1">

      <!-- Main nav items -->
      <NavItem :href="route('dashboard')" icon="grid" label="Dashboard" :active="isRoute('dashboard')" />
      <NavItem :href="route('recap.dashboardrecap')" icon="zap" label="Recap Studio" :active="isRoute('recap.*')" />
      <NavItem :href="route('blogs.dashboardshow')" icon="tiktok" label="TikTok Post" :active="isRoute('tiktok.*')" />
      <NavItem :href="route('profile.edit')" icon="user" label="Profile" :active="isRoute('profile.edit')" />
      <!-- <NavItem :href="route('messages.index')" icon="message" label="Message Box" :active="isRoute('messages.*')" /> -->

      <!-- Admin section -->
      <template v-if="isAdmin">
        <div class="px-3 pt-5 pb-2">
          <span style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#475569;">Admin</span>
        </div>
        <NavItem :href="route('admin.index')" icon="gauge" label="Admin Panel" :active="isRoute('admin.*')" accent />
        <NavItem :href="route('admin.users')" icon="users" label="Manage Users" :active="isRoute('admin.users')" accent />
        <NavItem :href="route('admin.roles')" icon="shield" label="Roles" :active="isRoute('admin.roles')" accent />
        <NavItem :href="route('admin.servers')" icon="server" label="Servers" :active="isRoute('admin.servers')" accent />
      </template>
    </nav>

    <!-- User mini-profile at bottom -->
    <div class="px-4 py-4" style="border-top:1px solid rgba(255,255,255,0.06);">
      <div class="flex items-center gap-3">
        <div class="relative flex-shrink-0">
          <img
            v-if="auth.user.avatar"
            :src="auth.user.avatar"
            class="w-9 h-9 rounded-xl object-cover"
            style="border:1px solid rgba(255,255,255,0.1);"
          />
          <div
            v-else
            class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold"
            style="background:linear-gradient(135deg,#7C3AED,#06B6D4);color:#fff;"
          >
            {{ avatarInitial }}
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-700 truncate" style="color:#E2E8F0;font-weight:700;">{{ auth.user.username }}</p>
          <p class="text-xs truncate" style="color:#475569;">{{ roleMeta.icon }} {{ roleMeta.label }}</p>
        </div>
        <Link
          href="/logout"
          method="post"
          as="button"
          class="flex-shrink-0 p-2 rounded-lg transition-colors"
          style="color:#64748B;"
          title="Logout"
          @mouseenter="e => e.currentTarget.style.color='#F87171'"
          @mouseleave="e => e.currentTarget.style.color='#64748B'"
        >
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </Link>
      </div>
    </div>
  </aside>

  <!-- ═══ TOP BAR (mobile only) ═══ -->
  <header
    class="lg:hidden fixed top-0 left-0 right-0 z-20 flex items-center justify-between px-4"
    style="height:60px;background:rgba(8,11,20,0.96);border-bottom:1px solid rgba(255,255,255,0.07);backdrop-filter:blur(20px);"
  >
    <!-- Hamburger -->
    <button
      @click="sidebarOpen = !sidebarOpen"
      class="w-9 h-9 flex flex-col justify-center items-center gap-1.5 rounded-xl"
      style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);"
    >
      <span
        v-for="i in 3" :key="i"
        class="block rounded-full transition-all duration-200"
        style="width:18px;height:2px;background:#94A3B8;"
      />
    </button>

    <!-- Logo center -->
    <a href="/">
      <span style="font-size:20px;font-weight:800;letter-spacing:1px;color:#F1F5F9;position: relative;left: -10px;">Z.<span class="pc">A</span>.K.<span class="pc">E</span>.R.<span class="pc">X</span><span>.A</span></span>
    </a>

    <!-- Avatar -->
    <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0" style="border:1px solid rgba(255,255,255,0.1);">
      <img v-if="auth.user.avatar" :src="auth.user.avatar" class="w-full h-full object-cover" />
      <div
        v-else
        class="w-full h-full flex items-center justify-center text-sm font-bold"
        style="background:linear-gradient(135deg,#7C3AED,#06B6D4);color:#fff;"
      >{{ avatarInitial }}</div>
    </div>
  </header>

  <!-- ═══ PAGE CONTENT SLOT ═══ -->
  <div
    class="min-h-screen transition-all duration-300"
    style="background:#080B14;"
    :style="{ paddingLeft: '0', '--sidebar-w': '240px' }"
  >
    <div class="lg:pl-[240px]">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavItem from '@/Components/NavItem.vue';

const props = defineProps({
  auth: { type: Object, required: true },
});

const sidebarOpen = ref(false);
const page = usePage();

const isAdmin = computed(() => props.auth?.user?.role_name === 'admin');

const isRoute = (pattern) => {
  try {
    // Inertia current route name
    const current = page.props?.ziggy?.location || window.location.pathname;
    // Try ziggy route().current() if available
    if (typeof route === 'function' && route().current) {
      return route().current(pattern);
    }
    return false;
  } catch {
    return false;
  }
};

const ROLE_META = {
  tester: { label: 'Tester', icon: '🧪' },
  normal: { label: 'Normal', icon: '⚡' },
  pro:    { label: 'Pro',    icon: '🔥' },
  vip:    { label: 'VIP',   icon: '👑' },
  admin:  { label: 'Admin', icon: '🛡️' },
};

const roleMeta = computed(() => {
  const key = (props.auth?.user?.role_name || 'tester').toLowerCase();
  return ROLE_META[key] || { label: key, icon: '🧪' };
});

const avatarInitial = computed(() => {
  const name = props.auth?.user?.username || props.auth?.user?.email || '?';
  return name.charAt(0).toUpperCase();
});
</script>

<style>
.pc{
    color:#7C3AED;
}
.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 28px;
}

.dash-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  position: relative;
  overflow: hidden;
}

/* ─── Ambient orbs ─── */
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 560px; height: 560px; background: rgba(63, 31, 117, 0.18); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6,182,212,0.14);  top: 400px; right: -160px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.08); bottom: -100px; left: 30%; }

.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 28px;
}


</style>
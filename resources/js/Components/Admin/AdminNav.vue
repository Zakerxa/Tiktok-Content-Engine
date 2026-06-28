<template>
  <nav class="admin-nav">
    <Link :href="route('admin.index')" class="nav-link" :class="{ 'nav-active': current === 'index' }">
      Dashboard
    </Link>
    <Link :href="route('admin.users')" class="nav-link" :class="{ 'nav-active': current === 'users' }">
      Users
    </Link>
    <Link :href="route('admin.roles')" class="nav-link" :class="{ 'nav-active': current === 'roles' }">
      Roles
    </Link>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  current: {
    type: String,
    default: '',
  },
});
</script>

<style scoped>
.admin-nav {
  display: flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 100px; padding: 5px;
}
.nav-link {
  font-size: 13px; font-weight: 600; color: #94A3B8;
  padding: 8px 16px; border-radius: 100px;
  text-decoration: none; transition: background 0.2s, color 0.2s;
}
.nav-link:hover { color: #E2E8F0; background: rgba(255,255,255,0.04); }
.nav-active { background: rgba(124,58,237,0.18); color: #C4B5FD; }

@media (max-width: 480px) {
  .admin-nav { width: 100%; }
  .nav-link { flex: 1; text-align: center; padding: 8px 10px; }
}
</style>
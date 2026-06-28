<template>
  <Link
    :href="href"
    :class="[
      'nav-item',
      active ? 'nav-active' : 'nav-idle',
      accent && active ? 'nav-accent-active' : '',
      accent && !active ? 'nav-accent-idle' : '',
    ]"
  >
    <!-- Icon -->
    <span class="nav-icon">
      <component :is="iconComponent" />
    </span>
    <span class="nav-label">{{ label }}</span>

    <!-- Active indicator bar -->
    <span v-if="active" class="nav-dot" />
  </Link>
</template>

<script setup>
import { computed, h } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  href:   { type: String, required: true },
  label:  { type: String, required: true },
  icon:   { type: String, required: true },
  active: { type: Boolean, default: false },
  accent: { type: Boolean, default: false }, // for admin items (violet tint)
});

/* ── SVG icon map ─────────────────────────────────── */
const ICONS = {
  grid: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('rect', { x:3, y:3, width:7, height:7 }),
    h('rect', { x:14, y:3, width:7, height:7 }),
    h('rect', { x:3, y:14, width:7, height:7 }),
    h('rect', { x:14, y:14, width:7, height:7 }),
  ]),
  user: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('path', { d:'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2' }),
    h('circle', { cx:12, cy:7, r:4 }),
  ]),
  zap: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('polygon', { points:'13 2 3 14 12 14 11 22 21 10 12 10 13 2' }),
  ]),
  tiktok: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'currentColor' }, [
    h('path', { d:'M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.89a8.18 8.18 0 004.78 1.54V7a4.84 4.84 0 01-1.01-.31z' }),
  ]),
  message: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('path', { d:'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z' }),
  ]),
  gauge: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('path', { d:'M12 2a10 10 0 00-6.88 17.22M12 2a10 10 0 016.88 17.22M12 12l3.5-3.5' }),
    h('circle', { cx:12, cy:12, r:1 }),
  ]),
  users: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('path', { d:'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2' }),
    h('circle', { cx:9, cy:7, r:4 }),
    h('path', { d:'M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75' }),
  ]),
  shield: () => h('svg', { width:16, height:16, viewBox:'0 0 24 24', fill:'none', stroke:'currentColor', 'stroke-width':'2', 'stroke-linecap':'round', 'stroke-linejoin':'round' }, [
    h('path', { d:'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z' }),
  ]),
};

const iconComponent = computed(() => ICONS[props.icon] || ICONS.grid);
</script>

<style scoped>
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 12px;
  font-size: 13.5px;
  font-weight: 600;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
  position: relative;
  cursor: pointer;
}

/* Idle state */
.nav-idle {
  color: #64748B;
}
.nav-idle:hover {
  background: rgba(255,255,255,0.05);
  color: #CBD5E1;
}

/* Active state */
.nav-active {
  background: rgba(124,58,237,0.15);
  color: #C4B5FD;
  border: 1px solid rgba(124,58,237,0.25);
}

/* Admin accent — idle */
.nav-accent-idle {
  color: #7C3AED;
}
.nav-accent-idle:hover {
  background: rgba(124,58,237,0.08);
  color: #A78BFA;
}

/* Admin accent — active */
.nav-accent-active {
  background: rgba(124,58,237,0.2);
  color: #A78BFA;
  border-color: rgba(124,58,237,0.4);
}

.nav-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
}

.nav-label { flex: 1; }

.nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #7C3AED;
  flex-shrink: 0;
}
</style>
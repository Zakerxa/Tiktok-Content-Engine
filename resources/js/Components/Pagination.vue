<template>
  <div v-if="links.length > 3" class="pagination-wrap">

    <p class="pagination-summary">
      Showing <strong>{{ from }}</strong>–<strong>{{ to }}</strong> of <strong>{{ total }}</strong>
    </p>

    <nav class="pagination-nav" aria-label="Pagination">
      <Link
        :href="prevUrl || '#'"
        :class="['page-arrow', { 'page-disabled': !prevUrl }]"
        aria-label="Previous page"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
          <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </Link>

      <Link
        v-for="(item, key) in visibleLinks"
        :key="key"
        :href="item.url || '#'"
        :class="[
          'page-num',
          { 'page-active': item.active },
          { 'page-disabled': !item.url || item.isEllipsis },
        ]"
      >
        {{ item.isEllipsis ? '···' : item.label }}
      </Link>

      <Link
        :href="nextUrl || '#'"
        :class="['page-arrow', { 'page-disabled': !nextUrl }]"
        aria-label="Next page"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
          <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </Link>
    </nav>

  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  links: Array,
  from: Number,
  to: Number,
  total: Number,
});

// links[0] = "Previous", links[last] = "Next" (Laravel default), middle = page numbers
const numberLinks = computed(() => props.links.slice(1, -1));
const prevUrl = computed(() => props.links[0]?.url);
const nextUrl = computed(() => props.links[props.links.length - 1]?.url);

// Truncate long page lists (e.g. 1 2 3 ... 8 9) so it stays compact on mobile too
const visibleLinks = computed(() => {
  const all = numberLinks.value;
  const total = all.length;
  if (total <= 7) return all;

  const activeIndex = all.findIndex((l) => l.active);
  const result = [];

  const pushUnique = (item) => {
    if (!result.includes(item)) result.push(item);
  };

  pushUnique(all[0]);

  if (activeIndex > 2) {
    result.push({ isEllipsis: true, label: '...' });
  }

  const start = Math.max(1, activeIndex - 1);
  const end = Math.min(total - 2, activeIndex + 1);
  for (let i = start; i <= end; i++) {
    pushUnique(all[i]);
  }

  if (activeIndex < total - 3) {
    result.push({ isEllipsis: true, label: '...' });
  }

  pushUnique(all[total - 1]);

  return result;
});
</script>

<style scoped>
.pagination-wrap {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 14px;
  padding-top: 8px;
}

.pagination-summary {
  font-size: 12.5px; color: #64748B;
}
.pagination-summary strong { color: #CBD5E1; font-weight: 600; }

.pagination-nav {
  display: flex; align-items: center; gap: 6px;
  margin-left: auto;
}

.page-arrow, .page-num {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 34px; height: 34px; padding: 0 8px;
  border-radius: 10px; font-size: 13px; font-weight: 600;
  color: #94A3B8; background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  text-decoration: none; transition: background 0.2s, border-color 0.2s, color 0.2s;
}

.page-arrow:hover:not(.page-disabled),
.page-num:hover:not(.page-disabled):not(.page-active) {
  background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.18); color: #F1F5F9;
}

.page-active {
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  border-color: transparent; color: #fff;
}

.page-disabled {
  opacity: 0.35; pointer-events: none;
}

/* ─── Accessibility ─── */
.page-arrow:focus-visible, .page-num:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .page-arrow, .page-num { transition: none !important; }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .pagination-wrap { justify-content: center; }
  .pagination-summary { display: none; }
  .pagination-nav { margin-left: 0; }
  .page-arrow, .page-num { min-width: 30px; height: 30px; font-size: 12.5px; }
}
</style>
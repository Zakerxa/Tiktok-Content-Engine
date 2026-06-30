<template>
  <Head title="Content Hub" />

  <div class="index-root">
    <AppNavbar :auth="$page.props.auth" />

    <!-- Ambient brand orbs -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="index-main">

      <!-- ═══════════ Hero / search ═══════════ -->
      <section class="hero-block">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          AI-Generated TikTok Scripts
        </div>
        <h1 class="hero-title">
          <span class="gradient-text">TikTok</span> Content <span class="gradient-text">Hub</span>
        </h1>
        <p class="hero-sub">
          Instant video production scripts, tactical hooks, and image-prompt blueprints — ready to post.
        </p>

        <div class="search-wrap">
          <svg class="search-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search blogs by title..."
            class="search-input"
          />
          <button v-if="searchQuery" @click="searchQuery = ''" class="search-clear">
            Clear
          </button>
        </div>
      </section>

      <!-- ═══════════ Topic pills (horizontal scroll) ═══════════ -->
      <section class="topics-block">
        <div class="topics-header">
          <span class="card-eyebrow">Topics</span>
          <Link v-if="currentTopic" :href="route('blogs.index')" class="reset-link">
            Reset Filter ✕
          </Link>
        </div>

        <div class="topics-scroll">
          <Link
            :href="route('blogs.index')"
            class="topic-pill"
            :class="{ 'topic-pill-active': !currentTopic }"
          >
            <span class="pill-label">All Posts</span>
          </Link>

          <Link
            v-for="stat in stats"
            :key="stat.clean"
            :href="route('topics.show', stat.clean)"
            class="topic-pill"
            :class="{ 'topic-pill-active': currentTopic === stat.clean }"
          >
            <span class="pill-label">{{ stat.clean }}</span>
            <span class="pill-count">{{ stat.total }}</span>
          </Link>
        </div>
      </section>

      <!-- ═══════════ Post grid ═══════════ -->
      <section v-if="filteredPosts.length > 0" class="posts-grid">
        <article v-for="post in filteredPosts" :key="post.id" class="post-card">

          <Link :href="route('posts.show', post.slug)" class="cover-link">
            <img v-if="post.image_path" :src="post.image_path" :alt="post.title" class="cover-img" />
            <div v-else class="cover-placeholder">
              <span class="placeholder-topic">{{ post.topic }}</span>
              <h3 class="placeholder-title">{{ post.cover_title_burmese }}</h3>
            </div>
            <span class="cover-fade"></span>
          </Link>

          <div class="post-body">
            <span class="post-topic">{{ post.topic }}</span>

            <Link :href="route('posts.show', post.slug)" class="post-title-link">
              <h2 class="post-title">{{ post.title }}</h2>
              <p class="post-excerpt">{{ post.content }}</p>
            </Link>

            <div class="post-footer">
              <span class="post-date">📅 {{ post.created_at }}</span>
              <span class="post-model">{{ post.model_used }}</span>
            </div>
          </div>
        </article>
      </section>

      <div v-else class="empty-state">
        <p>No blog titles match "{{ searchQuery }}".</p>
      </div>

      <Pagination
        v-if="posts.links && posts.total > 0"
        :links="posts.links"
        :from="posts.from"
        :to="posts.to"
        :total="posts.total"
        class="pagination-block"
      />

    </main>

    <AppFooter :stats="stats" />
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';

const props = defineProps({
  posts: Object,
  stats: Array,
  currentTopic: String,
});

const searchQuery = ref('');

const filteredPosts = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.posts.data;
  }
  const q = searchQuery.value.toLowerCase();
  return props.posts.data.filter(post =>
    post.title.toLowerCase().includes(q) ||
    (post.cover_title_burmese || '').toLowerCase().includes(q)
  );
});
</script>

<style scoped>
.index-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* ─── Ambient orbs ─── */
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 580px; height: 580px; background: rgba(124,58,237,0.18); top: -140px; left: -180px; }
.orb-cyan   { width: 480px; height: 480px; background: rgba(6,182,212,0.14);  top: 600px; right: -180px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.08); bottom: 200px; left: 35%; }

.index-main {
  position: relative; z-index: 5;
  max-width: 1180px; margin: 0 auto;
  padding: 100px 24px 80px;
}

.card-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: #7C3AED;
}

/* ─── Hero ─── */
.hero-block { text-align: center; max-width: 640px; margin: 0 auto 48px; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.35);
  color: #A78BFA; font-size: 12.5px; font-weight: 500;
  padding: 6px 16px; border-radius: 100px; margin-bottom: 22px;
}
.badge-dot { width: 6px; height: 6px; background: #7C3AED; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }

.hero-title { font-size: clamp(32px, 5vw, 48px); font-weight: 900; letter-spacing: -1.5px; margin-bottom: 14px; }
.gradient-text {
  background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 60%, #F59E0B 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.hero-sub { font-size: 15.5px; color: #94A3B8; line-height: 1.7; margin-bottom: 30px; }

.search-wrap { position: relative; max-width: 440px; margin: 0 auto; }
.search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #475569; }
.search-input {
  width: 100%; padding: 14px 80px 14px 44px; border-radius: 16px;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  color: #F1F5F9; font-size: 14px;
}
.search-input::placeholder { color: #475569; }
.search-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 4px rgba(124,58,237,0.15); }
.search-clear {
  position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
  font-size: 11.5px; font-weight: 700; color: #64748B; background: none; border: none; cursor: pointer;
}
.search-clear:hover { color: #F1F5F9; }

/* ─── Topics ─── */
.topics-block { margin-bottom: 36px; }
.topics-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.reset-link { font-size: 12px; font-weight: 600; color: #A78BFA; text-decoration: none; }
.reset-link:hover { text-decoration: underline; }

.topics-scroll {
  display: flex; gap: 10px; overflow-x: auto; padding-bottom: 6px;
  scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent;
}
.topics-scroll::-webkit-scrollbar { height: 5px; }
.topics-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }

.topic-pill {
  flex-shrink: 0; display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
  border-radius: 100px; padding: 9px 16px; font-size: 13px; font-weight: 600;
  color: #94A3B8; text-decoration: none; transition: background 0.2s, border-color 0.2s, color 0.2s;
  white-space: nowrap;
}
.topic-pill:hover { background: rgba(255,255,255,0.07); color: #E2E8F0; }
.topic-pill-active {
  background: linear-gradient(135deg, rgba(124,58,237,0.25), rgba(6,182,212,0.2));
  border-color: rgba(124,58,237,0.5); color: #F1F5F9;
}
.pill-count {
  background: rgba(255,255,255,0.1); font-size: 11px; font-weight: 700;
  padding: 1px 7px; border-radius: 100px;
}
.topic-pill-active .pill-count { background: rgba(255,255,255,0.18); }

/* ─── Post grid ─── */
.posts-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px;
  margin-bottom: 40px;
}
@media (max-width: 980px) { .posts-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 620px) { .posts-grid { grid-template-columns: 1fr; } }

.post-card {
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
  border-radius: 22px; overflow: hidden; display: flex; flex-direction: column;
  transition: border-color 0.3s, transform 0.3s;
}
.post-card:hover { border-color: rgba(255,255,255,0.16); transform: translateY(-3px); }

.cover-link {
  position: relative; display: block; aspect-ratio: 4 / 5;
  background: #050710; overflow: hidden;
}
.cover-img { width: 100%; height: 100%; object-fit: cover; object-position: 50% 55%; }
.cover-placeholder {
  position: absolute; inset: 0;
  background: linear-gradient(160deg, #1B1036 0%, #2B0F3D 60%, #1A0B2E 100%);
  display: flex; flex-direction: column; justify-content: flex-end; gap: 8px;
  padding: 20px;
}
.placeholder-topic {
  display: inline-flex; align-self: flex-start; font-size: 10.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.5px;
  background: rgba(255,255,255,0.15); color: #fff; padding: 4px 10px; border-radius: 8px;
}
.placeholder-title { font-size: 16px; font-weight: 800; color: #fff; line-height: 1.35; }
.cover-fade {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.35) 100%);
  pointer-events: none;
}

.post-body { padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
.post-topic { font-size: 11px; font-weight: 700; color: #A78BFA; text-transform: uppercase; letter-spacing: 0.8px; }
.post-title-link { text-decoration: none; flex: 1; }
.post-title {
  font-size: 16.5px; font-weight: 800; color: #F1F5F9; line-height: 1.4;
  letter-spacing: -0.2px; margin-bottom: 8px; transition: color 0.2s;
}
.post-title-link:hover .post-title { color: #C4B5FD; }
.post-excerpt {
  font-size: 13px; color: #64748B; line-height: 1.65;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.post-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 6px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06);
  font-size: 11.5px; color: #475569;
}
.post-model {
  background: rgba(255,255,255,0.05); color: #94A3B8; font-family: monospace;
  padding: 2px 8px; border-radius: 6px; font-size: 10.5px; text-transform: uppercase;
}

/* ─── Empty state ─── */
.empty-state {
  text-align: center; padding: 64px 24px;
  background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.12);
  border-radius: 22px; color: #64748B; font-size: 14px; margin-bottom: 40px;
}

.pagination-block { margin-top: 8px; margin-bottom: 8px; }

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .index-main { padding: 100px 16px 60px; }
  .hero-block { margin-bottom: 36px; }
  .search-input { padding: 13px 70px 13px 40px; }
}

/* ─── Accessibility ─── */
.search-clear:focus-visible,
.reset-link:focus-visible,
.topic-pill:focus-visible,
.post-title-link:focus-visible,
.cover-link:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .post-card, .topic-pill, .post-title { transition: none !important; }
  .post-card:hover { transform: none !important; }
  .badge-dot { animation: none !important; }
}
</style>
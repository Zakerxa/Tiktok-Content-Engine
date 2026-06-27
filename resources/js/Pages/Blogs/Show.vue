<template>
  <Head :title="post.title" />

  <div class="show-root">
    <AppNavbar :auth="$page.props.auth" />

    <!-- Ambient brand orbs -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="show-main">

      <Link :href="route('blogs.index')" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to Content Feed
      </Link>

      <div class="show-grid">

        <!-- ═══════════ LEFT: Content ═══════════ -->
        <article class="content-card">
          <span class="topic-chip">{{ post.topic }}</span>

          <h1 class="post-title">{{ post.title }}</h1>

          <div class="hashtag-row" v-if="post.hashtags && post.hashtags.length">
            <span v-for="tag in post.hashtags" :key="tag" class="hashtag-chip">{{ tag }}</span>
          </div>

          <div class="script-box">
            <button
              @click="copyToClipboard(post.content)"
              class="copy-btn"
              :class="{ 'copy-btn-done': contentCopied }"
            >
              {{ contentCopied ? '✓ Copied' : '📋 Copy' }}
            </button>
            <div class="script-text">{{ post.content }}</div>
          </div>

          <div class="broll-section" v-if="post.b_roll_animation_suggestion || post.image_prompt">
            <h3 class="broll-heading">🎬 B-Roll Blueprint</h3>

            <div v-if="post.b_roll_animation_suggestion" class="broll-card">
              <span class="broll-label">Visual Sequence Directions</span>
              <p class="broll-text">{{ post.b_roll_animation_suggestion }}</p>
            </div>

            <div v-if="post.image_prompt" class="prompt-card">
              <div class="prompt-header">
                <span class="prompt-label">Midjourney / DALL·E Image Prompt</span>
                <button @click="copyPromptToClipboard(post.image_prompt)" class="prompt-copy-btn">
                  {{ promptCopied ? 'Copied' : 'Copy Prompt' }}
                </button>
              </div>
              <p class="prompt-text">{{ post.image_prompt }}</p>
            </div>
          </div>
        </article>

        <!-- ═══════════ RIGHT: Cover preview ═══════════ -->
        <aside class="cover-rail">
          <div class="cover-frame">
            <img v-if="post.image_path" :src="post.image_path" alt="Cover Preview" class="cover-img" />

            <div v-else class="cover-placeholder">
              <div class="placeholder-card">
                <h2 class="placeholder-title">{{ post.title }}</h2>
                <p class="placeholder-sub">{{ post.cover_title_burmese }}</p>
              </div>
              <div class="placeholder-meta">
                <span class="meta-eyebrow">Video Asset Metadata</span>
                <p><span class="meta-key">Engine:</span> {{ post.model_used }}</p>
                <p><span class="meta-key">Created:</span> {{ post.created_at }}</p>
              </div>
            </div>

            <button
              v-if="post.image_path"
              @click="downloadImage(post.image_path, post.title)"
              class="download-btn"
            >
              📥 Download Cover Art
            </button>

            <span class="aspect-badge">TikTok 9:16</span>
          </div>
        </aside>

      </div>
    </main>
  </div>
</template>

<script setup>
import AppNavbar from '@/Components/AppNavbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  post: Object,
});

const contentCopied = ref(false);
const promptCopied = ref(false);

const copyToClipboard = (text) => {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    contentCopied.value = true;
    setTimeout(() => { contentCopied.value = false; }, 2000);
  });
};

const copyPromptToClipboard = (text) => {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    promptCopied.value = true;
    setTimeout(() => { promptCopied.value = false; }, 2000);
  });
};

const downloadImage = (url, filename) => {
  const link = document.createElement('a');
  link.href = url;
  link.download = `${filename.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_cover.png`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
</script>

<style scoped>
.show-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* ─── Ambient orbs ─── */
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 560px; height: 560px; background: rgba(124,58,237,0.18); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6,182,212,0.14);  top: 500px; right: -160px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.08); bottom: -100px; left: 30%; }

.show-main {
  position: relative; z-index: 5;
  max-width: 1140px; margin: 0 auto;
  padding: 140px 24px 100px;
}

.back-link {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 13.5px; font-weight: 600; color: #94A3B8;
  text-decoration: none; margin-bottom: 28px;
  transition: color 0.2s;
}
.back-link:hover { color: #F1F5F9; }

.show-grid {
  display: grid; grid-template-columns: 1.5fr 1fr; gap: 28px; align-items: start;
}
@media (max-width: 960px) {
  .show-grid { grid-template-columns: 1fr; }
}

/* ─── Content card ─── */
.content-card {
  background: rgba(255,255,255,0.035);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px; padding: 36px;
  backdrop-filter: blur(18px);
}
@media (max-width: 960px) {
  .content-card { order: 2; padding: 28px 24px; }
}

.topic-chip {
  display: inline-block; font-size: 11.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.8px;
  background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.35);
  color: #C4B5FD; padding: 5px 12px; border-radius: 8px; margin-bottom: 16px;
}

.post-title {
  font-size: clamp(24px, 3vw, 32px); font-weight: 800;
  letter-spacing: -0.6px; line-height: 1.25; color: #F1F5F9;
}

.hashtag-row { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }
.hashtag-chip {
  font-size: 12px; font-weight: 500; color: #94A3B8;
  background: rgba(255,255,255,0.05); padding: 3px 10px; border-radius: 8px;
}

/* ─── Script box ─── */
.script-box {
  position: relative; margin-top: 32px;
  background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.08);
  border-radius: 18px; padding: 28px;
}
.copy-btn {
  position: absolute; top: 16px; right: 16px;
  font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
  padding: 7px 14px; border-radius: 10px; cursor: pointer;
  background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
  color: #E2E8F0; transition: background 0.2s, border-color 0.2s;
}
.copy-btn:hover { background: rgba(255,255,255,0.1); border-color: rgba(124,58,237,0.4); }
.copy-btn-done { background: rgba(52,211,153,0.18); border-color: rgba(52,211,153,0.4); color: #34D399; }
.script-text {
  font-size: 15px; line-height: 1.75; color: #CBD5E1;
  white-space: pre-line; padding-right: 70px;
}

/* ─── B-Roll section ─── */
.broll-section { margin-top: 36px; padding-top: 28px; border-top: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; gap: 18px; }
.broll-heading { font-size: 16px; font-weight: 700; color: #F1F5F9; display: flex; align-items: center; gap: 8px; }
.broll-card {
  background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07);
  border-radius: 14px; padding: 16px;
}
.broll-label { font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.6px; display: block; margin-bottom: 6px; }
.broll-text { font-size: 13.5px; color: #CBD5E1; line-height: 1.7; }

.prompt-card {
  background: rgba(0,0,0,0.3); border: 1px solid rgba(124,58,237,0.25);
  border-radius: 14px; padding: 16px;
}
.prompt-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.prompt-label { font-size: 11px; font-weight: 700; color: #A78BFA; text-transform: uppercase; letter-spacing: 0.6px; }
.prompt-copy-btn {
  font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;
  background: none; border: none; color: #64748B; cursor: pointer; text-decoration: underline;
}
.prompt-copy-btn:hover { color: #F1F5F9; }
.prompt-text { font-size: 12.5px; font-family: 'JetBrains Mono', monospace; color: #94A3B8; line-height: 1.7; user-select: all; }

/* ─── Cover rail ─── */
.cover-rail { width: 100%; }
@media (min-width: 961px) { .cover-rail { position: sticky; top: 100px; } }
@media (max-width: 960px) { .cover-rail { order: 1; max-width: 360px; margin: 0 auto; } }

.cover-frame {
  position: relative; width: 100%; aspect-ratio: 9 / 16;
  background: #050710; border-radius: 32px; overflow: hidden;
  border: 6px solid rgba(255,255,255,0.06);
  box-shadow: 0 30px 80px rgba(0,0,0,0.55);
}
.cover-img {
  width: 100%; height: 100%;
  object-fit: cover; object-position: center;
}

.cover-placeholder {
  position: absolute; inset: 0;
  background: linear-gradient(180deg, #0F1320 0%, #1B1036 55%, #2B0F3D 100%);
  display: flex; flex-direction: column; justify-content: space-between;
  padding: 32px 24px; text-align: center;
}
.placeholder-card {
  margin-top: 36px;
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 18px; padding: 20px; backdrop-filter: blur(10px);
}
.placeholder-title { font-size: 17px; font-weight: 800; color: #FCD34D; line-height: 1.35; margin-bottom: 8px; }
.placeholder-sub { font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); }

.placeholder-meta {
  text-align: left;
  background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.07);
  border-radius: 14px; padding: 14px 16px; font-size: 12px; color: #CBD5E1;
  display: flex; flex-direction: column; gap: 5px;
}
.meta-eyebrow { font-size: 10px; font-weight: 700; color: #A78BFA; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; }
.meta-key { color: #64748B; }

.download-btn {
  position: absolute; left: 20px; right: 20px; bottom: 52px;
  background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(10px); color: #fff; font-size: 12.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.5px;
  padding: 12px 16px; border-radius: 14px; cursor: pointer;
  transition: background 0.2s, transform 0.2s;
}
.download-btn:hover { background: rgba(255,255,255,0.18); transform: translateY(-1px); }

.aspect-badge {
  position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
  background: rgba(0,0,0,0.65); color: #fff; font-size: 9.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1.2px;
  padding: 5px 12px; border-radius: 100px; border: 1px solid rgba(255,255,255,0.08);
  pointer-events: none;
}

/* ─── Accessibility ─── */
.back-link:focus-visible,
.copy-btn:focus-visible,
.prompt-copy-btn:focus-visible,
.download-btn:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .back-link, .copy-btn, .download-btn { transition: none !important; }
  .download-btn:hover { transform: none !important; }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .show-main { padding: 120px 16px 80px; }
  .content-card { padding: 22px 18px; border-radius: 20px; }
  .script-box { padding: 20px; }
  .script-text { padding-right: 0; margin-top: 36px; }
  .copy-btn { top: 12px; right: 12px; }
}
</style>
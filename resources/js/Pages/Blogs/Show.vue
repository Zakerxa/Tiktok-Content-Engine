<template>
  <Head :title="post.title" />

  <div class="bg-[#080B14] text-slate-100 font-sans min-h-screen relative overflow-hidden">
    <AppNavbar :auth="$page.props.auth" />

    <!-- Ambient brand orbs -->
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[560px] h-[560px] bg-violet-600/[0.18] -top-[120px] -left-[160px]"></div>
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[460px] h-[460px] bg-cyan-500/[0.14] top-[500px] -right-[160px]"></div>
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[280px] h-[280px] bg-amber-500/[0.08] -bottom-[100px] left-[30%]"></div>

    <main class="relative z-[5] max-w-[1140px] mx-auto px-6 pt-[140px] pb-[100px] sm:px-4 sm:pt-[120px] sm:pb-20">

      <Link :href="route('blogs.index')" class="inline-flex items-center gap-2 text-[13.5px] font-semibold text-slate-400 hover:text-slate-100 no-underline mb-7 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back to Content Feed
      </Link>

      <div class="grid grid-cols-1 md:grid-cols-[1.5fr_1fr] gap-7 items-start">

        <!-- ═══════════ LEFT: Content ═══════════ -->
        <article class="order-2 md:order-1 bg-white/[0.035] border border-white/[0.08] rounded-3xl p-6 sm:p-9 backdrop-blur-xl">
          <span class="inline-block text-[11.5px] font-bold uppercase tracking-wide bg-violet-600/15 border border-violet-600/35 text-violet-300 px-3 py-1.5 rounded-lg mb-4">{{ post.topic }}</span>

          <h1 class="text-[clamp(24px,3vw,32px)] font-extrabold tracking-tight leading-tight text-slate-100">{{ post.title }}</h1>

          <div class="flex flex-wrap gap-1.5 mt-3.5" v-if="post.hashtags && post.hashtags.length">
            <span v-for="tag in post.hashtags" :key="tag" class="text-xs font-medium text-slate-400 bg-white/5 px-2.5 py-0.5 rounded-lg">{{ tag }}</span>
          </div>

          <div class="relative mt-8 bg-white/[0.025] border border-white/[0.08] rounded-2xl p-5 sm:p-7">
            <button
              @click="copyToClipboard(post.content)"
              :class="['absolute top-3 sm:top-4 right-3 sm:right-4 text-[11px] font-bold uppercase tracking-wide px-3.5 py-1.5 rounded-[10px] cursor-pointer border transition-colors',
                contentCopied
                  ? 'bg-emerald-400/[0.18] border-emerald-400/40 text-emerald-400'
                  : 'bg-white/[0.06] border-white/[0.12] text-slate-200 hover:bg-white/10 hover:border-violet-600/40']"
            >
              {{ contentCopied ? '✓ Copied' : '📋 Copy' }}
            </button>
            <div class="text-[15px] leading-relaxed text-slate-300 whitespace-pre-line pr-0 mt-9 sm:pr-[70px] sm:mt-0">{{ post.content }}</div>
          </div>

          <div class="mt-9 pt-7 border-t border-white/[0.08] flex flex-col gap-4.5 gap-[18px]" v-if="post.b_roll_animation_suggestion || post.image_prompt">
            <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">🎬 B-Roll Blueprint</h3>

            <div v-if="post.b_roll_animation_suggestion" class="bg-white/[0.025] border border-white/[0.07] rounded-[14px] p-4">
              <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block mb-1.5">Visual Sequence Directions</span>
              <p class="text-[13.5px] text-slate-300 leading-relaxed">{{ post.b_roll_animation_suggestion }}</p>
            </div>

            <div v-if="post.image_prompt" class="bg-black/30 border border-violet-600/25 rounded-[14px] p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-violet-400 uppercase tracking-wide">Midjourney / DALL·E Image Prompt</span>
                <button @click="copyPromptToClipboard(post.image_prompt)" class="text-[10.5px] font-bold uppercase tracking-wide bg-transparent border-none text-slate-500 hover:text-slate-100 cursor-pointer underline">
                  {{ promptCopied ? 'Copied' : 'Copy Prompt' }}
                </button>
              </div>
              <p class="text-[12.5px] font-mono text-slate-400 leading-relaxed select-all">{{ post.image_prompt }}</p>
            </div>
          </div>
        </article>

        <!-- ═══════════ RIGHT: Cover preview ═══════════ -->
        <aside class="order-1 md:order-2 w-full max-w-[360px] mx-auto md:max-w-none md:mx-0 md:sticky md:top-[100px]">
          <div class="relative w-full aspect-9/16 bg-[#050710] rounded-[32px] overflow-hidden border-[6px] border-white/[0.06] shadow-[0_30px_80px_rgba(0,0,0,0.55)]">
            <img v-if="post.image_path" :src="post.image_path" alt="Cover Preview" class="w-full h-full object-cover object-center" />

            <div v-else class="absolute inset-0 bg-gradient-to-b from-[#0F1320] via-[#1B1036] to-[#2B0F3D] flex flex-col justify-between p-6 sm:p-8 text-center" style="background-image: linear-gradient(180deg, #0F1320 0%, #1B1036 55%, #2B0F3D 100%);">
              <div class="mt-9 bg-white/5 border border-white/10 rounded-[18px] p-5 backdrop-blur-md">
                <h2 class="text-[17px] font-extrabold text-amber-300 leading-snug mb-2">{{ post.title }}</h2>
                <p class="text-[12.5px] font-semibold text-white/85">{{ post.cover_title_burmese }}</p>
              </div>
              <div class="text-left bg-black/40 border border-white/[0.07] rounded-[14px] px-4 py-3.5 text-xs text-slate-300 flex flex-col gap-1.5">
                <span class="text-[10px] font-bold text-violet-400 uppercase tracking-wide mb-1">Video Asset Metadata</span>
                <p><span class="text-slate-500">Engine:</span> {{ post.model_used }}</p>
                <p><span class="text-slate-500">Created:</span> {{ post.created_at }}</p>
              </div>
            </div>

            <button
              v-if="post.image_path"
              @click="downloadImage(post.image_path, post.title)"
              class="absolute left-5 right-5 bottom-[52px] bg-white/10 hover:bg-white/[0.18] border border-white/20 backdrop-blur-md text-white text-[12.5px] font-bold uppercase tracking-wide px-4 py-3 rounded-[14px] cursor-pointer transition-all hover:-translate-y-px"
            >
              📥 Download Cover Art
            </button>

            <span class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/65 text-white text-[9.5px] font-bold uppercase tracking-[1.2px] px-3 py-1.5 rounded-full border border-white/[0.08] pointer-events-none">TikTok 9:16</span>
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
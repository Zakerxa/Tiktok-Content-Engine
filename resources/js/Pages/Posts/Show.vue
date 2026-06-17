<template>
  <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      
      <div class="mb-6">
        <Link :href="route('home')" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 bg-white px-4 py-2 rounded-xl shadow-xs border border-gray-100 transition">
          ← Back to Content Feed
        </Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start max-lg:flex max-lg:flex-col">
        
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-xs w-full">
          <span class="inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 tracking-wider uppercase mb-3">
            {{ post.topic }}
          </span>
          
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-snug">
            {{ post.title }}
          </h1>

          <div class="mt-3 flex flex-wrap gap-1.5">
            <span 
              v-for="tag in post.hashtags" 
              :key="tag" 
              class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md"
            >
              {{ tag }}
            </span>
          </div>

          <div class="mt-8 relative bg-gray-50 rounded-2xl p-6 sm:p-8 border border-gray-100 shadow-inner group">
            
            <button 
              @click="copyToClipboard(post.content)"
              class="absolute top-4 right-4 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg shadow-2xs border transition duration-150"
              :class="contentCopied ? 'bg-emerald-600 text-white border-transparent' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'"
            >
              <span>{{ contentCopied ? '✓ Copied' : '📋 Copy' }}</span>
            </button>

            <div class="text-base sm:text-lg text-gray-800 leading-relaxed whitespace-pre-line font-normal pr-14">
              {{ post.content }}
            </div>
          </div>

          <div class="mt-10 border-t border-gray-200 pt-8 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">🎬 B-Roll Blueprint</h3>
            
            <div v-if="post.b_roll_animation_suggestion" class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Visual Sequence Directions</span>
              <p class="text-sm text-slate-700 leading-relaxed">{{ post.b_roll_animation_suggestion }}</p>
            </div>

            <div v-if="post.image_prompt" class="bg-gray-900 rounded-2xl p-4 shadow-inner relative">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Midjourney / DALL-E Image Prompt</span>
                <button 
                  @click="copyPromptToClipboard(post.image_prompt)" 
                  class="text-[10px] font-bold uppercase tracking-wider text-gray-400 hover:text-white underline"
                >
                  {{ promptCopied ? 'Copied' : 'Copy Prompt' }}
                </button>
              </div>
              <p class="text-xs font-mono text-gray-300 leading-relaxed select-all">{{ post.image_prompt }}</p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-5 lg:sticky lg:top-6 w-full flex flex-col items-center gap-4 max-lg:order-first">
          <div class="bg-gray-950 shadow-2xl rounded-[2.5rem] overflow-hidden border-8 border-gray-800 w-full max-w-sm aspect-[9/16] relative group">
            
            <img v-if="post.image_path" :src="post.image_path" alt="Cover Preview" class="w-full h-full object-cover" />
            
            <div v-else class="absolute inset-0 bg-gradient-to-b from-slate-900 via-indigo-950 to-purple-950 flex flex-col justify-between p-8 text-center">
              <div class="pt-10 bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                <h2 class="text-lg font-black text-amber-400 mb-2 leading-snug">{{ post.title }}</h2>
                <p class="text-xs font-semibold text-white/90 tracking-wide">{{ post.cover_title_burmese }}</p>
              </div>
              
              <div class="text-left space-y-1.5 bg-black/40 backdrop-blur-xs rounded-xl p-4 border border-slate-800/60 text-xs text-slate-300">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider block">Video Asset Metadata</span>
                <p><span class="text-slate-500">Engine:</span> {{ post.model_used }}</p>
                <p><span class="text-slate-500">Created:</span> {{ post.created_at }}</p>
              </div>
            </div>

            <div class="absolute bottom-14 left-0 right-0 px-6 opacity-90 group-hover:opacity-100 transition">
              <button 
                v-if="post.image_path"
                @click="downloadImage(post.image_path, post.title)"
                class="w-full bg-white/10 backdrop-blur-md hover:bg-white/20 text-white text-xs font-bold uppercase tracking-wider py-3 px-4 rounded-xl border border-white/20 shadow-lg flex items-center justify-center gap-2 transition"
              >
                📥 Download Cover Art
              </button>
            </div>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/70 backdrop-blur-md text-white text-[9px] uppercase font-bold tracking-widest px-3 py-1 rounded-full border border-white/5 pointer-events-none">
              TikTok 9:16 Aspect View
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
  post: Object
});

const contentCopied = ref(false);
const promptCopied = ref(false);

// Content Copy Action
const copyToClipboard = (text) => {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    contentCopied.value = true;
    setTimeout(() => {
      contentCopied.value = false;
    }, 2000);
  });
};

// Bottom Code Snippet Prompt Copy Action
const copyPromptToClipboard = (text) => {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    promptCopied.value = true;
    setTimeout(() => {
      promptCopied.value = false;
    }, 2000);
  });
};

// Asset Downloader
const downloadImage = (url, filename) => {
  const link = document.createElement('a');
  link.href = url;
  link.download = `${filename.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_cover.png`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
</script>
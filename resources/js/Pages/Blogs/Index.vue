<template>
  <Head title="Content Hub" />

  <div class=" text-slate-100 font-sans min-h-screen relative overflow-hidden">
    <AppNavbar :auth="$page.props.auth" />

    <!-- Ambient brand orbs -->
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[580px] h-[580px] bg-violet-600/[0.18] -top-[140px] -left-[180px]"></div>
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[480px] h-[480px] bg-cyan-500/[0.14] top-[600px] -right-[180px]"></div>
    <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[280px] h-[280px] bg-amber-500/[0.08] bottom-[200px] left-[35%]"></div>

    <main class="relative z-[5] max-w-[1180px] mx-auto px-6 pt-[100px] pb-20 sm:px-4 sm:pt-[100px] sm:pb-[60px]">

      <!-- ═══════════ Hero / search ═══════════ -->
      <section class="text-center max-w-[640px] mx-auto mb-12 sm:mb-9">
        <div class="inline-flex items-center gap-2 bg-violet-600/15 border border-violet-600/35 text-violet-300 text-[12.5px] font-medium px-4 py-1.5 rounded-full mb-[22px]">
          <span class="w-1.5 h-1.5 bg-violet-600 rounded-full animate-pulse"></span>
          AI-Generated TikTok Scripts
        </div>
        <h1 class="text-[clamp(32px,5vw,48px)] font-black tracking-[-1.5px] mb-3.5">
          <span class="bg-gradient-to-br from-violet-600 via-cyan-500 to-amber-500 bg-clip-text text-transparent">TikTok</span> Content <span class="bg-gradient-to-br from-violet-600 via-cyan-500 to-amber-500 bg-clip-text text-transparent">Hub</span>
        </h1>
        <p class="text-[15.5px] text-slate-400 leading-relaxed mb-[30px]">
          Instant video production scripts, tactical hooks, and image-prompt blueprints — ready to post.
        </p>

        <div class="relative max-w-[440px] mx-auto">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search blogs by title..."
            class="w-full py-3.5 pl-11 pr-20 sm:pl-10 sm:pr-[70px] rounded-2xl bg-white/[0.04] border border-white/10 text-slate-100 text-sm placeholder:text-slate-600 focus:outline-none focus:border-violet-600 focus:ring-4 focus:ring-violet-600/15"
          />
          <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11.5px] font-bold text-slate-500 hover:text-slate-100 bg-transparent border-none cursor-pointer">
            Clear
          </button>
        </div>
      </section>

      <!-- ═══════════ Topic pills (horizontal scroll) ═══════════ -->
      <section class="mb-9">
        <div class="flex items-center justify-between mb-3.5">
          <span class="text-[11px] font-bold uppercase tracking-[1.5px] text-violet-600">Topics</span>
          <Link v-if="currentTopic" :href="route('blogs.index')" class="text-xs font-semibold text-violet-300 no-underline hover:underline">
            Reset Filter ✕
          </Link>
        </div>

        <div class="flex gap-2.5 overflow-x-auto pb-1.5 [scrollbar-width:thin] [scrollbar-color:rgba(255,255,255,0.15)_transparent] [&::-webkit-scrollbar]:h-[5px] [&::-webkit-scrollbar-thumb]:bg-white/[0.15] [&::-webkit-scrollbar-thumb]:rounded-[10px]">
          <Link
            :href="route('blogs.index')"
            :class="['shrink-0 inline-flex items-center gap-2 rounded-full px-4 py-2.5 text-[13px] font-semibold no-underline whitespace-nowrap transition-colors',
              !currentTopic
                ? 'bg-gradient-to-br from-violet-600/25 to-cyan-500/20 border border-violet-600/50 text-slate-100'
                : 'bg-white/[0.03] border border-white/[0.08] text-slate-400 hover:bg-white/[0.07] hover:text-slate-200']"
          >
            <span>All Posts</span>
          </Link>

          <Link
            v-for="stat in stats"
            :key="stat.clean"
            :href="route('topics.show', stat.clean)"
            :class="['shrink-0 inline-flex items-center gap-2 rounded-full px-4 py-2.5 text-[13px] font-semibold no-underline whitespace-nowrap transition-colors',
              currentTopic === stat.clean
                ? 'bg-gradient-to-br from-violet-600/25 to-cyan-500/20 border border-violet-600/50 text-slate-100'
                : 'bg-white/[0.03] border border-white/[0.08] text-slate-400 hover:bg-white/[0.07] hover:text-slate-200']"
          >
            <span>{{ stat.clean }}</span>
            <span :class="['text-[11px] font-bold px-[7px] py-px rounded-full', currentTopic === stat.clean ? 'bg-white/[0.18]' : 'bg-white/10']">{{ stat.total }}</span>
          </Link>
        </div>
      </section>

      <!-- ═══════════ Post grid ═══════════ -->
      <section v-if="filteredPosts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[22px] mb-10">
        <article v-for="post in filteredPosts" :key="post.id" class="bg-white/[0.03] border border-white/[0.08] rounded-[22px] overflow-hidden flex flex-col transition-all duration-300 hover:border-white/[0.16] hover:-translate-y-[3px]">

          <Link :href="route('posts.show', post.slug)" class="relative block aspect-[4/5] bg-[#050710] overflow-hidden">
            <img v-if="post.image_path" :src="post.image_path" :alt="post.title" class="w-full h-full object-cover" style="object-position: 50% 55%" />
            <div v-else class="absolute inset-0 flex flex-col justify-end gap-2 p-5" style="background-image: linear-gradient(160deg, #1B1036 0%, #2B0F3D 60%, #1A0B2E 100%);">
              <span class="inline-flex self-start text-[10.5px] font-bold uppercase tracking-wide bg-white/[0.15] text-white px-2.5 py-1 rounded-lg">{{ post.topic }}</span>
              <h3 class="text-base font-extrabold text-white leading-snug">{{ post.cover_title_burmese }}</h3>
            </div>
            <span class="absolute inset-0 pointer-events-none" style="background-image: linear-gradient(180deg, transparent 60%, rgba(0,0,0,0.35) 100%);"></span>
          </Link>

          <div class="p-5 flex flex-col gap-2.5 flex-1">
            <span class="text-[11px] font-bold text-violet-300 uppercase tracking-wide">{{ post.topic }}</span>

            <Link :href="route('posts.show', post.slug)" class="no-underline flex-1 group">
              <h2 class="text-[16.5px] font-extrabold text-slate-100 leading-snug tracking-[-0.2px] mb-2 transition-colors group-hover:text-violet-300">{{ post.title }}</h2>
              <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2">{{ post.content }}</p>
            </Link>

            <div class="flex items-center justify-between mt-1.5 pt-3.5 border-t border-white/[0.06] text-[11.5px] text-slate-600">
              <span>📅 {{ post.created_at }}</span>
              <span class="bg-white/5 text-slate-400 font-mono px-2 py-0.5 rounded-md text-[10.5px] uppercase">{{ post.model_used }}</span>
            </div>
          </div>
        </article>
      </section>

      <div v-else class="text-center py-16 px-6 bg-white/[0.02] border border-dashed border-white/[0.12] rounded-[22px] text-slate-500 text-sm mb-10">
        <p>No blog titles match "{{ searchQuery }}".</p>
      </div>

      <Pagination
        v-if="posts.links && posts.total > 0"
        :links="posts.links"
        :from="posts.from"
        :to="posts.to"
        :total="posts.total"
        class="mt-2 mb-2"
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
import { router } from '@inertiajs/vue3';
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
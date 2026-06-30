<template>
  <Head title="TikTok Post" />

  <AppSidebar :auth="$page.props.auth">
    <div class="bg-[#080B14] text-slate-100 font-sans min-h-screen relative overflow-hidden">
      <!-- Ambient brand orbs -->
      <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[580px] h-[580px] bg-violet-600/[0.18] -top-[140px] -left-[180px]"></div>
      <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[480px] h-[480px] bg-cyan-500/[0.14] top-[600px] -right-[180px]"></div>
      <div class="absolute rounded-full blur-[80px] pointer-events-none z-0 w-[280px] h-[280px] bg-amber-500/[0.08] bottom-[200px] left-[35%]"></div>

      <main class="relative z-[5] mt-10 px-10 pt-10 sm:px-4">

        <!-- ═══════════ Hero / search ═══════════ -->
        <section class="text-center max-w-[640px] mx-auto mb-12">
          <div class="relative max-w-[440px] mx-auto">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-600" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search blogs by title..."
              class="w-full py-3.5 pl-11 pr-20 rounded-2xl bg-white/[0.04] border border-white/10 text-slate-100 text-sm placeholder:text-slate-600 focus:outline-none focus:border-violet-600 focus:ring-4 focus:ring-violet-600/15"
            />
            <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11.5px] font-bold text-slate-500 hover:text-slate-100 bg-transparent border-none cursor-pointer">
              Clear
            </button>
          </div>
        </section>

        <!-- ═══════════ Post grid ═══════════ -->
        <section v-if="filteredPosts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[22px] mb-10">
          <article v-for="post in filteredPosts" :key="post.id" class="bg-white/[0.03] border border-white/[0.08] rounded-[22px] overflow-hidden flex flex-col transition-all duration-300 hover:border-white/[0.16] hover:-translate-y-[3px]">

            <Link :href="route('posts.show', post.slug)" class="relative block aspect-[3/4] bg-[#050710] overflow-hidden">
              <img v-if="post.image_path" :src="post.image_path" :alt="post.title" class="w-full h-full object-cover" style="object-position: 50% 25%" />
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
                <span class="text-xs text-[#64748B]">📅 {{ post.created_at_human }}</span>
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
          class="mt-2 mb-2 pb-20"
        />

      </main>

    </div>
  </AppSidebar>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import AppSidebar from '@/Components/AppSidebar.vue';

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
<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-3xl mx-auto mb-10">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl tracking-tight">
          🎬 TikTok Content Hub
        </h1>
        <p class="mt-4 text-lg text-gray-500">
          Instant video production scripts, tactical hooks, and image matrix blueprints.
        </p>

        <div class="mt-6 max-w-md mx-auto relative rounded-2xl shadow-sm">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
            <span class="text-gray-400 text-sm">🔍</span>
          </div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search blogs by title name..." 
            class="block w-full rounded-2xl border-0 py-3.5 pl-11 pr-4 text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm bg-white transition"
          />
          <button 
            v-if="searchQuery" 
            @click="searchQuery = ''" 
            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 text-xs font-bold"
          >
            Clear
          </button>
        </div>
      </div>

      <div class="mb-12">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Topics Metrics</h3>
          <Link v-if="currentTopic" :href="route('home')" class="text-xs text-indigo-600 font-medium hover:underline">
            Reset Filter ✕
          </Link>
        </div>
        <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
          <Link 
            :href="route('home')" 
            class="bg-white overflow-hidden shadow-xs rounded-xl border p-5 transition hover:shadow-md block"
            :class="[!currentTopic ? 'ring-2 ring-indigo-500 bg-indigo-50/30 border-transparent' : 'border-gray-100']"
          >
            <dt class="text-xs font-semibold text-gray-500 uppercase">Global Matrix</dt>
            <dd class="mt-1 text-2xl font-bold text-indigo-600">All Posts</dd>
          </Link>

          <Link 
            v-for="stat in stats" 
            :key="stat.raw" 
            :href="route('topics.show', stat.clean)"
            class="bg-white overflow-hidden shadow-xs rounded-xl border p-5 transition hover:shadow-md block"
            :class="[currentTopic === stat.clean ? 'ring-2 ring-indigo-500 bg-indigo-50/30 border-transparent' : 'border-gray-100']"
          >
            <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider truncate">{{ stat.clean }}</dt>
            <dd class="mt-1 text-3xl font-extrabold text-gray-900 flex items-baseline gap-1">
              {{ stat.total }}
              <span class="text-xs font-normal text-gray-400">videos</span>
            </dd>
          </Link>
        </dl>
      </div>

      <div v-if="filteredPosts.length > 0" class="grid gap-8 lg:grid-cols-3 md:grid-cols-2 grid-cols-1">
        <article 
  v-for="post in filteredPosts" 
  :key="post.id" 
  class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-xs border border-gray-100 transition duration-300 hover:-translate-y-1 hover:shadow-lg"
>
  <Link :href="route('posts.show', post.slug)" class="shrink-0 bg-gray-900 relative aspect-[4/3] overflow-hidden block">
    <img v-if="post.image_path" :src="post.image_path" :alt="post.title" class="h-full w-full object-cover" />
    <div v-else class="absolute inset-0 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 flex flex-col justify-end p-6">
      <span class="inline-flex items-center rounded-md bg-white/20 px-2 py-1 text-xs font-medium text-white backdrop-blur-md mb-2 w-max">
        {{ post.topic }}
      </span>
      <h3 class="text-lg font-bold text-white leading-snug">{{ post.cover_title_burmese }}</h3>
    </div>
  </Link>

  <div class="flex flex-1 flex-col justify-between p-6">
    <div class="flex-1">
      <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ post.topic }}</span>
      <Link :href="route('posts.show', post.slug)" class="mt-2 block group">
        <h2 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition duration-150 leading-snug">
          {{ post.title }}
        </h2>
        <p class="mt-3 text-sm text-gray-500 line-clamp-3 leading-relaxed">
          {{ post.content }}
        </p>
      </Link>
    </div>
    
    <div class="mt-6 flex items-center justify-between border-t border-gray-100 pt-4 text-xs text-gray-400">
      <div>📅 {{ post.created_at }}</div>
      <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] uppercase font-mono">{{ post.model_used }}</span>
    </div>
  </div>
</article>
      </div>

      <div v-else class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200">
        <p class="text-sm text-gray-500">No blog titles discovered matching "{{ searchQuery }}".</p>
      </div>

      <Pagination 
        v-if="posts.links && posts.total > 0"
        :links="posts.links"
        :from="posts.from"
        :to="posts.to"
        :total="posts.total"
        class="mt-8"
      />

       <div class="mt-16">
         <Footer :stats="stats" />
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  posts: Object,
  stats: Array,
  currentTopic: String
});

const searchQuery = ref('');

// Dynamically filter matching title fields in real-time
const filteredPosts = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.posts.data;
  }
  return props.posts.data.filter(post => 
    post.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    post.cover_title_burmese.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});
</script>
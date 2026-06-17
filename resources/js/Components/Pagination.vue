<template>
  <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-xl shadow-sm">
    <div class="flex flex-1 justify-between sm:hidden">
      <Link :href="prevUrl" :class="[!prevUrl ? 'pointer-events-none opacity-50' : '', 'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50']">Previous</Link>
      <Link :href="nextUrl" :class="[!nextUrl ? 'pointer-events-none opacity-50' : '', 'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50']">Next</Link>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing <span class="font-medium">{{ from }}</span> to <span class="font-medium">{{ to }}</span> of <span class="font-medium">{{ total }}</span> scripts
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
          <Link 
            v-for="(link, key) in links" 
            :key="key" 
            :href="link.url || '#'" 
            :disabled="!link.url"
            v-html="link.label"
            :class="[
              link.active ? 'z-10 bg-indigo-600 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
              !link.url ? 'pointer-events-none text-gray-300' : '',
              'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
            ]"
          />
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  links: Array,
  from: Number,
  to: Number,
  total: Number
});

const prevUrl = computed(() => props.links[0]?.url);
const nextUrl = computed(() => props.links[props.links.length - 1]?.url);
</script>
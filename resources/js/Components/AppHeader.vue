<template>
  <nav class="bg-white shadow-md dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <div class="flex-shrink-0 font-bold text-xl text-indigo-600 dark:text-indigo-400">
          TikTok Engine
        </div>

        <div class="hidden md:flex space-x-8">
          <a href="/" class="flex items-center text-gray-700 dark:text-gray-200 hover:text-indigo-600">
            <span class="mr-2">🏠</span> Home
          </a>
          <a href="/topics" class="flex items-center text-gray-700 dark:text-gray-200 hover:text-indigo-600">
            <span class="mr-2">🏷️</span> Topics
          </a>
        </div>

        <div class="md:hidden">
          <button @click="isOpen = !isOpen" class="text-gray-700 dark:text-gray-200 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="isOpen" class="md:hidden bg-gray-50 dark:bg-gray-700 px-4 pt-2 pb-4 space-y-2">
      <a href="/" class="block flex items-center p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
        <span class="mr-3">🏠</span> Home
      </a>
      <a href="/topics" class="block flex items-center p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
        <span class="mr-3">🏷️</span> Topics
      </a>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
const isOpen = ref(false);
</script>
<template>
  <footer class="bg-white border-t border-gray-200 w-full py-8 mt-auto font-sans">
    <div class="max-w-7xl mx-auto px-4 text-center">
      
      <div class="flex justify-center items-center gap-2 mb-4">
        <span class="text-xl">🎬</span>
        <span class="font-extrabold text-gray-900 tracking-tight">TikTok Content Hub</span>
      </div>

      <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mb-4 text-sm font-medium text-gray-500">
        <Link :href="route('home')" class="hover:text-indigo-600 transition duration-150">Home</Link>
        <a href="#" class="hover:text-indigo-600 transition duration-150">Privacy Policy</a>
        <a href="#" class="hover:text-indigo-600 transition duration-150">Terms of Service</a>
        <a href="#" class="hover:text-indigo-600 transition duration-150">Contact Support</a>
      </div>

      <p class="text-xs text-gray-400">
        &copy; 2026 TikTok Content Hub. All rights reserved.
      </p>

    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
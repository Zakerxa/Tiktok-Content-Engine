<template>
  <nav class="nav-bar">
    <div class="nav-inner">
      <a href="/" class="nav-logo">
        <img src="/favicon.png" alt="icon" class="logo-icon" srcset="">
        <span style="font-size:20px;font-weight:800;letter-spacing:1px;color:#F1F5F9;position: relative;left: -10px;">Z.<span class="pc">A</span>.K.<span class="pc">E</span>.R.<span class="pc">X</span><span>.A</span></span>
      </a>
      <div class="nav-links">
        <a href="/blogs" class="nav-link">TikTok Post</a>
        <a href="/movie-recap" class="nav-link">Movie Recap</a>
        <a href="/ContactUs" class="nav-link">Contct Us</a>
        <a href="/About" class="nav-link">About</a>
        <a v-if="isHome" href="#pricing" class="nav-link">Pricing</a>
        <a v-if="auth?.user" href="/dashboard" class="btn-nav-primary">Dashboard</a>
        <template v-else>
          <a href="/login" class="btn-nav-ghost">Sign In</a>
          <a href="/auth/google" class="btn-nav-primary">Get Started</a>
        </template>
      </div>

      <!-- Mobile hamburger -->
      <button class="mobile-menu-btn" @click="open = !open" aria-label="Toggle menu">
        <span class="hamburger-line" :class="{ 'rotate-45 translate-y-2': open }"></span>
        <span class="hamburger-line" :class="{ 'opacity-0': open }"></span>
        <span class="hamburger-line" :class="{ '-rotate-45 -translate-y-2': open }"></span>
      </button>
    </div>

    <!-- Mobile drawer -->
    <div v-if="open" class="mobile-drawer">
      <a href="/blogs" class="mobile-link" @click="open = false">TikTok Post</a>
      <a href="/movie-recap" class="mobile-link" @click="open = false">Movie Recap</a>
      <a v-if="isHome" href="#pricing" class="mobile-link" @click="open = false">Pricing</a>
      <div class="mobile-actions">
        <a v-if="auth?.user" href="/dashboard" class="btn-nav-primary w-full text-center">Dashboard</a>
        <template v-else>
          <a href="/login" class="btn-nav-ghost text-center">Sign In</a>
          <a href="/auth/google" class="btn-nav-primary text-center">Get Started</a>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
  auth: Object,
});

const open = ref(false);

const page = usePage();
const isHome = computed(() => page.url === '/' || page.url.startsWith('/?'));
</script>

<style>

.pc{
    color:#7C3AED;
}
.nav-bar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: rgba(8, 11, 20, 0.85);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.nav-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.nav-logo {
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}
.logo-icon {
  width: 65px;
  height: 65px;
  /* background: linear-gradient(135deg, #180f27, #0c2327); */
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}
.logo-text {
  font-size: 22px;
  font-weight: 800;
  color: #F1F5F9;
  position: relative;
  left: -10px;
  letter-spacing: 1px;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}
@media (max-width: 640px) {
  .nav-links { display: none; }
}
.nav-link {
  color: #94A3B8;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 6px 12px;
  border-radius: 8px;
  transition: color 0.2s;
}
.nav-link:hover { color: #F1F5F9; }
.btn-nav-ghost {
  color: #F1F5F9;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 8px 16px;
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 10px;
  transition: border-color 0.2s;
}
.btn-nav-ghost:hover { border-color: rgba(255,255,255,0.4); }
.btn-nav-primary {
  color: #fff;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  padding: 8px 18px;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  border-radius: 10px;
  transition: opacity 0.2s;
  display: inline-block;
}
.btn-nav-primary:hover { opacity: 0.85; }

/* Hamburger */
.mobile-menu-btn {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}
@media (max-width: 640px) {
  .mobile-menu-btn { display: flex; }
}
.hamburger-line {
  display: block;
  width: 22px;
  height: 2px;
  background: #94A3B8;
  border-radius: 2px;
  transition: transform 0.2s, opacity 0.2s;
}

/* Mobile Drawer */
.mobile-drawer {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 16px 24px 20px;
  border-top: 1px solid rgba(255,255,255,0.06);
  background: rgba(8,11,20,0.97);
}
.mobile-link {
  color: #94A3B8;
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  padding: 10px 0;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.mobile-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 12px;
}
</style>
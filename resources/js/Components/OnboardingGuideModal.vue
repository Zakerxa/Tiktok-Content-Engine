<!--
  First-visit onboarding guide.

  - Shows once per browser (via cookie), only when the "don't show again"
    checkbox was ticked on a previous close — otherwise it reappears next
    session, which is usually what you want for people who dismissed it
    by accident.
  - Swap the `slides` array's `image` paths for your real guide screenshots.
    Suggested location: /public/images/onboarding/slide-1.png etc.
  - Cookie name/duration are constants at the top — change ONBOARDING_COOKIE_DAYS
    if you want a different window than 14 days.
-->
<template>
  <transition name="onb-fade">
    <div
      v-if="visible"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70 p-3 backdrop-blur-sm sm:p-6"
      @click.self="close()"
    >
      <div
        class="flex w-full max-w-md flex-col overflow-hidden rounded-3xl border border-white/10 bg-[#0D1120] shadow-2xl sm:max-w-lg"
      >
        <!-- ═══ Slider ═══ -->
        <div class="relative">
          <audio ref="audioEl" class="hidden"></audio>

          <div class="absolute right-3 top-3 z-20 flex items-center gap-2">
            <!-- "tap to enable sound" hint — shows only if autoplay was blocked -->
            <span
              v-if="audioBlocked && !isMuted"
              class="rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-semibold text-white/80 backdrop-blur-md"
            >
              အသံဖွင့်ရန် နှိပ်ပါ
            </span>

            <button
              type="button"
              @click="toggleMute()"
              :aria-label="isMuted ? 'Unmute' : 'Mute'"
              class="flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white/80 backdrop-blur-md transition-colors hover:bg-black/60 hover:text-white"
            >
              <svg v-if="!isMuted" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
                <path d="M15.54 8.46a5 5 0 010 7.07M19.07 4.93a10 10 0 010 14.14" />
              </svg>
              <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" />
                <line x1="23" y1="9" x2="17" y2="15" />
                <line x1="17" y1="9" x2="23" y2="15" />
              </svg>
            </button>

            <button
              type="button"
              @click="close()"
              aria-label="Close"
              class="flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white/80 backdrop-blur-md transition-colors hover:bg-black/60 hover:text-white"
            >
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                <path d="M18 6L6 18M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div
            ref="carouselEl"
            class="no-scrollbar flex snap-x snap-mandatory overflow-x-auto scroll-smooth"
          >
            <div
              v-for="(slide, i) in slides"
              :key="i"
              :ref="el => setSlideRef(el, i)"
              class="w-full flex-shrink-0 snap-center"
            >
              <div class="aspect-[4/3] w-full bg-gradient-to-br from-[#161B2E] to-[#0D1120]">
                <img
                  :src="slide.image"
                  :alt="slide.title"
                  class="h-full w-full object-cover"
                  loading="lazy"
                />
              </div>
              <div class="px-6 pt-5 text-center sm:px-8">
                <h3 class="text-base font-bold text-[#F1F5F9] sm:text-lg">{{ slide.title }}</h3>
                <p class="mt-1.5 text-sm leading-relaxed text-[#94A3B8]">{{ slide.desc }}</p>
              </div>
            </div>
          </div>

          <!-- Prev / next arrows — desktop only, hidden on touch-first mobile -->
          <button
            v-if="slides.length > 1"
            type="button"
            @click="goTo(activeIndex - 1)"
            :disabled="activeIndex === 0"
            class="absolute left-2 top-[36%] z-10 hidden h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md transition-opacity hover:bg-black/60 disabled:pointer-events-none disabled:opacity-0 sm:flex"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6" /></svg>
          </button>
          <button
            v-if="slides.length > 1"
            type="button"
            @click="goTo(activeIndex + 1)"
            :disabled="activeIndex === slides.length - 1"
            class="absolute right-2 top-[36%] z-10 hidden h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-md transition-opacity hover:bg-black/60 disabled:pointer-events-none disabled:opacity-0 sm:flex"
          >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6" /></svg>
          </button>
        </div>

        <!-- Dots -->
        <div v-if="slides.length > 1" class="mt-4 flex items-center justify-center gap-1.5">
          <button
            v-for="(slide, i) in slides"
            :key="i"
            type="button"
            class="h-1.5 rounded-full transition-all duration-200"
            :class="activeIndex === i ? 'w-5 bg-violet-400' : 'w-1.5 bg-white/15'"
            :aria-label="`Slide ${i + 1}`"
            @click="goTo(i)"
          />
        </div>

        <!-- ═══ Footer ═══ -->
        <div class="mt-5 flex flex-col gap-4 border-t border-white/8 px-6 py-5 sm:px-8">
          <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-[#94A3B8] select-none">
            <input
              type="checkbox"
              v-model="dontShowAgain"
              class="h-4 w-4 shrink-0 rounded border-white/20 bg-white/5 text-violet-500 accent-violet-500"
            />
            ဒီလမ်းညွှန်ကို နောက်ထပ် မပြပါနှင့် (၁၄ ရက်)
          </label>

          <div class="flex items-center gap-3">
            <button
              v-if="!isLastSlide"
              type="button"
              @click="close()"
              class="rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] transition-colors hover:text-[#CBD5E1]"
            >
              ကျော်မည်
            </button>
            <button
              v-if="!isLastSlide"
              type="button"
              @click="goTo(activeIndex + 1)"
              class="ml-auto flex-1 rounded-xl bg-gradient-to-r from-[#7C3AED] to-[#06B6D4] py-2.5 text-center text-sm font-bold text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] transition-all hover:brightness-110 active:scale-[0.98]"
            >
              ရှေ့ဆက်မည်
            </button>
            <button
              v-else
              type="button"
              @click="close()"
              class="ml-auto flex-1 rounded-xl bg-gradient-to-r from-[#7C3AED] to-[#06B6D4] py-2.5 text-center text-sm font-bold text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] transition-all hover:brightness-110 active:scale-[0.98]"
            >
              စတင်အသုံးပြုမည် 🚀
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue';

// TODO: replace with your real guide screenshots (put them in /public/images/onboarding/)
// and your real narration clips (put them in /public/audio/onboarding/)
const slides = [
  {
    image: '/images/onboarding/slide-0.png',
    audio: '',
    title: 'ညာဘက်ကိုဆွဲပြီးကြည့်ပါ 👉',
    desc: 'Recap Video ပြုလုပ်ပုံအဆင့်ဆင့်ကိုလေ့လာပါ။',
  },
  {
    image: '/images/onboarding/slide-1.png',
    audio: '/music/onboarding/slide-1.wav',
    title: 'Video တင်ပါ',
    desc: 'File upload လုပ်ပြီး Video ကို စတင်တင်ပို့နိုင်ပါသည်။သင့် Role အလိုက် Video မိနစ်ကွဲပြားနိုင်တယ်။',
  },
  {
    image: '/images/onboarding/slide-2.png',
    audio: '/music/onboarding/slide-2.wav',
    title: 'Customize Blur Position',
    desc: 'နောက်ခံစာတန်းထိုးများကို Blur အုပ်၍ ကြိုက်နင့်သက်ရာနေရာကို လက်နဲ့ဆွဲ့ရွှေ့နိုင်သည်။',
  },
  {
    image: '/images/onboarding/slide-3.png',
    audio: '/music/onboarding/slide-3.wav',
    title: 'Voice နှင့် Subtitle ရွေးပါ',
    desc: 'AI Voice Over၊ Auto Subtitle color များကို သင့်စိတ်ကြိုက် customize လုပ်နိုင်ပါသည်။',
  },
  {
    image: '/images/onboarding/slide-4.png',
    audio: '/music/onboarding/slide-4.wav',
    title: 'Recap Video ထုတ်လုပ်ပါ',
    desc: '"Generate Recap Video" ကိုနှိပ်လိုက်ရုံဖြင့် AI က အလိုအလျောက် ဖန်တီးပေးပါလိမ့်မည်။',
  },
];

const ONBOARDING_COOKIE_NAME = 'onboarding_guide_dismissed';
const ONBOARDING_COOKIE_DAYS = 14;

function getCookie(name) {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

function setCookie(name, value, days) {
  const expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
  document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

const visible = ref(false);
const dontShowAgain = ref(false);
const activeIndex = ref(0);
const isLastSlide = computed(() => activeIndex.value === slides.length - 1);

const carouselEl = ref(null);
const slideRefs = ref([]);
let observer = null;

/* ─── Per-slide narration audio ─── */
const audioEl = ref(null);
const isMuted = ref(false);
const audioBlocked = ref(false);

function playSlideAudio(idx) {
  const el = audioEl.value;
  const src = slides[idx]?.audio;
  if (!el || !src) return;

  el.pause();
  el.currentTime = 0;
  el.src = src;
  audioBlocked.value = false;

  if (isMuted.value) return;

  // Browsers can block autoplay if this wasn't triggered by a direct user
  // gesture (e.g. the very first slide when the modal opens on its own).
  // Swallow the rejection and show a small "tap to enable sound" hint instead.
  el.play().catch(() => { audioBlocked.value = true; });
}

function toggleMute() {
  isMuted.value = !isMuted.value;
  if (!audioEl.value) return;
  if (isMuted.value) {
    audioEl.value.pause();
  } else {
    // Toggling is itself a user gesture, so this play() call is reliable
    // even if the earlier automatic one got blocked.
    audioEl.value.play().catch(() => { audioBlocked.value = true; });
  }
}

function stopAudio() {
  if (audioEl.value) {
    audioEl.value.pause();
    audioEl.value.currentTime = 0;
  }
}

function setSlideRef(el, i) {
  if (el) slideRefs.value[i] = el;
}

function goTo(i) {
  const clamped = Math.max(0, Math.min(slides.length - 1, i));
  const el = slideRefs.value[clamped];
  if (el) el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
  // Play immediately — this is a direct click, so it's a reliable user
  // gesture, unlike the swipe-driven IntersectionObserver update below.
  if (clamped !== activeIndex.value) {
    activeIndex.value = clamped;
    playSlideAudio(clamped);
  }
}

function close() {
  if (dontShowAgain.value) {
    setCookie(ONBOARDING_COOKIE_NAME, '1', ONBOARDING_COOKIE_DAYS);
  }
  stopAudio();
  visible.value = false;
}

onMounted(async () => {
  if (!getCookie(ONBOARDING_COOKIE_NAME)) {
    visible.value = true;
    playSlideAudio(0);
  }

  await nextTick();
  if (!carouselEl.value || !slideRefs.value.length) return;

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) {
          const idx = slideRefs.value.findIndex((el) => el === entry.target);
          if (idx !== -1 && idx !== activeIndex.value) {
            activeIndex.value = idx;
            playSlideAudio(idx);
          }
        }
      });
    },
    { root: carouselEl.value, threshold: [0.6] }
  );
  slideRefs.value.forEach((el) => el && observer.observe(el));
});

onBeforeUnmount(() => {
  if (observer) observer.disconnect();
  stopAudio();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.onb-fade-enter-active, .onb-fade-leave-active { transition: opacity 0.2s ease; }
.onb-fade-enter-from, .onb-fade-leave-to { opacity: 0; }
</style>
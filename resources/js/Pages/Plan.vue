<template>
  <Head title="Plan" />

  <AppSidebar :auth="$page.props.auth">
    <div class="dash-root">
      <!-- Ambient brand orbs (reused from AppSidebar's global styles) -->
      <div class="orb orb-violet"></div>
      <div class="orb orb-cyan"></div>
      <div class="orb orb-gold"></div>

      <main class="dash-main">

        <!-- ═══ Header ═══ -->
        <section class="plan-strip mt-10">
          <span class="inline-flex items-center gap-2 rounded-full border border-violet-500/30 bg-violet-500/10 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-violet-300">
            Your Plan
          </span>
          <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-50 sm:text-3xl">
            Manage your subscription
          </h1>
          <p class="mt-2 text-sm text-slate-400">
            You're currently on the <strong class="text-slate-200">{{ roleLabel }}</strong> plan.
            <template v-if="formattedExpiry">
              {{ isExpired ? 'It expired on' : 'It renews on' }}
              <strong class="text-slate-200">{{ formattedExpiry }}</strong>.
            </template>
            <template v-else>
              No expiry date on this plan.
            </template>
          </p>
        </section>

        <!-- ═══ Plan cards — mobile: swipeable carousel ═══ -->
        <section class="sm:hidden">
          <p class="mb-3 flex items-center gap-1.5 px-1 text-xs font-semibold text-slate-500">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="swipe-hint">
              <path d="M9 6l6 6-6 6" />
            </svg>
            Swipe to see other plans
          </p>

          <div
            ref="carouselEl"
            class="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pt-4 pb-2 pl-1 pr-10"
          >
            <div
              v-for="(plan, i) in orderedPlans"
              :key="plan.key"
              :ref="el => setCardRef(el, i)"
              class="w-[95%] flex-shrink-0 snap-center"
            >
              <PlanCard :plan="plan" :is-current="isCurrent(plan)" class="h-full" @upgrade="openTelegram" />
            </div>
          </div>

          <!-- Dot indicators -->
          <div class="mt-4 flex items-center justify-center gap-1.5">
            <button
              v-for="(plan, i) in orderedPlans"
              :key="plan.key"
              type="button"
              class="h-1.5 rounded-full transition-all duration-200"
              :class="activeIndex === i ? 'w-5 bg-violet-400' : 'w-1.5 bg-white/15'"
              :aria-label="`Go to ${plan.name} plan`"
              @click="scrollToCard(i)"
            />
          </div>
        </section>

        <!-- ═══ Plan cards — desktop / tablet: grid ═══ -->
        <section class="hidden sm:grid sm:grid-cols-2 items-start gap-5 xl:grid-cols-3">
          <PlanCard
            v-for="plan in orderedPlans"
            :key="plan.key"
            :plan="plan"
            :is-current="isCurrent(plan)"
            @upgrade="openTelegram"
          />
        </section>

        <!-- ═══ Help / contact strip ═══ -->
        <section class="rounded-3xl border border-white/10 bg-white/[0.03] p-7 text-center">
          <p class="text-sm text-slate-400">
            Need a custom plan, bulk generations, or have a payment question?
          </p>
          <button
            type="button"
            @click="openTelegram(null)"
            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-500 to-cyan-400 px-5 py-2.5 text-sm font-bold text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] transition-all duration-200 hover:brightness-110 active:scale-[0.98]"
          >
            Join Our Telegram Group 🚀
          </button>
        </section>

      </main>
    </div>
  </AppSidebar>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppSidebar from '@/Components/AppSidebar.vue';
import PlanCard from '@/Components/PlanCard.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});

// TODO: replace with your real Telegram group / support invite link
const TELEGRAM_URL = 'https://t.me/+6hc4y3AceQJmNTQ1';

const ROLE_LABELS = {
  tester: 'Tester',
  normal: 'Standard',
  pro: 'Pro',
  vip: 'VIP',
  admin: 'Admin',
};

const currentRole = computed(() => (user.value.role_name || 'tester').toLowerCase());
const roleLabel = computed(() => ROLE_LABELS[currentRole.value] || currentRole.value);

const formattedExpiry = computed(() => {
  if (!user.value.plan_expires_at) return null;
  return new Date(user.value.plan_expires_at).toLocaleDateString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric',
  });
});

const isExpired = computed(() => {
  if (!user.value.plan_expires_at) return false;
  return new Date(user.value.plan_expires_at) < new Date();
});

const plans = [
  {
    key: 'tester',
    name: 'Tester',
    icon: '🔥',
    price: 0,
    tagline: 'Try before you commit',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(148,163,184,0.18) 0%, transparent 70%)',
    features: [
      { label: '1 free generation/day', included: true },
      { label: 'Auto Subtitles (+1)', included: true },
      { label: 'AI Voice Over (+2)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min Video Max', included: true },
      { label: 'Copyright Protection (40%)', included: true, warn: true },
      { label: 'Low quality export', included: true, warn: true },
      { label: 'Low processing', included: true, warn: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    key: 'pro',
    name: 'Pro',
    icon: '👑',
    price: 1750,
    tagline: 'Priority rendering, built to scale',
    featured: true,
    glowColor: 'radial-gradient(circle, rgba(124,58,237,0.35) 0%, transparent 70%)',
    features: [
      { label: '3 generations/day', included: true },
      { label: 'Auto Subtitles (+8)', included: true },
      { label: 'AI Voice Over (+16)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '2 min 30s Video Max', included: true },
      { label: 'Copyright Protection (70%)', included: true },
      { label: 'High quality export', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: true },
    ],
  },
  {
    key: 'normal',
    name: 'Standard',
    icon: '⚡',
    price: 1000,
    tagline: 'Reliable, steady daily output',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 70%)',
    features: [
      { label: '3 generations/day', included: true },
      { label: 'Auto Subtitles (+8)', included: true },
      { label: 'AI Voice Over (+16)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min 30s Video Max', included: true },
      { label: 'Copyright Protection (70%)', included: true },
      { label: 'Standard quality export', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
];

// Roles that aren't one of the 3 paid tiers still need a "current plan" card,
// otherwise the carousel/grid never shows one for them.
const SPECIAL_PLANS = {
  admin: {
    key: 'admin',
    name: 'Admin',
    icon: '🛡️',
    price: 0,
    tagline: 'Full internal access — not a paid tier',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(56,189,248,0.28) 0%, transparent 70%)',
    features: [
      { label: 'Unlimited generations', included: true },
      { label: 'All subtitle & voice-over options', included: true },
      { label: 'No video length limit', included: true },
      { label: 'Full admin dashboard access', included: true },
    ],
  },
  vip: {
    key: 'vip',
    name: 'VIP',
    icon: '👑',
    price: 5000,
    tagline: 'Top priority, unlimited power',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.28) 0%, transparent 70%)',
    features: [
      { label: '5 generations/day', included: true },
      { label: 'Auto Subtitles (+8)', included: true },
      { label: 'AI Voice Over (+16)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '5 min Video Max', included: true },
      { label: 'Copyright Protection (70%)', included: true },
      { label: 'HD quality export', included: true },
      { label: 'Custom Watermark', included: true },
    ],
  },
};

// Current plan floats to the front — matters most on the mobile carousel,
// harmless on the desktop grid. Roles outside the 3 paid tiers (admin, vip)
// get a special card injected so there's always a "Current Plan" match.
const orderedPlans = computed(() => {
  const knownKeys = plans.map(p => p.key);
  const special = SPECIAL_PLANS[currentRole.value];
  const basePlans = (!knownKeys.includes(currentRole.value) && special)
    ? [special, ...plans]
    : plans;

  const current = basePlans.filter(p => p.key === currentRole.value);
  const rest = basePlans.filter(p => p.key !== currentRole.value);
  return [...current, ...rest];
});

function isCurrent(plan) {
  return plan.key === currentRole.value;
}

function openTelegram(plan) {
  // Optionally tag which plan the user was looking at, e.g. for support context.
  // const url = plan ? `${TELEGRAM_URL}?start=${plan.key}` : TELEGRAM_URL;
  window.open(TELEGRAM_URL, '_blank', 'noopener');
}

/* ─── Mobile carousel: track active slide + click-to-scroll dots ─── */
const carouselEl = ref(null);
const cardRefs = ref([]);
const activeIndex = ref(0);
let observer = null;

function setCardRef(el, i) {
  if (el) cardRefs.value[i] = el;
}

function scrollToCard(i) {
  const el = cardRefs.value[i];
  if (el) el.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
}

onMounted(async () => {
  await nextTick();
  if (!carouselEl.value || !cardRefs.value.length) return;

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) {
          const idx = cardRefs.value.findIndex((el) => el === entry.target);
          if (idx !== -1) activeIndex.value = idx;
        }
      });
    },
    { root: carouselEl.value, threshold: [0.6] }
  );

  cardRefs.value.forEach((el) => el && observer.observe(el));
});

onBeforeUnmount(() => {
  if (observer) observer.disconnect();
});
</script>

<style scoped>
.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 28px;
}

.plan-strip {
  align-items: start; justify-content: start;
  flex-wrap: wrap; gap: 20px;
  background: rgba(255,255,255,0.035);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px; padding: 28px 32px;
  backdrop-filter: blur(18px);
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.swipe-hint {
  animation: swipe-nudge 1.4s ease-in-out infinite;
}
@keyframes swipe-nudge {
  0%, 100% { transform: translateX(0); opacity: 0.6; }
  50% { transform: translateX(4px); opacity: 1; }
}
@media (prefers-reduced-motion: reduce) {
  .swipe-hint { animation: none; }
}
</style>
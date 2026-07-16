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

        <!-- ═══ Loading / error state (backend fetch) ═══ -->
        <p v-if="loading" class="px-1 text-sm text-slate-500">Loading plans…</p>
        <p v-else-if="error" class="px-1 text-sm text-rose-400">Couldn't load plans: {{ error }}</p>

        <!-- ═══ Plan cards — mobile: swipeable carousel ═══ -->
        <section v-else class="sm:hidden">
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
              <PlanCard
                :plan="plan"
                :is-current="isCurrent(plan)"
                :best-deal="bestDeal"
                :discounted-per-day="discountedPerDay(plan)"
                class="h-full"
                @upgrade="openTelegram"
              />
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
        <section v-if="!loading && !error" class="hidden sm:grid sm:grid-cols-2 items-start gap-5 xl:grid-cols-3">
          <PlanCard
            v-for="plan in orderedPlans"
            :key="plan.key"
            :plan="plan"
            :is-current="isCurrent(plan)"
            :best-deal="bestDeal"
            :discounted-per-day="discountedPerDay(plan)"
            @upgrade="openTelegram"
          />
        </section>

        <!-- ═══ Billing calculator — see cost by duration ═══ -->
        <section v-if="!loading && !error && payablePlans.length" class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 sm:p-7">
          <h2 class="text-base font-extrabold text-slate-50 sm:text-lg">ကျသင့်ငွေ တွက်ချက်ကြည့်ရန်</h2>
          <p class="mt-1 text-sm text-slate-400">ရက်အလိုက် ပက်ကေ့ချ်တွေရဲ့ ကျသင့်ငွေကို နှိုင်းယှဉ်ကြည့်ပါ။</p>

          <!-- Duration tabs -->
          <div class="mt-5 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
            <button
              v-for="d in durationOptions"
              :key="d.key"
              type="button"
              @click="selectedDurationKey = d.key"
              class="rounded-xl border px-3 py-2.5 text-center text-xs font-bold transition-all duration-200 active:scale-[0.98] sm:px-4 sm:text-left"
              :class="selectedDurationKey === d.key
                ? 'border-violet-500/50 bg-violet-500/15 text-violet-200'
                : 'border-white/10 bg-white/[0.03] text-slate-400 hover:border-white/20 hover:text-slate-200'"
            >
              {{ d.label }}
            </button>
          </div>

          <!-- Mobile: stacked cards -->
          <div class="mt-5 grid gap-3 sm:hidden">
            <div
              v-for="plan in payablePlans"
              :key="plan.key"
              class="rounded-2xl border border-white/10 bg-white/[0.025] p-4"
            >
              <div class="flex items-center justify-between gap-2">
                <span class="flex items-center gap-1.5 text-sm font-bold text-slate-200">
                  <span>{{ plan.icon }}</span> {{ plan.name }}
                </span>
                <span
                  class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide"
                  :class="selectedDuration.discountPercent > 0 ? 'bg-emerald-500/15 text-emerald-300' : 'bg-white/[0.05] text-slate-400'"
                >
                  {{ selectedDuration.discountPercent > 0 ? `${selectedDuration.discountPercent}% Off` : 'နဂိုဈေး' }}
                </span>
              </div>

              <div class="mt-3.5 flex items-center justify-between text-xs">
                <span class="text-slate-500">{{ selectedDuration.days }} ရက် — စုစုပေါင်း</span>
                <span class="text-[15px] font-black text-slate-50">{{ totalCost(plan).toLocaleString() }} MMK</span>
              </div>

              <div class="my-3 h-px w-full bg-white/[0.06]"></div>

              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">တစ်ရက်ပျမ်းမျှဈေး</span>
                <span class="flex items-baseline gap-1.5">
                  <span v-if="selectedDuration.discountPercent > 0" class="text-[11px] text-slate-500 line-through">
                    {{ plan.price.toLocaleString() }}
                  </span>
                  <span
                    class="text-[15px] font-black"
                    :class="selectedDuration.discountPercent > 0 ? 'text-emerald-400' : 'text-slate-200'"
                  >
                    {{ perDayCost(plan).toLocaleString() }} MMK
                  </span>
                </span>
              </div>
            </div>
          </div>

          <!-- Desktop / tablet: table -->
          <div class="mt-6 hidden overflow-x-auto sm:block">
            <table class="w-full min-w-[560px] border-collapse text-left text-sm">
              <thead>
                <tr class="border-b border-white/10 text-[11px] uppercase tracking-wide text-slate-500">
                  <th class="py-2 pr-4 font-semibold">အစီအစဉ် (Plan)</th>
                  <th class="py-2 pr-4 font-semibold">အသုံးပြုခွင့်ရက်</th>
                  <th class="py-2 pr-4 font-semibold">စုစုပေါင်းကျသင့်ငွေ</th>
                  <th class="py-2 pr-4 font-semibold">တစ်ရက်ပျမ်းမျှဈေး</th>
                  <th class="py-2 font-semibold">ရရှိမယ့် လျှော့ဈေး</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="plan in payablePlans" :key="plan.key" class="border-b border-white/5 last:border-0">
                  <td class="py-3.5 pr-4 font-semibold text-slate-200">{{ plan.icon }} {{ plan.name }}</td>
                  <td class="py-3.5 pr-4 text-slate-400">{{ selectedDuration.days }} ရက်</td>
                  <td class="py-3.5 pr-4 font-bold text-slate-50">{{ totalCost(plan).toLocaleString() }} MMK</td>
                  <td class="py-3.5 pr-4">
                    <span class="flex items-baseline gap-1.5">
                      <span v-if="selectedDuration.discountPercent > 0" class="text-xs text-slate-500 line-through">
                        {{ plan.price.toLocaleString() }}
                      </span>
                      <span
                        class="font-bold"
                        :class="selectedDuration.discountPercent > 0 ? 'text-emerald-400' : 'text-slate-300'"
                      >
                        {{ perDayCost(plan).toLocaleString() }} MMK
                      </span>
                    </span>
                  </td>
                  <td class="py-3.5">
                    <span
                      class="rounded-full px-2.5 py-1 text-[11px] font-bold"
                      :class="selectedDuration.discountPercent > 0 ? 'bg-emerald-500/15 text-emerald-300' : 'bg-white/[0.05] text-slate-400'"
                    >
                      {{ selectedDuration.discountPercent > 0 ? `${selectedDuration.discountPercent}% Off` : 'နဂိုဈေး' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
import { computed, ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
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

// Design/visual data only — price, tagline, and features all come live from
// the backend now. Keyed by role name so it merges onto whatever the API
// returns (tester / normal / pro / vip / admin).
const visualConfig = {
  tester: {
    icon: '🔥',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(148,163,184,0.18) 0%, transparent 70%)',
  },
  normal: {
    icon: '⚡',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 70%)',
  },
  pro: {
    icon: '👑',
    featured: true,
    glowColor: 'radial-gradient(circle, rgba(124,58,237,0.35) 0%, transparent 70%)',
  },
  vip: {
    icon: '👑',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.28) 0%, transparent 70%)',
  },
  admin: {
    icon: '🛡️',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(56,189,248,0.28) 0%, transparent 70%)',
  },
};

const plans = ref([]);
const loading = ref(true);
const error = ref(null);

// Best-deal duration shown on every card (matches plan_durations table: 30day / 40% off)
// TODO: once /api/pricing-plans returns plan_durations data, replace this
// static config with the actual duration row (label, days, discount_percent)
// from the backend so it stays in sync with the admin-editable table.
const bestDeal = {
  label: '30-Day Plan',
  days: 30,
  discountPercent: 40,
};

function regularTotal(plan) {
  if (!plan.price) return 0;
  return Math.round(plan.price * bestDeal.days);
}

function discountedTotal(plan) {
  if (!plan.price) return 0;
  return Math.round(regularTotal(plan) * (1 - bestDeal.discountPercent / 100));
}

function discountedPerDay(plan) {
  if (!plan.price) return 0;
  return Math.round(discountedTotal(plan) / bestDeal.days);
}

onMounted(async () => {
  try {
    const res = await fetch('/api/pricing-plans');
    if (!res.ok) throw new Error('Failed to load pricing plans');
    const data = await res.json();

    // Backend price/features/tagline data + frontend visual config merged,
    // same pattern as PricingSection.vue.
    plans.value = data.map(plan => ({
      ...plan,
      key: plan.name,
      name: ROLE_LABELS[plan.name] || plan.name,
      ...(visualConfig[plan.name] ?? {}),
    }));
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
});

// Current plan floats to the front — matters most on the mobile carousel,
// harmless on the desktop grid.
const orderedPlans = computed(() => {
  const current = plans.value.filter(p => p.key === currentRole.value);
  const rest = plans.value.filter(p => p.key !== currentRole.value);
  return [...current, ...rest];
});

// ─── Billing calculator: cost by duration ───
// Same discount-tier idea as `bestDeal` above, but as a full table so users
// can compare 1-day / weekly / 2+1 / monthly cost side by side.
// TODO: once /api/pricing-plans (or a /api/plan-durations endpoint) returns
// these tiers from the backend, replace this static list with the live data.
const durationOptions = [
  { key: 'day', label: '၃ ရက်စာ (Base)', days: 3, discountPercent: 10 },
  { key: 'week', label: '၁ ပတ်စာ (Weekly Pass)', days: 7, discountPercent: 20 },
  { key: 'twoPlusOne', label: '၂ ပတ်ဝယ် + ၁ ပတ်ရ (2+1 Pass)', days: 21, discountPercent: 33.3 },
  { key: 'month', label: '၃၀ ရက်စာ (Monthly Pass)', days: 30, discountPercent: 40 },
];

const selectedDurationKey = ref('week');
const selectedDuration = computed(
  () => durationOptions.find(d => d.key === selectedDurationKey.value) ?? durationOptions[0]
);

// Only paid plans belong in the cost table (Free/Admin have nothing to calculate).
const payablePlans = computed(() => plans.value.filter(p => p.price > 0));

function totalCost(plan, duration = selectedDuration.value) {
  if (!plan.price) return 0;
  return Math.round(plan.price * duration.days * (1 - duration.discountPercent / 100));
}

function perDayCost(plan, duration = selectedDuration.value) {
  if (!plan.price) return 0;
  return Math.round(totalCost(plan, duration) / duration.days);
}

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

// Cards only exist once the live plan data has loaded, so wait for `loading`
// to flip to false (instead of a plain onMounted) before wiring the observer.
watch(loading, async (isLoading) => {
  if (isLoading) return;
  await nextTick();
  if (!carouselEl.value || !cardRefs.value.length) return;

  if (observer) observer.disconnect();
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
}, { immediate: true });

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
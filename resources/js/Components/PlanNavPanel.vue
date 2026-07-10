<template>
  <div class="flex flex-col gap-1">
    <!-- ═══ "Plan" nav toggle ═══ -->
    <button
      type="button"
      @click="togglePanel"
      class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-left text-[13.5px] font-semibold transition-colors"
      :class="isOpen
        ? 'border border-[#7C3AED]/25 bg-[#7C3AED]/[0.15] text-[#C4B5FD]'
        : 'text-[#64748B] hover:bg-white/5 hover:text-[#CBD5E1]'"
    >
      <span class="flex w-5 flex-shrink-0 items-center justify-center">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2l2.6 5.9L21 9l-5 4.5L17.4 20 12 16.8 6.6 20 8 13.5 3 9l6.4-1.1L12 2z" />
        </svg>
      </span>
      <span class="flex-1">Plan</span>
      <span
        v-if="!isOpen"
        class="flex h-4 w-4 flex-shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-[9px] font-bold text-emerald-400"
        title="Current plan"
      >✓</span>
      <svg
        width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
        stroke-linecap="round" stroke-linejoin="round"
        class="flex-shrink-0 text-[#64748B] transition-transform duration-200"
        :class="isOpen ? 'rotate-180' : 'rotate-0'"
      >
        <polyline points="6 9 12 15 18 9" />
      </svg>
    </button>

    <!-- ═══ Plan panel ═══ -->
    <div v-if="isOpen" class="flex flex-col gap-2 pb-1 pl-1 pr-0.5 pt-1">
      <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-[#475569]">Your Plan</p>

      <p
        v-if="!knownPlan"
        class="rounded-xl border border-amber-500/25 bg-amber-500/[0.06] px-3 py-2 text-[11.5px] text-amber-300"
      >
        You're on the <strong>{{ roleLabel }}</strong> plan.
      </p>

      <div
        v-for="plan in plans"
        :key="plan.key"
        class="overflow-hidden rounded-xl border transition-colors"
        :class="[planShellClass(plan), isCurrentPlan(plan) ? 'ring-1 ring-emerald-400/40' : '']"
      >
        <!-- Collapsed row -->
        <button
          type="button"
          @click="togglePlan(plan.key)"
          class="flex w-full items-center justify-between gap-2 p-2.5"
        >
          <span class="flex min-w-0 items-center gap-2">
            <span class="text-sm leading-none">{{ plan.icon }}</span>
            <span class="flex min-w-0 flex-col items-start text-left">
              <span class="flex items-center gap-1.5 truncate text-xs font-bold text-[#E2E8F0]">
                {{ plan.name }}
                <span
                  v-if="isCurrentPlan(plan)"
                  class="flex h-3.5 w-3.5 items-center justify-center rounded-full bg-emerald-500/20 text-[9px] font-bold text-emerald-400"
                  title="Current plan"
                >✓</span>
              </span>
              <span class="text-[10px] text-[#64748B]">
                {{ plan.price === 0 ? 'Free' : plan.price.toLocaleString() + ' MMK/day' }}
              </span>
            </span>
          </span>
          <svg
            width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
            stroke-linecap="round" stroke-linejoin="round"
            class="flex-shrink-0 text-[#64748B] transition-transform duration-200"
            :class="expandedPlan === plan.key ? 'rotate-180' : 'rotate-0'"
          >
            <polyline points="6 9 12 15 18 9" />
          </svg>
        </button>

        <!-- Expanded features -->
        <div v-if="expandedPlan === plan.key" class="px-2.5 pb-2.5 pt-0.5">
          <p class="mb-1.5 text-[11px] text-[#64748B]">{{ plan.tagline }}</p>
          <ul class="flex flex-col gap-1.5">
            <li v-for="feat in plan.features" :key="feat.label" class="flex items-center gap-1.5 text-[11.5px]">
              <span
                class="flex h-3.5 w-3.5 shrink-0 items-center justify-center rounded-full text-[9px] font-bold"
                :class="!feat.included ? 'bg-white/[0.04] text-slate-600' : feat.warn ? 'bg-amber-500/15 text-amber-400' : 'bg-cyan-500/15 text-cyan-400'"
              >
                {{ feat.included ? '✓' : '✕' }}
              </span>
              <span :class="feat.included ? 'text-[#94A3B8]' : 'text-[#475569]'">{{ feat.label }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Upgrade CTA → Telegram -->
      <button
        type="button"
        @click="openTelegram"
        class="mt-1 w-full rounded-xl bg-gradient-to-r from-[#7C3AED] to-[#06B6D4] py-2.5 text-center text-xs font-bold text-white shadow-[0_4px_16px_-4px_rgba(124,58,237,0.5)] transition-transform duration-150 hover:brightness-110 active:scale-[0.98]"
      >
        Plan အဆင့်မြင်မည် 🚀
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  auth: { type: Object, required: true },
});

// TODO: replace with your real Telegram group invite link
const TELEGRAM_URL = 'https://t.me/your_group_here';

const isOpen = ref(false);
const expandedPlan = ref(null);

const currentRole = computed(() => (props.auth?.user?.role_name || 'tester').toLowerCase());

const ROLE_LABELS = {
  tester: 'Tester',
  normal: 'Normal',
  pro: 'Pro',
  vip: 'VIP',
  admin: 'Admin',
};
const roleLabel = computed(() => ROLE_LABELS[currentRole.value] || currentRole.value);
const knownPlan = computed(() => ['tester', 'normal', 'pro'].includes(currentRole.value));

const plans = [
  {
    key: 'tester',
    name: 'Tester',
    icon: '🔥',
    price: 0,
    tagline: 'Try before you commit',
    accent: 'slate',
    features: [
      { label: '1 free generation/day', included: true },
      { label: 'Auto Subtitles (+1)', included: true },
      { label: 'AI Voice Over (+2)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min Video Max', included: true },
      { label: 'Copyright Protection (40%)', included: true, warn: true },
      { label: 'Low quality export', included: true, warn: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    key: 'normal',
    name: 'Normal',
    icon: '⚡',
    price: 1000,
    tagline: 'Reliable, steady daily output',
    accent: 'amber',
    features: [
      { label: '3 generations/day', included: true },
      { label: 'Auto Subtitles (+8)', included: true },
      { label: 'AI Voice Over (+16)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min 30s Video Max', included: true },
      { label: 'Copyright Protection (70%)', included: true },
      { label: 'Standard quality export', included: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    key: 'pro',
    name: 'Pro',
    icon: '👑',
    price: 1500,
    tagline: 'Priority rendering, built to scale',
    accent: 'violet',
    features: [
      { label: '3 generations/day', included: true },
      { label: 'Auto Subtitles (+8)', included: true },
      { label: 'AI Voice Over (+16)', included: true },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '2 min 30s Video Max', included: true },
      { label: 'Copyright Protection (70%)', included: true },
      { label: 'High quality export', included: true },
      { label: 'Custom Watermark', included: true },
    ],
  },
];

function togglePanel() {
  isOpen.value = !isOpen.value;
  if (!isOpen.value) expandedPlan.value = null;
}

function togglePlan(key) {
  expandedPlan.value = expandedPlan.value === key ? null : key;
}

function isCurrentPlan(plan) {
  return plan.key === currentRole.value;
}

function planShellClass(plan) {
  if (plan.accent === 'violet') return 'border-[#7C3AED]/30 bg-[#7C3AED]/[0.06]';
  if (plan.accent === 'amber') return 'border-amber-500/25 bg-amber-500/[0.04]';
  return 'border-white/10 bg-white/[0.03]';
}

function openTelegram() {
  window.open(TELEGRAM_URL, '_blank', 'noopener');
}
</script>
<template>
  <div
    class="group relative flex h-full flex-col rounded-3xl border p-7 transition-all duration-300 hover:-translate-y-1"
    :class="shellClass"
  >
    <!-- Ribbon: current plan takes priority over "most popular" -->
    <div
      v-if="isCurrent"
      class="absolute right-6 top-0 -translate-y-1/2 rounded-full bg-emerald-500 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-[0_4px_16px_-2px_rgba(16,185,129,0.5)]"
    >
      Current Plan
    </div>
    <div
      v-else-if="plan.featured"
      class="absolute right-6 top-0 -translate-y-1/2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-400 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-[0_4px_16px_-2px_rgba(124,58,237,0.45)]"
    >
      Most Popular
    </div>

    <!-- Glow -->
    <div
      class="pointer-events-none absolute -top-10 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full opacity-60 blur-[42px] transition-opacity duration-300 group-hover:opacity-90"
      :style="{ background: plan.glowColor }"
    ></div>

    <!-- Header -->
    <div class="relative">
      <span class="block text-[28px] leading-none">{{ plan.icon }}</span>
      <h3 class="mt-3 text-xl font-extrabold text-slate-50">{{ plan.name }}</h3>
      <div class="mt-3 flex items-baseline gap-1.5">
        <span v-if="plan.price === 0" class="text-[32px] font-black tracking-tight text-cyan-400">Free</span>
        <template v-else>
          <span class="text-[15px] line-through tracking-tight font-medium text-slate-500">{{ plan.price.toLocaleString() }} MMK/day</span>
        </template>
      </div>

      <!-- Discount label (best-deal duration, e.g. 30-Day Plan · 40% Off) -->
      <div v-if="plan.price > 0 && bestDeal" class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1">
        <span class="text-[24px] font-black tracking-tight text-slate-50">{{ discountedPerDay.toLocaleString() }}</span>
        <span class="text-[13px] font-medium text-slate-500">MMK<small class="text-[11px]">/day</small></span>
        <span class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-300">
          {{ bestDeal.label }} · {{ bestDeal.discountPercent }}% Off
        </span>
      </div>

      <p class="mt-1.5 text-[15px] text-slate-500">{{ plan.tagline }}</p>
    </div>

    <!-- Divider -->
    <div class="my-6 h-px w-full bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

    <!-- Features -->
    <ul class="flex flex-1 flex-col gap-2.5">
      <li v-for="feat in plan.features" :key="feat.label" class="flex items-center gap-2.5 text-sm">
        <span
          class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
          :class="featIconClass(feat)"
        >
          {{ feat.included ? '✓' : '✕' }}
        </span>
        <span class="text-slate-400">{{ feat.label }}</span>
      </li>
    </ul>

    <!-- CTA -->
    <button
      v-if="isCurrent"
      type="button"
      disabled
      class="relative mt-8 block w-full cursor-default rounded-xl border border-emerald-500/30 bg-emerald-500/10 py-3.5 text-center text-sm font-bold text-emerald-300"
    >
      ✓ Current Plan
    </button>
    <button
      v-else
      type="button"
      @click="$emit('upgrade', plan)"
      class="relative mt-8 block w-full rounded-xl py-3.5 text-center text-sm font-bold transition-all duration-200 active:scale-[0.98]"
      :class="ctaClass"
    >
      Upgrade via Telegram →
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  plan: { type: Object, required: true },
  isCurrent: { type: Boolean, default: false },
  bestDeal: { type: Object, default: null },
  discountedPerDay: { type: Number, default: 0 },
});

defineEmits(['upgrade']);

const shellClass = computed(() => {
  if (props.isCurrent) {
    return 'border-emerald-500/40 bg-emerald-500/[0.05] shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_28px_56px_-24px_rgba(16,185,129,0.35)]';
  }
  if (props.plan.featured) {
    return 'border-violet-500/50 bg-violet-500/[0.07] shadow-[0_0_0_1px_rgba(124,58,237,0.15),0_28px_56px_-24px_rgba(124,58,237,0.4)]';
  }
  return 'border-white/10 bg-white/[0.025] hover:border-white/20';
});

const ctaClass = computed(() => {
  if (props.plan.featured) {
    return 'bg-gradient-to-r from-violet-500 to-cyan-400 text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] hover:shadow-[0_10px_28px_-8px_rgba(124,58,237,0.75)] hover:brightness-110';
  }
  return 'border border-white/10 bg-white/[0.05] text-slate-300 hover:bg-white/[0.09] hover:text-white';
});

function featIconClass(feat) {
  if (!feat.included) return 'bg-white/[0.04] text-slate-700';
  if (feat.type === 'warning') return 'bg-amber-500/15 text-amber-400';
  return 'bg-cyan-500/15 text-cyan-400';
}
</script>
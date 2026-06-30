<template>
  <section id="pricing" class="relative overflow-hidden py-24 sm:py-28 lg:py-32">


    <div class="relative mx-auto max-w-7xl px-5 sm:px-8">
      <!-- Header -->
      <div class="mx-auto max-w-3xl text-center pb-20">
        <span class="inline-flex items-center gap-2 rounded-full border border-violet-500/30 bg-violet-500/10 px-3.5 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-violet-300">
          Pricing Plans
        </span>
        <h2 class="mt-5 text-balance text-3xl font-extrabold leading-tight tracking-tight text-slate-50 sm:text-4xl lg:text-[44px]">
          Start free, scale as your channel grows
        </h2>
        <p class="mt-4 text-balance text-[15px] leading-relaxed text-slate-400">
          Every plan unlocks both <span class="text-slate-300">Movie Recap Studio</span> and <span class="text-slate-300">Blog Generator</span> — pay only for speed, length, and quality.
        </p>
      </div>

      <!-- Cards -->
      <div class="mt-16 grid grid-cols-1 items-start gap-5 sm:grid-cols-2 xl:grid-cols-4 xl:pb-6">
        <div
          v-for="plan in plans"
          :key="plan.name"
          class="group relative flex flex-col rounded-3xl border p-7 transition-all duration-300 hover:-translate-y-1.5 sm:p-8"
          :class="[cardShellClass(plan), liftClass(plan)]"
        >
          <!-- Popular ribbon -->
          <div
            v-if="plan.featured"
            class="absolute right-6 top-0 -translate-y-1/2 rounded-full bg-gradient-to-r from-violet-500 to-cyan-400 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white shadow-[0_4px_16px_rgba(124,58,237,0.45)]"
          >
            Most Popular
          </div>

          <!-- Glow -->
          <div
            class="pointer-events-none absolute -top-10 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full opacity-70 blur-[42px] transition-opacity duration-300 group-hover:opacity-100"
            :style="{ background: plan.glowColor }"
          ></div>

          <!-- Header -->
          <div class="relative">
            <span class="block text-[28px] leading-none">{{ plan.icon }}</span>
            <h3 class="mt-3 text-xl font-extrabold text-slate-50">{{ plan.name }}</h3>

            <div class="mt-3 flex items-baseline gap-1.5">
              <span v-if="plan.price === 0" class="text-[32px] font-black tracking-tight text-cyan-400">Free</span>
              <template v-else>
                <span class="text-[32px] font-black tracking-tight text-slate-50">{{ plan.price.toLocaleString() }}</span>
                <span class="text-[13px] font-medium text-slate-500">MMK<small class="text-[11px]">/day</small></span>
              </template>
            </div>
            <p class="mt-1.5 text-[15px] text-slate-500">{{ plan.tagline }}</p>
            <span
              v-if="plan.priorityLabel"
              class="mt-3 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold"
              :class="plan.vip ? 'bg-amber-500/15 text-amber-300' : 'bg-violet-500/15 text-violet-300'"
            >
              {{ plan.priorityLabel }}
            </span>
          </div>

          <!-- Divider -->
          <div class="my-6 h-px w-full bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

          <!-- Features -->
          <ul class="flex flex-1 flex-col gap-2.5">
            <li
              v-for="feat in plan.features"
              :key="feat.label"
              class="flex items-center gap-2.5 text-sm"
            >
              <span
                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[11px] font-bold"
                :class="featIconClass(feat)"
              >
                {{ feat.included ? '✓' : '✕' }}
              </span>
              <span
                class="text-slate-400"
                :class="{ 'cursor-help  border-slate-600 text-slate-300': feat.tooltip }"
                :title="feat.tooltip"
              >
                {{ feat.label }}
              </span>
            </li>
          </ul>

          <!-- CTA -->
          <a :href="plan.href" class="relative mt-8 block rounded-xl py-3.5 text-center text-sm font-bold transition-all duration-200 active:scale-[0.98]" :class="ctaClass(plan)">
            {{ plan.cta }}
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const plans = [
  {
    name: 'Tester',
    icon: '🧪',
    price: 0,
    tagline: 'Try before you commit',
    priorityLabel: null,
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(148,163,184,0.18) 0%, transparent 70%)',
    href: '/dashboard',
    cta: 'Start Free',
    features: [
      { label: '1 free generation/day', included: true, tooltip: 'Upgrade for more generation' },
      { label: 'Auto Subtitles (+1 )', included: true },
      { label: 'AI Voice Over (+2 )', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '40sec Video Max', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'Copyright Protection (50%)', included: true, type: 'warning', tooltip: 'Upgrade for longer durations' },
      { label: 'Low quality export', included: true, type: 'warning' },
      { label: 'Low processing', included: true, type: 'warning' },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    name: 'Normal',
    icon: '⚡',
    price: 2000,
    tagline: 'Reliable, steady daily output',
    priorityLabel: null,
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(6,182,212,0.22) 0%, transparent 70%)',
    href: '/dashboard',
    cta: 'Get Normal',
    features: [
      { label: '2 generations/day', included: true, tooltip: 'Upgrade for more generation' },
      { label: 'Auto Subtitles (+8 )', included: true },
      { label: 'AI Voice Over (+16 )', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min Video Max', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'Copyright Protection (70%)', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'Standard quality export', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    name: 'Pro',
    icon: '🔥',
    price: 3000,
    tagline: 'Priority rendering, built to scale',
    priorityLabel: '⚡ Priority Queue',
    featured: true,
    glowColor: 'radial-gradient(circle, rgba(124,58,237,0.35) 0%, transparent 70%)',
    href: '/dashboard',
    cta: 'Go Pro',
    features: [
      { label: '3 generations/day', included: true, tooltip: 'Upgrade for more generation' },
      { label: 'Auto Subtitles (+8 )', included: true },
      { label: 'AI Voice Over (+16 )', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1 min 45s Video Max', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'Copyright Protection (70%)', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'High quality export', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: true },
    ],
  },
  {
    name: 'VIP',
    icon: '👑',
    price: 5000,
    tagline: 'Top priority, unlimited power',
    priorityLabel: '👑 Top Priority',
    featured: false,
    vip: true,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.22) 0%, transparent 70%)',
    href: '/auth/google',
    cta: 'Become VIP',
    features: [
      { label: '5 generations/day', included: true },
      { label: 'Auto Subtitles (+8 )', included: true },
      { label: 'AI Voice Over (+16 )', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '2 min Video length max', included: true },
      { label: 'Copyright Protection (70%)', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'HD quality export', included: true },
      { label: 'Fast processing', included: true },
      { label: 'Custom Watermark Position', included: true },
    ],
  },
];

function cardShellClass(plan) {
  if (plan.featured) {
    return 'border-violet-500/50 bg-violet-500/[0.07] shadow-[0_0_0_1px_rgba(124,58,237,0.15),0_28px_56px_-24px_rgba(124,58,237,0.4)] z-10';
  }
  if (plan.vip) {
    return 'border-amber-500/25 bg-amber-500/[0.04] hover:border-amber-500/40';
  }
  return 'border-white/10 bg-white/[0.025] hover:border-white/20';
}

// Podium-style elevation: Pro sits highest (top priority), VIP just under it,
// Normal and Tester step down further — only applied from xl: up so mobile/tablet stay flush.
function liftClass(plan) {
  if (plan.featured) return 'xl:-translate-y-5 xl:scale-[1.04]';
  if (plan.vip) return 'xl:-translate-y-2';
  if (plan.name === 'Normal') return 'xl:translate-y-1';
  return 'xl:translate-y-3'; // Tester
}

function featIconClass(feat) {
  if (!feat.included) return 'bg-white/[0.04] text-slate-700';
  if (feat.type === 'warning') return 'bg-amber-500/15 text-amber-400';
  return 'bg-cyan-500/15 text-cyan-400';
}

function ctaClass(plan) {
  if (plan.featured) {
    return 'bg-gradient-to-r from-violet-500 to-cyan-400 text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] hover:shadow-[0_10px_28px_-8px_rgba(124,58,237,0.75)] hover:brightness-110';
  }
  if (plan.vip) {
    return 'border border-amber-500/30 bg-amber-500/10 text-amber-300 hover:bg-amber-500/15';
  }
  return 'border border-white/10 bg-white/[0.05] text-slate-300 hover:bg-white/[0.09] hover:text-white';
}
</script>
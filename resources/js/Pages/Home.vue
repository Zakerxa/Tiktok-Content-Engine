<template>
  <div class="bg-[#080B14] text-[#F1F5F9] font-[Inter,_Segoe_UI,_sans-serif] min-h-screen overflow-x-hidden">

    <AppNavbar :auth="auth" />

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="relative min-h-screen flex flex-col items-center justify-center pt-[120px] px-6 pb-20 overflow-hidden text-center">
      <div class="absolute rounded-full blur-[80px] pointer-events-none w-[600px] h-[600px] bg-[rgba(124,58,237,0.25)] -top-[100px] -left-[150px]"></div>
      <div class="absolute rounded-full blur-[80px] pointer-events-none w-[500px] h-[500px] bg-[rgba(6,182,212,0.18)] top-[100px] -right-[100px]"></div>
      <div class="absolute rounded-full blur-[80px] pointer-events-none w-[300px] h-[300px] bg-[rgba(245,158,11,0.12)] bottom-0 left-[40%]"></div>

      <div class="absolute left-0 right-0 h-[28px] bg-[rgba(255,255,255,0.025)] border-y border-[rgba(255,255,255,0.06)] flex items-center gap-6 px-6 overflow-hidden top-16 -left-[240px]">
        <div class="flex gap-7 w-max animate-[film-top_18s_linear_infinite] motion-reduce:animate-none">
          <span v-for="n in 27" :key="`a${n}`" class="flex-shrink-0 w-[14px] h-[14px] rounded-[3px] bg-[rgba(255,255,255,0.07)] border border-[rgba(255,255,255,0.1)]"></span>
          <span v-for="n in 27" :key="`b${n}`" class="flex-shrink-0 w-[14px] h-[14px] rounded-[3px] bg-[rgba(255,255,255,0.07)] border border-[rgba(255,255,255,0.1)]"></span>
        </div>
      </div>

      <div class="relative z-10 max-w-[780px]">
        <div class="inline-flex items-center gap-2 bg-[rgba(124,58,237,0.15)] border border-[rgba(124,58,237,0.35)] text-[#A78BFA] text-[13px] font-medium px-4 py-1.5 rounded-full mb-8">
          <span class="w-[7px] h-[7px] bg-[#7C3AED] rounded-full animate-[badge-pulse_2s_infinite]"></span>
          AI-Powered Content Studio · Myanmar
        </div>

        <h1 class="text-[clamp(40px,7vw,80px)] font-black leading-[1.05] tracking-[-2px] text-[#F1F5F9] mb-6">
          Create. Protect.<br/>
          <span class="bg-gradient-to-br from-[#7C3AED] via-[#06B6D4] to-[#F59E0B] bg-clip-text text-transparent">Dominate TikTok.</span>
        </h1>

        <p class="text-[18px] text-[#94A3B8] leading-[1.7] max-w-[600px] mx-auto mb-10">
          One-click movie recaps with copyright protection, custom blur &amp; watermark.
          Plus free AI-generated TikTok content with image prompts — all in one platform.
        </p>

        <div class="flex gap-4 justify-center flex-wrap mb-14">
          <a href="/movie-recap" class="inline-flex items-center gap-2.5 bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] text-white no-underline text-base font-bold px-8 py-4 rounded-[14px] shadow-[0_0_40px_rgba(124,58,237,0.4)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_0_60px_rgba(124,58,237,0.6)]">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Start Movie Recap
          </a>
          <a href="/blogs" class="inline-flex items-center gap-2.5 bg-[rgba(255,255,255,0.06)] text-[#F1F5F9] no-underline text-base font-semibold px-8 py-4 rounded-[14px] border border-[rgba(255,255,255,0.12)] transition-all duration-200 hover:bg-[rgba(255,255,255,0.1)] hover:border-[rgba(255,255,255,0.25)]">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Free TikTok Content
          </a>
        </div>

        <div class="flex items-center justify-center gap-8 flex-wrap">
          <div class="flex flex-col items-center gap-0.5">
            <span class="text-[26px] font-extrabold text-[#F1F5F9] tracking-[-0.5px]">70%</span>
            <span class="text-xs text-[#64748B] font-medium uppercase tracking-[0.5px]">Copyright Safe</span>
          </div>
          <div class="w-px h-10 bg-[rgba(255,255,255,0.1)]"></div>
          <div class="flex flex-col items-center gap-0.5">
            <span class="text-[26px] font-extrabold text-[#F1F5F9] tracking-[-0.5px]">1/Click</span>
            <span class="text-xs text-[#64748B] font-medium uppercase tracking-[0.5px]">Movie Recap</span>
          </div>
          <div class="w-px h-10 bg-[rgba(255,255,255,0.1)]"></div>
          <div class="flex flex-col items-center gap-0.5">
            <span class="text-[26px] font-extrabold text-[#F1F5F9] tracking-[-0.5px]">Free</span>
            <span class="text-xs text-[#64748B] font-medium uppercase tracking-[0.5px]">Blog Generator</span>
          </div>
        </div>
      </div>

      <div class="absolute left-0 right-0 h-[28px] bg-[rgba(255,255,255,0.025)] border-y border-[rgba(255,255,255,0.06)] flex items-center gap-6 px-6 overflow-hidden bottom-0">
        <div class="flex gap-7 w-max animate-[film-scroll_18s_linear_infinite] motion-reduce:animate-none">
          <span v-for="n in 24" :key="`a${n}`" class="flex-shrink-0 w-[14px] h-[14px] rounded-[3px] bg-[rgba(255,255,255,0.07)] border border-[rgba(255,255,255,0.1)]"></span>
          <span v-for="n in 24" :key="`b${n}`" class="flex-shrink-0 w-[14px] h-[14px] rounded-[3px] bg-[rgba(255,255,255,0.07)] border border-[rgba(255,255,255,0.1)]"></span>
        </div>
      </div>
    </section>

    <!-- ═══════════════ PRODUCTS ═══════════════ -->
    <section class="py-[120px] bg-gradient-to-b from-[#080B14] via-[#0D1120] to-[#080B14]">
      <div class="max-w-[1200px] mx-auto px-6 lg:max-w-[1080px]">
        <div class="text-center text-xs font-bold uppercase tracking-[2px] text-[#7C3AED] mb-3">Two Powerful Tools</div>
        <h2 class="text-center text-[clamp(28px,4vw,44px)] font-extrabold text-[#F1F5F9] tracking-[-1px] mb-4">Everything Your Channel Needs</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-[6em] mt-16">
          <div class="relative bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-3xl p-10 overflow-hidden transition-all duration-300 hover:border-[rgba(255,255,255,0.15)] hover:-translate-y-1">
            <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full blur-[50px] pointer-events-none bg-[rgba(124,58,237,0.4)]"></div>
            <div class="w-14 h-14 bg-[rgba(255,255,255,0.06)] rounded-2xl flex items-center justify-center text-[26px] mb-5">🎬</div>
            <h3 class="text-2xl font-extrabold text-[#F1F5F9] mb-3 tracking-[-0.5px]">Movie Recap Studio</h3>
            <p class="text-[15px] text-[#64748B] leading-[1.7] mb-7">
              Turn any movie into a viral TikTok recap in seconds. Our AI handles copyright protection automatically so you can focus on growing your audience.
            </p>
            <ul class="list-none p-0 m-0 mb-8 flex flex-col gap-2.5">
              <li v-for="feat in recapFeatures" :key="feat.label" class="flex items-center gap-2.5 text-sm">
                <span
                  class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold flex-shrink-0"
                  :class="feat.available ? 'bg-[rgba(6,182,212,0.15)] text-[#06B6D4]' : 'bg-[rgba(255,255,255,0.05)] text-[#475569]'"
                >{{ feat.available ? '✓' : '○' }}</span>
                <span class="text-[#CBD5E1] flex-1">{{ feat.label }}</span>
                <span v-if="feat.badge" class="text-[10px] font-bold bg-[rgba(245,158,11,0.15)] text-[#F59E0B] px-2 py-0.5 rounded-full border border-[rgba(245,158,11,0.3)]">{{ feat.badge }}</span>
              </li>
            </ul>
            <a href="/dashboard/recap" class="inline-block no-underline text-[15px] font-bold py-3.5 px-7 rounded-xl transition-all duration-200 hover:opacity-85 hover:-translate-y-px bg-gradient-to-br from-[#7C3AED] to-[#9333EA] text-white">Launch Recap Studio →</a>
          </div>

          <div class="relative bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-3xl p-10 overflow-hidden transition-all duration-300 hover:border-[rgba(255,255,255,0.15)] hover:-translate-y-1">
            <div class="absolute -top-[60px] -right-[60px] w-[200px] h-[200px] rounded-full blur-[50px] pointer-events-none bg-[rgba(6,182,212,0.35)]"></div>
            <div class="w-14 h-14 bg-[rgba(255,255,255,0.06)] rounded-2xl flex items-center justify-center text-[26px] mb-5">✍️</div>
            <h3 class="text-2xl font-extrabold text-[#F1F5F9] mb-3 tracking-[-0.5px]">TikTok Blog Generator</h3>
            <p class="text-[15px] text-[#64748B] leading-[1.7] mb-7">
              Generate scroll-stopping TikTok content with matching image prompts. Add your own API key for fully custom content tailored to your niche.
            </p>
            <ul class="list-none p-0 m-0 mb-8 flex flex-col gap-2.5">
              <li v-for="feat in blogFeatures" :key="feat.label" class="flex items-center gap-2.5 text-sm">
                <span
                  class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold flex-shrink-0"
                  :class="feat.available ? 'bg-[rgba(6,182,212,0.15)] text-[#06B6D4]' : 'bg-[rgba(255,255,255,0.05)] text-[#475569]'"
                >{{ feat.available ? '✓' : '○' }}</span>
                <span class="text-[#CBD5E1] flex-1">{{ feat.label }}</span>
                <span v-if="feat.badge" class="text-[10px] font-bold bg-[rgba(245,158,11,0.15)] text-[#F59E0B] px-2 py-0.5 rounded-full border border-[rgba(245,158,11,0.3)]">{{ feat.badge }}</span>
              </li>
            </ul>
            <a href="/blogs" class="inline-block no-underline text-[15px] font-bold py-3.5 px-7 rounded-xl transition-all duration-200 hover:opacity-85 hover:-translate-y-px bg-gradient-to-br from-[#0891B2] to-[#06B6D4] text-white">Explore Free Blogs →</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ HOW IT WORKS ═══════════════ -->
    <section class="py-[120px] bg-[#080B14]">
      <div class="max-w-[1200px] mx-auto px-6">
        <div class="text-center text-xs font-bold uppercase tracking-[2px] text-[#7C3AED] mb-3">Workflow</div>
        <h2 class="text-center text-[clamp(28px,4vw,44px)] font-extrabold text-[#F1F5F9] tracking-[-1px] mb-4">From Idea to Viral — Fast</h2>
        <div class="grid grid-cols-1 min-[481px]:grid-cols-2 min-[901px]:grid-cols-4 gap-5 mt-16">
          <div v-for="(step, i) in steps" :key="i" class="bg-[rgba(255,255,255,0.02)] border border-[rgba(255,255,255,0.06)] rounded-[20px] py-8 px-6">
            <div class="text-[32px] mb-4">{{ step.icon }}</div>
            <h4 class="text-[17px] font-bold text-[#E2E8F0] mb-2.5">{{ step.title }}</h4>
            <p class="text-[13px] text-[#64748B] leading-[1.65]">{{ step.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ PRICING ═══════════════ -->
    <PricingSection />

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <AppFooter />

  </div>
</template>

<script setup>
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';
import PricingSection from '@/Components/PricingSection.vue';

defineProps({
  auth: Object,
});

const recapFeatures = [
  { label: 'Auto Subtitles (+7 Country )',                available: true },
  { label: 'Voice Over (+14 )',                    available: true },
  { label: 'Custom Watermark',              available: true },
  { label: 'Custom Blur & Mosaic',          available: true },
  { label: 'Copyright Protection', available: true, badge: '70% Safe' },
  { label: 'HD Video Export',               available: true },
  { label: '1 Click Movie Recap',       available: true },
];

const blogFeatures = [
  { label: 'Free TikTok Content Posts',        available: true },
  { label: 'AI Image Prompts',              available: true },
  { label: 'Burmese Language Support',      available: true },
  { label: 'Custom API Key Integration',    available: true, badge: 'Own Key' },
  { label: 'Topic-based Generation',        available: true },
  { label: 'Script & Hook Templates',       available: true },
  { label: 'Unlimited Browsing',            available: true },
];

const steps = [
  { icon: '🔗', title: 'Choose Your Content',  desc: 'Pick a movie for recap or select a TikTok blog topic from our library.' },
  { icon: '⚙️', title: 'AI Does the Work',     desc: 'Our engine generates scripts, applies protection layers, and creates image prompts automatically.' },
  { icon: '🎨', title: 'Customize Output',     desc: 'Add your watermark, adjust blur intensity, choose subtitle style and voice over tone.' },
  { icon: '🚀', title: 'Download & Post',      desc: 'Export your ready-to-post content and watch your TikTok audience grow.' },
];
</script>

<style>
/*
  Tailwind has no utility for declaring brand-new @keyframes, so these three
  stay as plain CSS. Everything else has been moved into Tailwind classes
  in the template above. They're referenced via Tailwind's arbitrary
  animation syntax, e.g. animate-[film-scroll_18s_linear_infinite].
*/
@keyframes film-top {
  from { transform: translateX(0); }
  to   { transform: translateX(50%); }
}

@keyframes film-scroll {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}

@keyframes badge-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.5; transform: scale(0.8); }
}
</style>
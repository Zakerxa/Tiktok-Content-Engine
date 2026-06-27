<template>
  <div class="home-root">

    <AppNavbar :auth="auth" />

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="hero-section">
      <div class="orb orb-violet"></div>
      <div class="orb orb-cyan"></div>
      <div class="orb orb-gold"></div>

      <div class="film-strip film-strip-top">
  <div class="film-track-top">
    <span v-for="n in 27" :key="`a${n}`" class="film-hole"></span>
    <span v-for="n in 27" :key="`b${n}`" class="film-hole"></span>
  </div>
</div>

      <div class="hero-content">
        <div class="hero-badge">
          <span class="badge-dot"></span>
          AI-Powered Content Studio · Myanmar
        </div>

        <h1 class="hero-title">
          Create. Protect.<br/>
          <span class="gradient-text">Dominate TikTok.</span>
        </h1>

        <p class="hero-sub">
          One-click movie recaps with copyright protection, custom blur &amp; watermark.
          Plus free AI-generated TikTok content with image prompts — all in one platform.
        </p>

        <div class="hero-ctas">
          <a href="/recap" class="cta-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Start Movie Recap
          </a>
          <a href="/blogs" class="cta-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Free TikTok Content
          </a>
        </div>

        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-num">70%</span>
            <span class="stat-label">Copyright Safe</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-num">1/Click</span>
            <span class="stat-label">Movie Recap</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-num">Free</span>
            <span class="stat-label">Blog Generator</span>
          </div>
        </div>
      </div>

      <div class="film-strip film-strip-bottom">
        <div class="film-track">
          <span v-for="n in 24" :key="`a${n}`" class="film-hole"></span>
          <span v-for="n in 24" :key="`b${n}`" class="film-hole"></span>
        </div>
      </div>
    </section>

    <!-- ═══════════════ PRODUCTS ═══════════════ -->
    <section class="products-section">
      <div class="section-inner">
        <div class="section-eyebrow">Two Powerful Tools</div>
        <h2 class="section-title">Everything Your Channel Needs</h2>

        <div class="products-grid">
          <div class="product-card">
            <div class="product-glow product-glow-violet"></div>
            <div class="product-icon-wrap">🎬</div>
            <h3 class="product-name">Movie Recap Studio</h3>
            <p class="product-desc">
              Turn any movie into a viral TikTok recap in seconds. Our AI handles copyright protection automatically so you can focus on growing your audience.
            </p>
            <ul class="product-features">
              <li v-for="feat in recapFeatures" :key="feat.label" class="feature-row">
                <span class="feature-icon" :class="feat.available ? 'feature-yes' : 'feature-no'">{{ feat.available ? '✓' : '○' }}</span>
                <span class="feature-label">{{ feat.label }}</span>
                <span v-if="feat.badge" class="feature-badge">{{ feat.badge }}</span>
              </li>
            </ul>
            <a href="/recap" class="product-cta product-cta-violet">Launch Recap Studio →</a>
          </div>

          <div class="product-card">
            <div class="product-glow product-glow-cyan"></div>
            <div class="product-icon-wrap">✍️</div>
            <h3 class="product-name">TikTok Blog Generator</h3>
            <p class="product-desc">
              Generate scroll-stopping TikTok content with matching image prompts. Add your own API key for fully custom content tailored to your niche.
            </p>
            <ul class="product-features">
              <li v-for="feat in blogFeatures" :key="feat.label" class="feature-row">
                <span class="feature-icon" :class="feat.available ? 'feature-yes' : 'feature-no'">{{ feat.available ? '✓' : '○' }}</span>
                <span class="feature-label">{{ feat.label }}</span>
                <span v-if="feat.badge" class="feature-badge">{{ feat.badge }}</span>
              </li>
            </ul>
            <a href="/blogs" class="product-cta product-cta-cyan">Explore Free Blogs →</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ HOW IT WORKS ═══════════════ -->
    <section class="how-section">
      <div class="section-inner">
        <div class="section-eyebrow">Workflow</div>
        <h2 class="section-title">From Idea to Viral — Fast</h2>
        <div class="steps-grid">
          <div v-for="(step, i) in steps" :key="i" class="step-card">
            <div class="step-icon">{{ step.icon }}</div>
            <h4 class="step-title">{{ step.title }}</h4>
            <p class="step-desc">{{ step.desc }}</p>
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
  { label: '1-Click Copyright Protection', available: true, badge: '70% Safe' },
  { label: 'Custom Blur & Mosaic',          available: true },
  { label: 'Custom Watermark',              available: true },
  { label: 'Auto Subtitles',                available: true },
  { label: 'Voice Over',                    available: true },
  { label: 'HD Video Export',               available: true },
  { label: 'Adjustable Video Length',       available: true },
];

const blogFeatures = [
  { label: 'Free TikTok Blog Posts',        available: true },
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

<style scoped>
.home-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  min-height: 100vh;
  overflow-x: hidden;
}

/* ─── Hero ─── */
.hero-section {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 120px 24px 80px;
  overflow: hidden;
  text-align: center;
}
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; }
.orb-violet { width: 600px; height: 600px; background: rgba(124,58,237,0.25);  top: -100px; left: -150px; }
.orb-cyan   { width: 500px; height: 500px; background: rgba(6,182,212,0.18);   top: 100px;  right: -100px; }
.orb-gold   { width: 300px; height: 300px; background: rgba(245,158,11,0.12);  bottom: 0;   left: 40%; }

.film-strip {
  position: absolute; left: 0; right: 0; height: 28px;
  background: rgba(255,255,255,0.025);
  border-top: 1px solid rgba(255,255,255,0.06);
  border-bottom: 1px solid rgba(255,255,255,0.06);
  display: flex; align-items: center; gap: 24px; padding: 0 24px; overflow: hidden;
}
.film-strip-top    { 
  top: 64px;
  left: -240px;}
.film-strip-bottom { bottom:0}

.film-track {
  display: flex;
  gap: 28px; /* match whatever spacing your holes already use */
  width: max-content;
  animation: film-scroll 18s linear infinite;
}

.film-track-top{
  display: flex;
  gap: 28px; /* match whatever spacing your holes already use */
  width: max-content;
  animation: film-top 18s linear infinite;
}


.film-hole {
  flex-shrink: 0; width: 14px; height: 14px; border-radius: 3px;
  background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
}

@keyframes film-top {
  from { transform: translateX(0); }
  to   { transform: translateX(50%); }
}

@media (prefers-reduced-motion: reduce) {
  .film-track { animation: none; }
}

@keyframes film-scroll {
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}

@media (prefers-reduced-motion: reduce) {
  .film-track { animation: none; }
}

.hero-content { position: relative; z-index: 10; max-width: 780px; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(124,58,237,0.15); border: 1px solid rgba(124,58,237,0.35);
  color: #A78BFA; font-size: 13px; font-weight: 500;
  padding: 6px 16px; border-radius: 100px; margin-bottom: 32px;
}
.badge-dot {
  width: 7px; height: 7px; background: #7C3AED; border-radius: 50%;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: 0.5; transform: scale(0.8); }
}
.hero-title {
  font-size: clamp(40px, 7vw, 80px); font-weight: 900;
  line-height: 1.05; letter-spacing: -2px; color: #F1F5F9; margin-bottom: 24px;
}
.gradient-text {
  background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 50%, #F59E0B 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.hero-sub {
  font-size: 18px; color: #94A3B8; line-height: 1.7;
  max-width: 600px; margin: 0 auto 40px;
}
.hero-ctas { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-bottom: 56px; }
.cta-primary {
  display: inline-flex; align-items: center; gap: 10px;
  background: linear-gradient(135deg, #7C3AED, #06B6D4); color: white;
  text-decoration: none; font-size: 16px; font-weight: 700;
  padding: 16px 32px; border-radius: 14px;
  box-shadow: 0 0 40px rgba(124,58,237,0.4);
  transition: transform 0.2s, box-shadow 0.2s;
}
.cta-primary:hover { transform: translateY(-2px); box-shadow: 0 0 60px rgba(124,58,237,0.6); }
.cta-secondary {
  display: inline-flex; align-items: center; gap: 10px;
  background: rgba(255,255,255,0.06); color: #F1F5F9;
  text-decoration: none; font-size: 16px; font-weight: 600;
  padding: 16px 32px; border-radius: 14px;
  border: 1px solid rgba(255,255,255,0.12);
  transition: background 0.2s, border-color 0.2s;
}
.cta-secondary:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25); }
.hero-stats { display: flex; align-items: center; justify-content: center; gap: 32px; flex-wrap: wrap; }
.stat-item  { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.stat-num   { font-size: 26px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.5px; }
.stat-label { font-size: 12px; color: #64748B; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-divider { width: 1px; height: 40px; background: rgba(255,255,255,0.1); }

/* ─── Shared ─── */
.section-inner   { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.section-eyebrow { text-align: center; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #7C3AED; margin-bottom: 12px; }
.section-title   { text-align: center; font-size: clamp(28px, 4vw, 44px); font-weight: 800; color: #F1F5F9; letter-spacing: -1px; margin-bottom: 16px; }

/* ─── Products ─── */
.products-section { padding: 120px 0; background: linear-gradient(180deg, #080B14 0%, #0D1120 50%, #080B14 100%); }
.products-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6em; margin-top: 64px; }
@media (max-width: 768px) { .products-grid { grid-template-columns: 1fr; } }
.product-card {
  position: relative; background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08); border-radius: 24px;
  padding: 40px; overflow: hidden;
  transition: border-color 0.3s, transform 0.3s;
}
.product-card:hover { border-color: rgba(255,255,255,0.15); transform: translateY(-4px); }
.product-glow { position: absolute; top: -60px; right: -60px; width: 200px; height: 200px; border-radius: 50%; filter: blur(50px); pointer-events: none; }
.product-glow-violet { background: rgba(124,58,237,0.4); }
.product-glow-cyan   { background: rgba(6,182,212,0.35); }
.product-icon-wrap { width: 56px; height: 56px; background: rgba(255,255,255,0.06); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 20px; }
.product-name    { font-size: 24px; font-weight: 800; color: #F1F5F9; margin-bottom: 12px; letter-spacing: -0.5px; }
.product-desc    { font-size: 15px; color: #64748B; line-height: 1.7; margin-bottom: 28px; }
.product-features { list-style: none; padding: 0; margin: 0 0 32px; display: flex; flex-direction: column; gap: 10px; }
.feature-row   { display: flex; align-items: center; gap: 10px; font-size: 14px; }
.feature-icon  { width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
.feature-yes   { background: rgba(6,182,212,0.15); color: #06B6D4; }
.feature-no    { background: rgba(255,255,255,0.05); color: #475569; }
.feature-label { color: #CBD5E1; flex: 1; }
.feature-badge { font-size: 10px; font-weight: 700; background: rgba(245,158,11,0.15); color: #F59E0B; padding: 2px 8px; border-radius: 100px; border: 1px solid rgba(245,158,11,0.3); }
.product-cta   { display: inline-block; text-decoration: none; font-size: 15px; font-weight: 700; padding: 14px 28px; border-radius: 12px; transition: opacity 0.2s, transform 0.2s; }
.product-cta:hover       { opacity: 0.85; transform: translateY(-1px); }
.product-cta-violet      { background: linear-gradient(135deg, #7C3AED, #9333EA); color: white; }
.product-cta-cyan        { background: linear-gradient(135deg, #0891B2, #06B6D4); color: white; }


@media (min-width: 1024px) {
  .products-section .section-inner {
    max-width: 1080px;
  }
}


/* ─── How It Works ─── */
.how-section { padding: 120px 0; background: #080B14; }
.steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 64px; }
@media (max-width: 900px) { .steps-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .steps-grid { grid-template-columns: 1fr; } }
.step-card  { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 32px 24px; }
.step-num   { font-size: 11px; font-weight: 700; color: #7C3AED; letter-spacing: 1px; margin-bottom: 16px; font-family: monospace; }
.step-icon  { font-size: 32px; margin-bottom: 16px; }
.step-title { font-size: 17px; font-weight: 700; color: #E2E8F0; margin-bottom: 10px; }
.step-desc  { font-size: 13px; color: #64748B; line-height: 1.65; }
</style>
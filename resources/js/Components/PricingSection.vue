<template>
    <section id="pricing" class="pricing-section">
      <div class="section-inner">
        <div class="section-eyebrow">Pricing Plans</div>
        <h2 class="section-title">Start Free, Scale as You Grow</h2>
        <p class="section-sub">All plans include access to both Movie Recap Studio and Blog Generator</p>

        <div class="pricing-grid">
          <div
            v-for="plan in plans"
            :key="plan.name"
            class="pricing-card"
            :class="{ 'pricing-card-featured': plan.featured }"
          >
            <div v-if="plan.featured" class="pricing-badge-top">Most Popular</div>
            <div class="pricing-glow" :style="{ background: plan.glowColor }"></div>

            <div class="plan-header">
              <span class="plan-icon">{{ plan.icon }}</span>
              <h3 class="plan-name">{{ plan.name }}</h3>
              <div class="plan-price">
                <span v-if="plan.price === 0" class="price-free">Free</span>
                <template v-else>
                  <span class="price-amount">{{ plan.price.toLocaleString() }}</span>
                  <span class="price-unit">MMK<small>/day</small></span>
                </template>
              </div>
              <p class="plan-tagline">{{ plan.tagline }}</p>
            </div>

            <ul class="plan-features">
              <li v-for="feat in plan.features" :key="feat.label" class="plan-feat-row">
                <span class="plan-feat-icon" :class="`pfeat-${feat.type || (feat.included ? 'yes' : 'no')}`">
                  {{ feat.included ? '✓' : '✕' }}
                </span>
                <span class="plan-feat-label" :data-tooltip="feat.tooltip" :class="{ 'has-tooltip': feat.tooltip }">{{ feat.label }}</span>
              </li>
            </ul>

            <a :href="plan.href" class="plan-cta" :class="plan.featured ? 'plan-cta-featured' : 'plan-cta-default'">
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
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(148,163,184,0.15) 0%, transparent 70%)',
    href: '/auth/google',
    cta: 'Start Free',
    features: [
      { label: '1 free generation', included: true , tooltip: 'Upgrade for more generation'},
      { label: 'AI Voice Over (2)', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '30s video length max', included: true, tooltip: 'Upgrade for longer durations' },
      { label: 'Auto Subtitles', included: true },
      { label: 'Low quality export', included: true,type: 'warning' },
      { label: 'Low processing', included: true,type: 'warning' },
      { label: 'Custom Watermark', included: false },
      
    ],
  },
  {
    name: 'Normal',
    icon: '⚡',
    price: 2000,
    tagline: 'Perfect for consistent creators',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(6,182,212,0.2) 0%, transparent 70%)',
    href: '/auth/google',
    cta: 'Get Normal',
    features: [
      { label: '2 generations/day', included: true, tooltip: 'Upgrade for more generation' },
      { label: 'AI Voice Over (+4)', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1min length max', included: true ,tooltip: 'Upgrade for longer durations'},
      { label: 'Auto Subtitles', included: true },
      { label: 'Standard quality export', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: false },
    ],
  },
  {
    name: 'Pro',
    icon: '🔥',
    price: 3000,
    tagline: 'For serious TikTok growth',
    featured: true,
    glowColor: 'radial-gradient(circle, rgba(124,58,237,0.3) 0%, transparent 70%)',
    href: '/auth/google',
    cta: 'Go Pro',
    features: [
      { label: '3 generations/day', included: true ,tooltip: 'Upgrade for more generation' },
      { label: 'AI Voice Over (+4)', included: true, tooltip: 'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '1min 30s length max', included: true,tooltip: 'Upgrade for longer durations' },
      { label: 'High quality export', included: true },
      { label: 'Auto Subtitles', included: true },
      { label: 'Standard processing', included: true },
      { label: 'Custom Watermark', included: true },
    ],
  },
  {
    name: 'VIP',
    icon: '👑',
    price: 9000,
    tagline: 'Unlimited creative power',
    featured: false,
    glowColor: 'radial-gradient(circle, rgba(245,158,11,0.2) 0%, transparent 70%)',
    href: '/auth/google',
    cta: 'Become VIP',
    features: [
      { label: '10 generations/day', included: true },
      { label: 'AI Voice Over (+6)', included: true, tooltip:'Upgrade for more AI Voice' },
      { label: 'Custom Blur & Mosaic', included: true },
      { label: '2min video length max', included: true },
      { label: 'HD quality export', included: true },
      { label: 'Auto Subtitles', included: true },
      { label: 'Fast processing', included: true },
      { label: 'Custom Watermark Position', included: true },
    ],
  },
];
</script>

<style scoped>
.pricing-section {
  padding: 120px 0 100px;
  background: linear-gradient(180deg, #080B14 0%, #0A0D1A 100%);
}
.section-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}
.section-eyebrow {
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #7C3AED;
  margin-bottom: 12px;
}
.section-title {
  text-align: center;
  font-size: clamp(28px, 4vw, 44px);
  font-weight: 800;
  color: #F1F5F9;
  letter-spacing: -1px;
  margin-bottom: 12px;
}
.section-sub {
  text-align: center;
  font-size: 15px;
  color: #64748B;
  margin-bottom: 64px;
}
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}
@media (max-width: 1100px) {
  .pricing-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
  .pricing-grid { grid-template-columns: 1fr; }
}
.pricing-card {
  position: relative;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px;
  padding: 32px 28px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: border-color 0.3s, transform 0.3s;
}
.pricing-card:hover {
  transform: translateY(-4px);
  border-color: rgba(255,255,255,0.14);
}
.pricing-card-featured {
  border-color: rgba(124,58,237,0.5) !important;
  background: rgba(124,58,237,0.07);
}
.pricing-badge-top {
  position: absolute;
  top: 16px;
  right: 16px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  color: white;
  padding: 4px 10px;
  border-radius: 100px;
}
.pricing-glow {
  position: absolute;
  top: -40px;
  left: 50%;
  transform: translateX(-50%);
  width: 160px;
  height: 160px;
  border-radius: 50%;
  filter: blur(40px);
  pointer-events: none;
}
.plan-header {
  position: relative;
  z-index: 1;
  margin-bottom: 28px;
}
.plan-icon { font-size: 28px; display: block; margin-bottom: 10px; }
.plan-name {
  font-size: 20px;
  font-weight: 800;
  color: #F1F5F9;
  margin-bottom: 8px;
}
.plan-price {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 8px;
}
.price-free {
  font-size: 32px;
  font-weight: 900;
  color: #06B6D4;
}
.price-amount {
  font-size: 32px;
  font-weight: 900;
  color: #F1F5F9;
  letter-spacing: -1px;
}
.price-unit {
  font-size: 13px;
  color: #64748B;
  font-weight: 500;
}
.price-unit small { font-size: 11px; }
.plan-tagline {
  font-size: 13px;
  color: #64748B;
}
.plan-features {
  list-style: none;
  padding: 0;
  margin: 0 0 28px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.plan-feat-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
}
.plan-feat-icon {
  width: 18px; height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
}
.has-tooltip {
  position: relative;
  cursor: help;
  /* Visual cue that it's clickable/hoverable */
}

.has-tooltip:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: 125%; /* Positions above the text */
  left: 50%;
  transform: translateX(-50%);
  background: #334155;
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 10;
  pointer-events: none;
}


.pfeat-warning { background: rgba(141, 150, 13, 0.15);color: #eab308; /* Yellow/Amber */}
.pfeat-yes { background: rgba(6,182,212,0.15); color: #06B6D4; }
.pfeat-no  { background: rgba(255,255,255,0.04); color: #334155; }
.plan-feat-label { color: #94A3B8; }
.plan-cta {
  display: block;
  text-align: center;
  text-decoration: none;
  font-size: 14px;
  font-weight: 700;
  padding: 14px;
  border-radius: 12px;
  transition: opacity 0.2s, transform 0.2s;
  position: relative;
  z-index: 1;
}
.plan-cta:hover { opacity: 0.85; transform: translateY(-1px); }
.plan-cta-featured {
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  color: white;
}
.plan-cta-default {
  background: rgba(255,255,255,0.07);
  color: #CBD5E1;
  border: 1px solid rgba(255,255,255,0.1);
}
.plan-cta-default:hover { background: rgba(255,255,255,0.12); }
</style>
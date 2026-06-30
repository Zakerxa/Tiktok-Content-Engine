<template>
  <Head title="Email Verification" />

  <div class="login-root">
    <!-- Ambient brand orbs (same palette as Home / Login / Register) -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <div class="film-strip film-strip-top">
      <span v-for="n in 24" :key="`t${n}`" class="film-hole"></span>
    </div>

    <a href="/" class="home-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M19 12H5M12 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Back to Home
    </a>

    <div class="login-card">

      <!-- Brand header -->
      <div class="brand-block">
        <div class="brand-badge">
          <svg xmlns="http://www.w3.org/2000/svg" class="brand-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
        </div>
        <h1 class="brand-name">Email <span class="gradient-text">အတည်ပြုပါ</span></h1>
        <p class="brand-tag">စတင်အသုံးပြုရန် နောက်ဆုံးအဆင့်တစ်ခုသာ ကျန်ရှိပါသည်</p>
      </div>

      <p class="verify-copy">
        Sign up ပြုလုပ်ပေးတဲ့အတွက် ကျေးဇူးတင်ပါတယ်! စတင်အသုံးပြုနိုင်ရန် သင့် Email လိပ်စာကို Verify လုပ်ပေးပါ။ ကျွန်ုပ်တို့ ပို့ပေးထားသော Email ထဲက Link ကိုနှိပ်ပြီး အတည်ပြုပေးပါ။
      </p>

      <p v-if="verificationLinkSent" class="status-banner">
        Verification Link အသစ်တစ်ခုကို သင် Register လုပ်စဉ်အသုံးပြုခဲ့သော Email လိပ်စာသို့ ပို့ပေးလိုက်ပါပြီ။
      </p>

      <form @submit.prevent="submit" class="login-form">
        <button type="submit" :disabled="form.processing || cooldown > 0" class="btn-submit">
          <svg v-if="form.processing" class="spinner" viewBox="0 0 24 24" fill="none">
            <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          <span>
            {{
              form.processing
                ? 'ပို့နေသည်...'
                : cooldown > 0
                  ? `${cooldown} စက္ကန့်အကြာတွင် ပြန်ပို့နိုင်ပါမည်`
                  : 'Verification Email ပြန်ပို့ရန်'
            }}
          </span>
        </button>
      </form>

      <p class="signin-row">
        <Link href="/logout" method="post" as="button" class="link-accent logout-btn">Log Out</Link>
      </p>
    </div>

    <div class="film-strip film-strip-bottom">
      <span v-for="n in 24" :key="`b${n}`" class="film-hole"></span>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  status: {
    type: String,
  },
})

const form = useForm({})

// Cooldown is stored in localStorage so it survives page refresh / navigation.
// Key includes nothing user-specific since this page is only reachable while
// logged in as the unverified user, and the timer is just a UX guard
// (the server should still rate-limit verification-notification requests).
const COOLDOWN_KEY = 'verify_email_resend_until'
const COOLDOWN_SECONDS = 60
const cooldown = ref(0)
let intervalId = null

function startCooldownFromStorage() {
  const until = Number(localStorage.getItem(COOLDOWN_KEY) || 0)
  const remaining = Math.ceil((until - Date.now()) / 1000)
  cooldown.value = remaining > 0 ? remaining : 0
}

function tick() {
  startCooldownFromStorage()
  if (cooldown.value <= 0 && intervalId) {
    clearInterval(intervalId)
    intervalId = null
  }
}

function beginCooldown() {
  const until = Date.now() + COOLDOWN_SECONDS * 1000
  localStorage.setItem(COOLDOWN_KEY, String(until))
  cooldown.value = COOLDOWN_SECONDS
  if (!intervalId) {
    intervalId = setInterval(tick, 1000)
  }
}

onMounted(() => {
  // A verification email is already sent automatically on registration,
  // so start the cooldown immediately on first load too — this stops users
  // from mashing the resend button before the first email has even arrived.
  const until = Number(localStorage.getItem(COOLDOWN_KEY) || 0)
  if (until > Date.now()) {
    startCooldownFromStorage()
    intervalId = setInterval(tick, 1000)
  } else {
    beginCooldown()
  }
})

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId)
})

function submit() {
  if (cooldown.value > 0) return
  form.post('/email/verification-notification', {
    onSuccess: () => beginCooldown(),
  })
}

const verificationLinkSent = computed(
  () => props.status === 'verification-link-sent'
)
</script>

<style scoped>
.login-root {
  background: #080B14;
  color: #F1F5F9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 100px 24px;
}

/* ─── Ambient orbs ─── */
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; }
.orb-violet { width: 560px; height: 560px; background: rgba(124,58,237,0.22); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6,182,212,0.16);  bottom: -140px; right: -120px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245,158,11,0.10); top: 45%; right: 8%; }

.film-strip {
  position: absolute; left: 0; right: 0; height: 26px;
  background: rgba(255,255,255,0.025);
  border-top: 1px solid rgba(255,255,255,0.06);
  border-bottom: 1px solid rgba(255,255,255,0.06);
  display: flex; align-items: center; gap: 22px; padding: 0 24px; overflow: hidden;
}
.film-strip-top    { top: 0; }
.film-strip-bottom { bottom: 0; }
.film-hole {
  flex-shrink: 0; width: 13px; height: 13px; border-radius: 3px;
  background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);
}

.home-link {
  position: absolute; top: 32px; left: 32px; z-index: 10;
  display: inline-flex; align-items: center; gap: 6px;
  color: #64748B; font-size: 14px; font-weight: 500;
  text-decoration: none; transition: color 0.2s;
}
.home-link:hover { color: #CBD5E1; }

/* ─── Card ─── */
.login-card {
  position: relative; z-index: 5;
  width: 100%; max-width: 408px;
  background: rgba(255,255,255,0.035);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 28px;
  padding: 44px 36px 36px;
  backdrop-filter: blur(18px);
  box-shadow: 0 30px 80px rgba(0,0,0,0.45);
}

.brand-block { display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 24px; }
.brand-badge {
  display: flex; align-items: center; justify-content: center;
  width: 52px; height: 52px; border-radius: 16px;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  box-shadow: 0 0 30px rgba(124,58,237,0.35);
  margin-bottom: 16px;
}
.brand-icon { width: 26px; height: 26px; color: #fff; }
.brand-name { font-size: 21px; font-weight: 800; letter-spacing: -0.4px; color: #F1F5F9; }
.gradient-text {
  background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 60%, #F59E0B 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.brand-tag { font-size: 13px; color: #64748B; margin-top: 6px; }

/* ─── Body copy ─── */
.verify-copy {
  font-size: 13.5px; color: #94A3B8; line-height: 1.7;
  text-align: center; margin-bottom: 20px;
}

/* ─── Status banner ─── */
.status-banner {
  font-size: 13px; font-weight: 600; color: #34D399;
  background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3);
  border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; text-align: center;
}

/* ─── Form ─── */
.login-form { display: flex; flex-direction: column; gap: 18px; }

.btn-submit {
  display: flex; align-items: center; justify-content: center; gap: 9px;
  width: 100%; padding: 13px; border-radius: 14px; border: none; cursor: pointer;
  background: linear-gradient(135deg, #7C3AED, #06B6D4); color: #fff;
  font-size: 14px; font-weight: 700;
  box-shadow: 0 0 30px rgba(124,58,237,0.3);
  transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 0 44px rgba(124,58,237,0.5); }
.btn-submit:disabled { opacity: 0.65; cursor: not-allowed; }
.spinner { width: 16px; height: 16px; animation: spin 0.8s linear infinite; }
.spinner-track { opacity: 0.25; }
.spinner-head { opacity: 0.85; }
@keyframes spin { to { transform: rotate(360deg); } }

.signin-row { text-align: center; font-size: 13px; color: #64748B; margin-top: 22px; }
.link-accent { color: #A78BFA; font-weight: 600; text-decoration: none; }
.link-accent:hover { text-decoration: underline; }
.logout-btn {
  background: none; border: none; cursor: pointer;
  font-size: 13px; font-family: inherit;
}

/* ─── Accessibility ─── */
.btn-submit:focus-visible,
.link-accent:focus-visible,
.home-link:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .btn-submit, .home-link, .link-accent { transition: none !important; }
  .btn-submit:hover:not(:disabled) { transform: none !important; }
  .spinner { animation: none !important; }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .login-root { padding: 80px 16px; }
  .login-card { padding: 36px 24px 28px; border-radius: 22px; }
  .film-strip { display: none; }
}
</style>
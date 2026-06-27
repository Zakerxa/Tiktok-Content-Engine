<template>
  <Head title="Log In" />

  <div class="login-root">
    <!-- Ambient brand orbs (same palette as Home) -->
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
              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
          </svg>
        </div>
        <h1 class="brand-name">Movie Recap <span class="gradient-text">Engine</span></h1>
        <p class="brand-tag">Sign in to keep the recaps rolling</p>
      </div>

      <!-- Flash error (from Laravel redirect) -->
      <div v-if="flashError" class="alert-error">{{ flashError }}</div>

      <!-- Google Login -->
      <a :href="googleLoginUrl" class="btn-google">
        <svg class="w-5 h-5" viewBox="0 0 24 24" width="20" height="20">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Google နဲ့ Login ဝင်မည်</span>
      </a>

      <!-- Ticket tear-line divider (signature element) -->
      <div class="tear-line">
        <span class="tear-rule"></span>
        <span v-for="n in 9" :key="n" class="perf-dot"></span>
        <span class="tear-rule"></span>
      </div>
      <p class="tear-label">သို့မဟုတ် Email နဲ့ ဝင်ပါ</p>

      <!-- Manual login -->
      <form @submit.prevent="submit" class="login-form">
        <div class="field">
          <label class="field-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="your@email.com"
            autocomplete="username"
            required
            class="field-input"
            :class="{ 'field-input-error': form.errors.email }"
          />
          <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
        </div>

        <div class="field">
          <label class="field-label">Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
            required
            class="field-input"
            :class="{ 'field-input-error': form.errors.password }"
          />
          <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" v-model="form.remember" />
            Remember me
          </label>
          <a href="/forgot-password" class="link-muted">Forgot your password?</a>
        </div>

        <button type="submit" :disabled="form.processing" class="btn-submit">
          <svg v-if="form.processing" class="spinner" viewBox="0 0 24 24" fill="none">
            <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          <span>{{ form.processing ? 'Logging in...' : 'Login ဝင်မည်' }}</span>
        </button>
      </form>

     <p class="signin-row">
        Account မရှိသေးလား?
        <a href="/register" class="link-accent">Sign up ပါ</a>
      </p>
    </div>

    <div class="film-strip film-strip-bottom">
      <span v-for="n in 24" :key="`b${n}`" class="film-hole"></span>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const flashError = computed(() => page.props.flash?.error ?? null)

const googleLoginUrl = '/auth/google'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
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

.signin-row { text-align: center; font-size: 13px; color: #64748B; margin-top: 22px; }
.link-accent { color: #A78BFA; font-weight: 600; text-decoration: none; margin-left: 4px; }
.link-accent:hover { text-decoration: underline; }

/* ─── Ambient orbs (shared brand language with Home) ─── */
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

.brand-block { display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 28px; }
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

/* ─── Alert ─── */
.alert-error {
  margin-bottom: 18px; padding: 12px 14px;
  background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.3);
  color: #FCA5A5; border-radius: 14px; font-size: 13px; font-weight: 500; line-height: 1.5;
}

/* ─── Google button ─── */
.btn-google {
  display: flex; align-items: center; justify-content: center; gap: 12px;
  width: 100%; padding: 13px 16px; border-radius: 14px;
  background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1);
  color: #F1F5F9; font-size: 14px; font-weight: 600;
  text-decoration: none; transition: background 0.2s, border-color 0.2s, transform 0.2s;
}
.btn-google:hover { background: rgba(255,255,255,0.07); border-color: rgba(124,58,237,0.4); transform: translateY(-1px); }

/* ─── Tear-line (signature element: ticket-stub perforation) ─── */
.tear-line { display: flex; align-items: center; gap: 5px; margin: 26px 0 10px; }
.tear-rule {
  flex: 1; height: 1px;
  background-image: repeating-linear-gradient(to right, rgba(255,255,255,0.18) 0 5px, transparent 5px 11px);
}
.perf-dot { width: 5px; height: 5px; border-radius: 50%; background: #080B14; flex-shrink: 0; box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08); }
.tear-label { text-align: center; font-size: 12px; color: #475569; margin-bottom: 22px; }

/* ─── Form ─── */
.login-form { display: flex; flex-direction: column; gap: 18px; }
.field { display: flex; flex-direction: column; }
.field-label { font-size: 12px; font-weight: 600; color: #94A3B8; margin-bottom: 7px; }
.field-input {
  width: 100%; padding: 11px 14px; border-radius: 12px;
  background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
  color: #F1F5F9; font-size: 14px; transition: border-color 0.2s, box-shadow 0.2s;
}
.field-input::placeholder { color: #475569; }
.field-input:focus { outline: none; border-color: #7C3AED; box-shadow: 0 0 0 4px rgba(124,58,237,0.15); }
.field-input-error { border-color: rgba(239,68,68,0.5); }
.field-error { font-size: 12px; color: #F87171; margin-top: 6px; }

.row-between { display: flex; align-items: center; justify-content: space-between; margin-top: -4px; }
.remember { display: flex; align-items: center; gap: 7px; font-size: 13px; color: #94A3B8; cursor: pointer; }
.remember input { accent-color: #7C3AED; width: 15px; height: 15px; }
.link-muted { font-size: 13px; color: #64748B; text-decoration: none; transition: color 0.2s; }
.link-muted:hover { color: #A78BFA; text-decoration: underline; }

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

.footnote { text-align: center; font-size: 11.5px; color: #475569; line-height: 1.7; margin-top: 26px; }

/* ─── Accessibility ─── */
.btn-google:focus-visible,
.btn-submit:focus-visible,
.field-input:focus-visible,
.link-muted:focus-visible,
.home-link:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .btn-google, .btn-submit, .home-link, .link-muted { transition: none !important; }
  .btn-google:hover, .btn-submit:hover:not(:disabled) { transform: none !important; }
  .spinner { animation: none !important; }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .login-root { padding: 80px 16px; }
  .login-card { padding: 36px 24px 28px; border-radius: 22px; }
  .film-strip { display: none; }
}
</style>
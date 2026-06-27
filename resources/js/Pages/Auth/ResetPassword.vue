<template>
  <Head title="Reset Password" />

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
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-12V7a4 4 0 10-8 0v4h8z"/>
          </svg>
        </div>
        <h1 class="brand-name">Reset your <span class="gradient-text">Password</span></h1>
        <p class="brand-tag">Choose a new password to secure your account</p>
      </div>

      <!-- Reset password form -->
      <form @submit.prevent="submit" class="login-form">
        <div class="field">
          <label class="field-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            placeholder="your@email.com"
            autocomplete="username"
            required
            autofocus
            class="field-input"
            :class="{ 'field-input-error': form.errors.email }"
          />
          <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
        </div>

        <div class="field">
          <label class="field-label">New Password</label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            autocomplete="new-password"
            required
            class="field-input"
            :class="{ 'field-input-error': form.errors.password }"
          />
          <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
        </div>

        <div class="field">
          <label class="field-label">Confirm New Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            placeholder="••••••••"
            autocomplete="new-password"
            required
            class="field-input"
            :class="{ 'field-input-error': form.errors.password_confirmation }"
          />
          <p v-if="form.errors.password_confirmation" class="field-error">{{ form.errors.password_confirmation }}</p>
        </div>

        <button type="submit" :disabled="form.processing" class="btn-submit">
          <svg v-if="form.processing" class="spinner" viewBox="0 0 24 24" fill="none">
            <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          <span>{{ form.processing ? 'Resetting...' : 'Reset Password' }}</span>
        </button>
      </form>
    </div>

    <div class="film-strip film-strip-bottom">
      <span v-for="n in 24" :key="`b${n}`" class="film-hole"></span>
    </div>
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  email: {
    type: String,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/reset-password', {
    onFinish: () => form.reset('password', 'password_confirmation'),
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

/* ─── Accessibility ─── */
.btn-submit:focus-visible,
.field-input:focus-visible,
.home-link:focus-visible {
  outline: 2px solid #06B6D4; outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
  .btn-submit, .home-link { transition: none !important; }
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
<template>
  <Transition name="modal-fade">
    <div
      v-if="show"
      class="fixed inset-0 z-40 flex items-center justify-center p-4"
      style="background: rgba(0,0,0,0.6); backdrop-filter: blur(6px);"
    >
      <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-200/60 p-8">

        <!-- Logo & Title -->
        <div class="flex flex-col items-center mb-8">
          <div class="bg-gradient-to-tr from-indigo-500 to-indigo-700 text-white p-3.5 rounded-2xl shadow-lg shadow-indigo-500/20 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
          </div>
          <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Movie Recap Engine</h2>
          <p class="text-sm text-slate-500 mt-1">AI-Powered Video Recap Generator</p>
        </div>

        <!-- Flash error (from Laravel redirect) -->
        <div
          v-if="flashError"
          class="mb-5 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm font-medium"
        >
          {{ flashError }}
        </div>

        <!-- Google Login Button -->
        <a
          :href="googleLoginUrl"
          class="flex items-center justify-center gap-3 w-full py-3 px-4 rounded-xl border-2 border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 transition-all font-semibold text-slate-700 text-sm mb-4 group"
        >
          <!-- Google Icon -->
          <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
          </svg>
          <span>Google နဲ့ Login ဝင်မည်</span>
        </a>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-5">
          <div class="flex-1 h-px bg-slate-200"></div>
          <span class="text-xs text-slate-400 font-medium">သို့မဟုတ်</span>
          <div class="flex-1 h-px bg-slate-200"></div>
        </div>

        <!-- Manual Login Form -->
        <form @submit.prevent="handleManualLogin" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="your@email.com"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm text-slate-700 placeholder-slate-400"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
            <input
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              required
              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm text-slate-700 placeholder-slate-400"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span>{{ loading ? 'Logging in...' : 'Login ဝင်မည်' }}</span>
          </button>
        </form>

        <!-- Footer note -->
        <p class="text-center text-xs text-slate-400 mt-6 leading-relaxed">
          Google login သုံးလျှင် Tester account အဖြစ် အလိုအလျောက် ဖန်တီးမည်။<br/>
          Plan upgrade အတွက် Admin ကို ဆက်သွယ်ပါ။
        </p>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show:          { type: Boolean, default: true },
  flashError:    { type: String,  default: null },
  googleLoginUrl:{ type: String,  default: '/auth/google' },
})

const emit = defineEmits(['loginSuccess'])

const form = ref({ email: '', password: '' })
const loading = ref(false)

function handleManualLogin() {
  loading.value = true
  router.post('/login', form.value, {
    onSuccess: () => emit('loginSuccess'),
    onError:   () => { loading.value = false },
    onFinish:  () => { loading.value = false },
  })
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.modal-fade-enter-from,
.modal-fade-leave-to    { opacity: 0; transform: scale(0.96); }
</style>

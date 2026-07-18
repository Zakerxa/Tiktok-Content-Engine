<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
  >
    <div class="relative flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-3xl border border-white/10 bg-[#0c1120] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)]">
      <!-- Close -->
      <button
        v-if="!['submitted', 'expired'].includes(step)"
        type="button"
        @click="handleClose()"
        class="absolute right-5 top-5 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-[#0c1120]/80 text-slate-500 backdrop-blur transition-colors hover:bg-white/[0.06] hover:text-slate-200"
        aria-label="ပိတ်ရန်"
      >
        ✕
      </button>

      <div class="modal-scroll flex-1 overflow-y-auto p-6 sm:p-7">
      <!-- Header -->
      <div class="flex items-center gap-2.5">
        <span class="text-2xl leading-none">{{ plan.icon }}</span>
        <div>
          <h3 class="text-base font-extrabold text-slate-50">{{ plan.name }} Plan ဝယ်ယူရန်</h3>
          <p class="text-xs text-slate-500">{{ plan.price.toLocaleString() }} MMK/ရက်</p>
        </div>
      </div>

      <div class="my-5 h-px w-full bg-white/[0.06]"></div>

      <!-- ═══ Step: choose package ═══ -->
      <div v-if="step === 'package'">
        <p class="text-sm font-semibold text-slate-300">ဘယ်လောက်ကြာကြာ သုံးမလဲ ရွေးပါ</p>

        <div class="mt-3 grid gap-2">
          <button v-for="pkg in packages" :key="pkg.key" type="button" @click="selectedPackageKey = pkg.key" class="flex items-center justify-between rounded-2xl border px-4 py-3 text-left transition-all duration-200" :class="selectedPackageKey === pkg.key ? 'border-violet-500/50 bg-violet-500/[0.08]' : 'border-white/10 bg-white/[0.02] hover:border-white/20'">
            <span>
              <span class="block text-sm font-bold text-slate-200">{{ pkg.label }}</span>
              <span class="text-sm bold text-slate-400">{{ pkg.days }} days — {{ costFor(pkg).total.toLocaleString() }} MMK</span>
            </span>
            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide"
              :class="pkg.discountPercent > 0 ? 'bg-emerald-500/15 text-emerald-300' : 'bg-white/[0.05] text-slate-400'"
            >
              {{ pkg.discountPercent > 0 ? `${pkg.discountPercent}% Off` : 'နဂိုဈေး' }}
            </span>
          </button>
        </div>

        <button
          type="button"
          @click="step = 'phone'"
          class="mt-5 block w-full rounded-xl bg-gradient-to-r from-violet-500 to-cyan-400 py-3.5 text-center text-sm font-bold text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] transition-all duration-200 hover:brightness-110 active:scale-[0.98]"
        >
          ဆက်လုပ်ရန် →
        </button>
      </div>

      <!-- ═══ Step: phone number ═══ -->
      <div v-else-if="step === 'phone'">
        <button type="button" @click="step = 'package'" class="mb-4 text-xs font-semibold text-slate-500 hover:text-slate-300">
          ← နောက်သို့
        </button>
      
        <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
          <div class="flex items-center justify-between text-sm">
            <span class="text-slate-400">{{ selectedPackage.label }}</span>
            <span class="font-bold text-slate-50">{{ costFor(selectedPackage).total.toLocaleString() }} MMK</span>
          </div>
        </div>
      
        <!-- Payment Type Selector -->
        <p class="mt-4 text-sm font-semibold text-slate-300">ဘယ် App နဲ့ ငွေလွှဲမလဲ ရွေးပါ</p>
        <div class="mt-2 grid grid-cols-2 gap-2">
          <button
            v-for="method in paymentMethods"
            :key="method.key"
            type="button"
            @click="bankType = method.key"
            class="flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-bold transition-all duration-200"
            :class="bankType === method.key
              ? 'border-violet-500/50 bg-violet-500/[0.08] text-slate-100'
              : 'border-white/10 bg-white/[0.02] text-slate-400 hover:border-white/20'"
          >
            <img :src="method.icon" :alt="method.label" class="h-5 w-5 rounded-md object-contain" />
            {{ method.label }}
          </button>
        </div>
      
        <label class="mt-4 block text-sm font-semibold text-slate-300">ဖုန်းနံပါတ် ထည့်ပါ</label>
        <input
          v-model="phone"
          type="tel"
          inputmode="numeric"
          placeholder="09xxxxxxxxx"
          class="mt-2 w-full rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm text-slate-100 placeholder:text-slate-600 outline-none transition-colors focus:border-violet-500/50"
        />
        <p v-if="initiateError" class="mt-2 text-xs font-medium text-rose-400">{{ initiateError }}</p>
      
        <button
          type="button"
          :disabled="!isPhoneValid || !bankType || submitting"
          @click="submitPhone"
          class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-500 to-cyan-400 py-3.5 text-center text-sm font-bold text-white shadow-[0_8px_24px_-8px_rgba(124,58,237,0.6)] transition-all duration-200 hover:brightness-110 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:brightness-100"
        >
          <span v-if="submitting" class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
          {{ submitting ? 'ကုဒ်ထုတ်နေသည်…' : 'ကုဒ်ထုတ်ပါ' }}
        </button>
      </div>

      <!-- ═══ Step: payment (code + upload) ═══ -->
      <div v-else-if="step === 'payment'">
        <!-- Countdown -->
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold text-slate-500">ကုဒ်သက်တမ်း</span>
          <span
            class="font-mono text-sm font-bold"
            :class="remainingSeconds <= 60 ? 'text-rose-400' : 'text-slate-300'"
          >
            {{ formattedTimer }}
          </span>
        </div>
        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/[0.06]">
          <div
            class="h-full rounded-full transition-all duration-1000 ease-linear"
            :class="remainingSeconds <= 60 ? 'bg-rose-500' : 'bg-gradient-to-r from-violet-500 to-cyan-400'"
            :style="{ width: `${(remainingSeconds / (paymentData?.expires_in_seconds || 600)) * 100}%` }"
          ></div>
        </div>

        <!-- Pay-to Account Box -->
        <div class="mt-5 rounded-2xl border border-violet-500/30 bg-violet-500/[0.06] p-5 text-center">
          <img
            :src="paymentData.bank_type === 'wave' ? '/images/wave.png' : '/images/kbz.webp'"
            :alt="paymentData.bank_type === 'wave' ? 'WaveMoney' : 'KPay'"
            class="mx-auto h-9 w-9 rounded-lg object-contain"
          />
          <p class="mt-2 text-xs font-semibold text-slate-400">
            {{ paymentData.bank_type === 'wave' ? 'WaveMoney' : 'KPay' }} ဖြင့် ဒီနံပါတ်ကို လွှဲပါ
          </p>
          <p class="mt-1.5 font-mono text-2xl font-black tracking-[0.2em] text-slate-50">{{ paymentData.pay_to_phone }}</p>
          <p class="mt-1 text-xs text-slate-500">အမည် — {{ paymentData.pay_to_name }}</p>
          <button
            type="button"
            @click="copyPhone"
            class="mt-2 text-xs font-bold text-violet-300 hover:text-violet-200"
          >
            {{ copiedPhone ? '✓ ကူးပြီး' : 'Copy ကူးရန်' }}
          </button>
        </div>

        <!-- Code -->
        <div class="mt-5 rounded-2xl border border-violet-500/30 bg-violet-500/[0.06] p-5 text-center">
          <p class="text-xs font-semibold text-slate-400">Note ထဲမှာ ဒီကုဒ်ကို ထည့်ပါ</p>
          <p class="mt-1.5 font-mono text-2xl font-black tracking-[0.2em] text-slate-50">{{ paymentData.ref_code }}</p>
          <button
            type="button"
            @click="copyCode"
            class="mt-2 text-xs font-bold text-violet-300 hover:text-violet-200"
          >
            {{ copied ? '✓ ကူးပြီး' : 'Copy ကူးရန်' }}
          </button>
        </div>

        <!-- Summary -->
        <div class="mt-4 space-y-1.5 rounded-2xl border border-white/10 bg-white/[0.02] p-4 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-slate-500">Plan</span>
            <span class="font-semibold text-slate-200">{{ plan.icon }} {{ plan.name }} — {{ selectedPackage.label }}</span>
          </div>
          <div class="flex items-center mt-3 justify-between">
            <span class="text-slate-500">လွှဲရမည့်ငွေ</span>
            <span class="font-black text-2xl text-slate-50">{{ paymentData.amount.toLocaleString() }} MMK</span>
          </div>
        </div>

        <!-- Warning -->
        <div class="mt-4 flex items-start gap-2 rounded-xl border border-amber-500/25 bg-amber-500/[0.08] p-3">
          <span class="text-base leading-none">⚠️</span>
          <p class="text-xs leading-relaxed text-amber-300">
            KPay/WavePay ဖြင့် ငွေလွှဲသည့်အခါ <strong class="font-bold">Note</strong> နေရာမှာ
            <strong class="font-mono font-bold">{{ paymentData.ref_code }}</strong> ကို မဖြစ်မနေ ထည့်ပေးပါ —
            Note မှားလျှင် (သို့) မထည့်ပါက အချိန်ကြာမြင့်ပြီး အတည်ပြုမှု နှေးနိုင်ပါသည်။
          </p>
        </div>

        <!-- Upload -->
        <label
          class="mt-4 flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-white/15 px-4 py-6 text-center transition-colors hover:border-violet-500/40 hover:bg-white/[0.02]"
        >
          <input type="file" accept="image/*" class="hidden" :disabled="uploading" @change="onFileChange" />
          <span v-if="uploading" class="h-6 w-6 animate-spin rounded-full border-2 border-violet-400/30 border-t-violet-400"></span>
          <template v-else>
            <span class="text-2xl">📸</span>
            <span class="text-sm font-bold text-slate-300">ငွေလွှဲပြေစာ Screenshot တင်ရန်</span>
          </template>
          <span class="text-xs text-slate-500">{{ uploading ? 'တင်နေသည်…' : 'နှိပ်ပြီး ရွေးပါ' }}</span>
        </label>
        <p v-if="uploadError" class="mt-2 text-xs font-medium text-rose-400">{{ uploadError }}</p>

        <!-- Manual cancel — hidden entirely once a screenshot is being
             uploaded/verified, so it can't race with verify() -->
        <button
          v-if="!uploading"
          type="button"
          :disabled="cancelling"
          @click="cancelPayment"
          class="mt-4 block w-full text-center text-xs font-semibold text-rose-400 transition-colors hover:text-rose-300 disabled:cursor-not-allowed disabled:opacity-50"
        >
          {{ cancelling ? 'ပယ်ဖျက်နေသည်…' : 'ဒီငွေလွှဲမှုကို ပယ်ဖျက်ရန်' }}
        </button>
        <p v-if="cancelError" class="mt-1 text-center text-xs font-medium text-rose-400">{{ cancelError }}</p>
      </div>

      <!-- ═══ Step: submitted ═══ -->
      <div v-else-if="step === 'submitted'" class="py-4 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/15 text-3xl">✓</div>
        <h4 class="mt-4 text-base font-extrabold text-slate-50">ပြေစာလက်ခံရရှိပါပြီ</h4>
        <p class="mt-1.5 text-sm text-slate-400">
          သင့်ငွေလွှဲမှုကို စစ်ဆေးနေပါသည်။ အတည်ပြုပြီးပါက Plan ကို အလိုအလျောက် အသက်ဝင်သွားပါမည်။
        </p>
        <button
          type="button"
          @click="handleClose('submitted')"
          class="mt-5 w-full rounded-xl border border-white/10 bg-white/[0.05] py-3 text-sm font-bold text-slate-200 hover:bg-white/[0.09]"
        >
          ပိတ်ရန်
        </button>
      </div>

      <!-- ═══ Step: expired ═══ -->
      <div v-else-if="step === 'expired'" class="py-4 text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-500/15 text-3xl">⏱️</div>
        <h4 class="mt-4 text-base font-extrabold text-slate-50">ကုဒ်သက်တမ်း ကုန်သွားပါပြီ</h4>
        <p class="mt-1.5 text-sm text-slate-400">
          ၁၀ မိနစ်အတွင်း ပြေစာ upload မတင်ခဲ့လို့ ကုဒ်ကို ပယ်ဖျက်လိုက်ပါပြီ။ ထပ်စမ်းကြည့်ပါ။
        </p>
        <div class="mt-5 flex gap-2">
          <button
            type="button"
            @click="handleClose('expired')"
            class="flex-1 rounded-xl border border-white/10 bg-white/[0.05] py-3 text-sm font-bold text-slate-200 hover:bg-white/[0.09]"
          >
            ပိတ်ရန်
          </button>
          <button
            type="button"
            @click="restart"
            class="flex-1 rounded-xl bg-gradient-to-r from-violet-500 to-cyan-400 py-3 text-sm font-bold text-white hover:brightness-110"
          >
            ထပ်စမ်းရန်
          </button>
        </div>
      </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  plan: { type: Object, required: true },
  packages: { type: Array, required: true },
  initialPackageKey: { type: String, default: null },
  payToPhone: String,
  // Passed by Plan.vue when it finds an active checkout via GET
  // /api/payments/current (e.g. user left for the KPay app and the mobile
  // browser killed the tab, or they just refreshed). Shape matches the
  // initiate()/current() JSON: { ref_code, amount, plan_key, duration_days,
  // expires_in_seconds }.
  resumePayment: { type: Object, default: null },
});

// 'close' carries a reason so Plan.vue knows whether to refresh the history
// tab: 'submitted' | 'cancelled' | 'expired' | 'dismissed' (package/phone step).
const emit = defineEmits(['close']);

// Screenshot ကို upload လုပ်ပြီးသား (status === 'processing') ဖြစ်ရင်
// upload window timer ကို ပြန်စဖို့ မလိုတော့ပါဘူး — "submitted" (review
// စောင့်နေတယ်) screen ကိုပဲ တိုက်ရိုက် ပြရပါမယ်။ status မလာသေးရင် (old
// backend) 'pending' လိုပဲ ယူဆပြီး timer ပါတဲ့ 'payment' step ကိုပြပါတယ်။
const step = ref(
  props.resumePayment
    ? (props.resumePayment.status === 'processing' ? 'submitted' : 'payment')
    : 'package'
); // package -> phone -> payment -> submitted | expired
const selectedPackageKey = ref(props.initialPackageKey || props.packages[props.packages.length - 1].key);
const selectedPackage = computed(
  () => props.packages.find(p => p.key === selectedPackageKey.value) ?? props.packages[0]
);

const phone = ref('');
const isPhoneValid = computed(() => /^09\d{7,9}$/.test(phone.value.trim()));

const submitting = ref(false);
const initiateError = ref(null);
const paymentData = ref(props.resumePayment ?? null); // { ref_code, amount, plan_key, duration_days, expires_in_seconds }

function costFor(pkg) {
  const total = Math.round(props.plan.price * pkg.days * (1 - pkg.discountPercent / 100));
  return { total, perDay: Math.round(total / pkg.days) };
}

// mm:ss ပြပေးတဲ့ computed — ဒါမရှိလို့ "sec" မပြတာ ဖြစ်ခဲ့ပါတယ်
const formattedTimer = computed(() => {
  const total = Math.max(Math.floor(remainingSeconds.value), 0);
  const minutes = Math.floor(total / 60);
  const seconds = total % 60;
  return `${minutes}:${String(seconds).padStart(2, '0')}`;
});

// Reads the XSRF-TOKEN cookie (Laravel reissues it fresh on every response),
// instead of a static <meta csrf-token> tag which can go stale in an Inertia SPA.
function csrfToken() {
  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : '';
}

// Bank type state — user ရွေးထားတဲ့ payment app
const bankType = ref(null);
const paymentMethods = [
  { key: 'kpay', label: 'KPay', icon: '/images/kbz.webp' },
  { key: 'wave', label: 'WaveMoney', icon: '/images/wave.png' },
];

// submitPhone ထဲမှာ bank_type ကို body ထဲ ထည့်ပါ
async function submitPhone() {
  if (!isPhoneValid.value || !bankType.value || submitting.value) return;
  submitting.value = true;
  initiateError.value = null;

  try {
    const res = await fetch('/api/payments/initiate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify({
        plan_key: props.plan.key,
        duration_days: selectedPackage.value.days,
        phone: phone.value.trim(),
        bank_type: bankType.value, // ← အသစ်ထည့်ထားတာ
      }),
    });

    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'ကုဒ်ထုတ်ရာတွင် အမှားတစ်ခု ဖြစ်သွားပါသည်');

    paymentData.value = json;
    remainingSeconds.value = Math.floor(json.expires_in_seconds || 600);
    startTimer();
    step.value = 'payment';
  } catch (e) {
    initiateError.value = e.message;
  } finally {
    submitting.value = false;
  }
}

// copyPhone function
const copiedPhone = ref(false);
function copyPhone() {
  if (!paymentData.value?.pay_to_phone) return;
  navigator.clipboard?.writeText(paymentData.value.pay_to_phone);
  copiedPhone.value = true;
  setTimeout(() => (copiedPhone.value = false), 1500);
}






/* ─── 10-minute countdown (server-verified on expiry) ─── */
const remainingSeconds = ref(Math.floor(props.resumePayment?.expires_in_seconds ?? 600));
let timerHandle = null;

// resumePayment ဖြင့် (refresh / KPay app ကနေ ပြန်လာတဲ့အခါ) mount ဖြစ်ရင်
// timer ကို စမလှုပ်ပဲ ရှိနေတာကြောင့် "refresh ပြီးရင် timer အလုပ်မလုပ်" ဖြစ်ခဲ့ပါတယ်
// — ဒီ onMounted က ဒါကို ဖြေရှင်းပေးပါတယ်။ status === 'processing' ဆိုရင်
// screenshot ကို upload လုပ်ပြီးသားမို့ timer ပြန်စဖို့ မလိုတော့ပါဘူး
// (အထက်က step init logic က 'submitted' ကို တန်းပြထားပြီးသားပါ)။
onMounted(() => {
  if (props.resumePayment && props.resumePayment.status !== 'processing') {
    startTimer();
  }
});

function startTimer() {
  if (timerHandle) clearInterval(timerHandle);
  timerHandle = setInterval(() => {
    remainingSeconds.value -= 1;
    if (remainingSeconds.value <= 0) {
      clearInterval(timerHandle);
      confirmExpiry(); // local timer ပြောတာကို မယုံဘဲ backend ကို ပြန်စစ်
    }
  }, 1000);
}

// Timer 0 ရောက်တဲ့အခါ backend ရဲ့ created_at-based calculation ကို
// အမှန်တကယ် ပြန်စစ်ပါတယ် — GET /api/payments/current က lazy-expire
// logic ကို run ပြီး status='failed' အဖြစ် update လုပ်ပြီးမှ null ပြန်ပါတယ်။
async function confirmExpiry() {
  try {
    const res = await fetch('/api/payments/current', {
      headers: { Accept: 'application/json' },
    });
    const json = await res.json();

    if (json.payment && json.payment.ref_code === paymentData.value?.ref_code) {
      if (json.payment.status === 'processing') {
        // ဒီအချိန်ကြားထဲ upload ဟာ အောင်မြင်သွားပြီးသား (ဥပမာ- တခြား tab
        // တစ်ခုကနေ) ဆိုရင် timer ပြန်မစဘဲ submitted screen ကို ပြပါ
        clearInterval(timerHandle);
        step.value = 'submitted';
        return;
      }
      // Server အလိုအရ တကယ်မကုန်သေးရင် (clock drift ဖြစ်နိုင်ချေ) ပြန် sync လုပ်ပြီး ဆက်တွက်ပါ
      remainingSeconds.value = Math.floor(json.payment.expires_in_seconds);
      startTimer();
    } else {
      // Server ကလည်း "expired/failed" လို့ အတည်ပြုပြီးသား
      step.value = 'expired';
    }
  } catch {
    // Network error ဖြစ်ရင်တောင် user ကို stuck screen မမြင်စေချင်တဲ့အတွက်
    // fail-safe အနေနဲ့ expired ပြပါ (user "ထပ်စမ်းရန်" ကနေ restart လုပ်နိုင်ပါ)
    step.value = 'expired';
  }
}






/* ─── Upload ─── */
const uploading = ref(false);
const uploadError = ref(null);

async function onFileChange(e) {
  const file = e.target.files?.[0];
  if (!file || !paymentData.value) return;

  uploading.value = true;
  uploadError.value = null;

  const formData = new FormData();
  formData.append('ref_code', paymentData.value.ref_code);
  formData.append('screenshot', file);

  try {
    const res = await fetch('/api/payments/verify', {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'X-XSRF-TOKEN': csrfToken(),
      },
      body: formData,
    });
    const json = await res.json();

    if (res.status === 410) {
      // Code expired server-side too — match the local countdown's behaviour.
      clearInterval(timerHandle);
      step.value = 'expired';
      return;
    }
    if (!res.ok) throw new Error(json.message || 'Upload မအောင်မြင်ပါ');

    clearInterval(timerHandle);
    step.value = 'submitted';
  } catch (e2) {
    uploadError.value = e2.message;
  } finally {
    uploading.value = false;
    e.target.value = '';
  }
}

const copied = ref(false);

function copyCode() {
  if (!paymentData.value) return;
  navigator.clipboard?.writeText(paymentData.value.ref_code);
  copied.value = true;
  setTimeout(() => (copied.value = false), 1500);
}


/* ─── Manual cancel (user-initiated, distinct from window-expiry) ─── */
const cancelling = ref(false);
const cancelError = ref(null);

async function cancelPayment() {
  if (!paymentData.value || cancelling.value) return;
  cancelling.value = true;
  cancelError.value = null;

  try {
    const res = await fetch('/api/payments/cancel', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-XSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify({ ref_code: paymentData.value.ref_code }),
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'ပယ်ဖျက်ရာတွင် အမှားတစ်ခု ဖြစ်သွားပါသည်');

    if (timerHandle) clearInterval(timerHandle);
    emit('close', 'cancelled');
  } catch (e) {
    cancelError.value = e.message;
  } finally {
    cancelling.value = false;
  }
}

function restart() {
  step.value = 'package';
  paymentData.value = null;
  initiateError.value = null;
  uploadError.value = null;
  phone.value = '';
  remainingSeconds.value = 600;
  if (timerHandle) clearInterval(timerHandle);
}

function handleClose(reason = 'dismissed') {
  if (timerHandle) clearInterval(timerHandle);
  emit('close', reason);
}

onBeforeUnmount(() => {
  if (timerHandle) clearInterval(timerHandle);
});
</script>

<style scoped>
/* Thin, theme-matched scrollbar instead of the bulky default OS one */
.modal-scroll {
  scrollbar-width: thin;
  scrollbar-color: rgba(139, 92, 246, 0.4) transparent;
}

.modal-scroll::-webkit-scrollbar {
  width: 6px;
}

.modal-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.modal-scroll::-webkit-scrollbar-thumb {
  background-color: rgba(139, 92, 246, 0.4);
  border-radius: 999px;
}

.modal-scroll::-webkit-scrollbar-thumb:hover {
  background-color: rgba(139, 92, 246, 0.65);
}
</style>
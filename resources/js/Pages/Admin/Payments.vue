<template>
  <Head title="Payments" />

  <AppSidebar :auth="$page.props.auth">
    <div class="dash-root">
      <!-- Ambient brand orbs -->
      <div class="orb orb-violet"></div>
      <div class="orb orb-cyan"></div>
      <div class="orb orb-gold"></div>

      <main class="dash-main">
        <!-- ═══ Header ═══ -->
        <section class="flex flex-wrap items-end justify-between gap-4">
          <div>
            <div class="mb-2 text-[11px] font-bold uppercase tracking-wider text-violet-400">Admin Panel</div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-50 sm:text-[26px]">Payments</h1>
          </div>
          <AdminNav current="payments" />
        </section>

        <!-- ═══ Status tabs ═══ -->
        <section class="flex flex-wrap gap-2">
          <Link
            v-for="tab in statusTabs"
            :key="tab.value"
            :href="route('admin.payments', { status: tab.value })"
            preserve-scroll
            class="rounded-xl border px-4 py-2 text-xs font-bold transition-all duration-200"
            :class="filters.status === tab.value
              ? 'border-violet-500/50 bg-violet-500/15 text-violet-200'
              : 'border-white/10 bg-white/[0.03] text-slate-400 hover:border-white/20 hover:text-slate-200'"
          >
            {{ tab.label }}
          </Link>
        </section>

        <!-- ═══ Queue ═══ -->
        <section class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 sm:p-7">
          <div class="mb-5 flex items-end justify-between gap-4">
            <div>
              <div class="mb-1.5 text-[11px] font-bold uppercase tracking-wider text-violet-400">Queue</div>
              <h2 class="text-lg font-extrabold text-slate-50">{{ statusLabel }}</h2>
            </div>
            <span class="text-xs text-slate-500">{{ payments.total }} payment(s)</span>
          </div>

          <!-- Mobile: stacked cards -->
          <div class="grid gap-3 sm:hidden">
            <div
              v-for="p in payments.data"
              :key="p.id"
              class="rounded-2xl border border-white/10 bg-white/[0.02] p-4"
            >
              <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-sm font-bold text-slate-200">{{ p.ref_code }}</span>
                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide" :class="statusBadgeClass(p.status)">
                  {{ p.status }}
                </span>
              </div>
              <div class="mt-3 space-y-1.5 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">User</span><span class="text-slate-300">{{ p.user?.username ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Plan</span><span class="text-slate-300">{{ p.plan_key }} · {{ p.duration_days }}d</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Amount</span><span class="font-bold text-slate-100">{{ Number(p.amount).toLocaleString() }} MMK</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Bank</span><span class="text-slate-300">{{ p.bank_type ?? '—' }}</span></div>
              </div>
              <button
                v-if="p.last_error"
                type="button"
                @click="openDetail(p)"
                class="mt-3 block w-full truncate rounded-lg bg-amber-500/[0.08] px-3 py-2 text-left text-[11px] leading-relaxed text-amber-300 hover:bg-amber-500/[0.14]"
              >
                {{ p.last_error }}
              </button>
              <div class="mt-3 flex gap-2">
                <button
                  type="button"
                  @click="openDetail(p)"
                  class="flex-1 rounded-lg border border-white/10 bg-white/[0.04] py-2 text-center text-xs font-bold text-slate-300 hover:bg-white/[0.08]"
                >
                  Details
                </button>
                <template v-if="isReviewable(p.status)">
                  <button
                    type="button"
                    :disabled="processingId === p.id"
                    @click="approve(p)"
                    class="flex-1 rounded-lg bg-emerald-500/15 py-2 text-xs font-bold text-emerald-300 hover:bg-emerald-500/25 disabled:opacity-50"
                  >
                    Approve
                  </button>
                  <button
                    type="button"
                    :disabled="processingId === p.id"
                    @click="reject(p)"
                    class="flex-1 rounded-lg bg-rose-500/15 py-2 text-xs font-bold text-rose-300 hover:bg-rose-500/25 disabled:opacity-50"
                  >
                    Reject
                  </button>
                </template>
              </div>
            </div>

            <p v-if="payments.data.length === 0" class="py-10 text-center text-sm text-slate-500">
              ဒီ status အတွက် payment မရှိသေးပါ။
            </p>
          </div>

          <!-- Desktop / tablet: table -->
          <div class="hidden overflow-x-auto sm:block">
            <table class="w-full min-w-[900px] border-collapse text-left text-sm">
              <thead>
                <tr class="border-b border-white/10 text-[11px] uppercase tracking-wide text-slate-500">
                  <th class="py-2 pr-4 font-semibold">Ref Code</th>
                  <th class="py-2 pr-4 font-semibold">User</th>
                  <th class="py-2 pr-4 font-semibold">Plan</th>
                  <th class="py-2 pr-4 font-semibold">Amount</th>
                  <th class="py-2 pr-4 font-semibold">Bank</th>
                  <th class="py-2 pr-4 font-semibold">Reason</th>
                  <th class="py-2 pr-4 font-semibold">Status</th>
                  <th class="py-2 font-semibold"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="p in payments.data" :key="p.id" class="border-b border-white/5 last:border-0">
                  <td class="py-3.5 pr-4 font-mono font-semibold text-slate-200">{{ p.ref_code }}</td>
                  <td class="py-3.5 pr-4 text-slate-300">{{ p.user?.username ?? '—' }}</td>
                  <td class="py-3.5 pr-4 text-slate-400">{{ p.plan_key }} · {{ p.duration_days }}d</td>
                  <td class="py-3.5 pr-4 font-bold text-slate-50">{{ Number(p.amount).toLocaleString() }} MMK</td>
                  <td class="py-3.5 pr-4 text-slate-400">{{ p.bank_type ?? '—' }}</td>
                  <td class="py-3.5 pr-4 max-w-[220px]">
                    <button
                      v-if="p.last_error"
                      type="button"
                      @click="openDetail(p)"
                      class="block max-w-full truncate text-xs text-amber-300 hover:text-amber-200 hover:underline"
                      :title="p.last_error"
                    >
                      {{ p.last_error }}
                    </button>
                    <span v-else class="text-xs text-slate-600">—</span>
                  </td>
                  <td class="py-3.5 pr-4">
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold" :class="statusBadgeClass(p.status)">
                      {{ p.status }}
                    </span>
                  </td>
                  <td class="py-3.5">
                    <div class="flex items-center gap-1.5">
                      <button
                        type="button"
                        @click="openDetail(p)"
                        class="rounded-lg border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-bold text-slate-300 hover:bg-white/[0.08]"
                      >
                        Details
                      </button>
                      <template v-if="isReviewable(p.status)">
                        <button
                          type="button"
                          :disabled="processingId === p.id"
                          @click="approve(p)"
                          class="rounded-lg bg-emerald-500/15 px-3 py-1.5 text-xs font-bold text-emerald-300 hover:bg-emerald-500/25 disabled:opacity-50"
                        >
                          Approve
                        </button>
                        <button
                          type="button"
                          :disabled="processingId === p.id"
                          @click="reject(p)"
                          class="rounded-lg bg-rose-500/15 px-3 py-1.5 text-xs font-bold text-rose-300 hover:bg-rose-500/25 disabled:opacity-50"
                        >
                          Reject
                        </button>
                      </template>
                    </div>
                  </td>
                </tr>

                <tr v-if="payments.data.length === 0">
                  <td colspan="8" class="py-10 text-center text-sm text-slate-500">
                    ဒီ status အတွက် payment မရှိသေးပါ။
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="payments.links?.length > 3" class="mt-6 flex flex-wrap items-center justify-center gap-1.5">
            <Link
              v-for="(link, i) in payments.links"
              :key="i"
              :href="link.url || '#'"
              preserve-scroll
              class="rounded-lg px-3 py-1.5 text-xs font-semibold"
              :class="[
                link.active ? 'bg-violet-500/20 text-violet-200' : 'text-slate-400 hover:bg-white/[0.05]',
                !link.url ? 'pointer-events-none opacity-30' : '',
              ]"
              v-html="link.label"
            />
          </div>
        </section>
      </main>
    </div>

    <!-- ═══ Payment detail modal (Reason + Screenshot) ═══ -->
    <Teleport to="body">
      <div
        v-if="detailPayment"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md"
      >
        <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto modal-scroll rounded-3xl border border-white/10 bg-[#0b0f1c] p-6 shadow-[0_40px_80px_-20px_rgba(0,0,0,0.85)] ring-1 ring-white/[0.06] sm:p-7">
          <button
            type="button"
            @click="closeDetail"
            class="absolute right-5 top-5 flex h-8 w-8 items-center justify-center rounded-full text-slate-500 transition-colors hover:bg-white/[0.06] hover:text-slate-200"
            aria-label="ပိတ်ရန်"
          >
            ✕
          </button>

          <h3 class="font-mono text-lg font-extrabold text-slate-50">{{ detailPayment.ref_code }}</h3>
          <span class="mt-1 inline-block rounded-full px-2.5 py-1 text-[11px] font-bold" :class="statusBadgeClass(detailPayment.status)">
            {{ detailPayment.status }}
          </span>

          <!-- Info grid -->
          <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 rounded-2xl border border-white/10 bg-white/[0.02] p-4 text-xs">
            <div><span class="text-slate-500">User</span><br /><span class="text-slate-200">{{ detailPayment.user?.username ?? '—' }}</span></div>
            <div><span class="text-slate-500">Plan</span><br /><span class="text-slate-200">{{ detailPayment.plan_key }} · {{ detailPayment.duration_days }}d</span></div>
            <div><span class="text-slate-500">Amount</span><br /><span class="font-bold text-slate-100">{{ Number(detailPayment.amount).toLocaleString() }} MMK</span></div>
            <div><span class="text-slate-500">Bank</span><br /><span class="text-slate-200">{{ detailPayment.bank_type ?? '—' }}</span></div>
            <div><span class="text-slate-500">Sender Phone</span><br /><span class="text-slate-200">{{ detailPayment.sender_phone ?? '—' }}</span></div>
            <div><span class="text-slate-500">Transaction ID</span><br /><span class="font-mono text-slate-200">{{ detailPayment.transaction_id ?? '—' }}</span></div>
            <div class="col-span-2"><span class="text-slate-500">Note (OCR)</span><br /><span class="text-slate-200">{{ detailPayment.note ?? '—' }}</span></div>
          </div>

          <!-- Reason -->
          <div v-if="detailPayment.last_error" class="mt-4 rounded-2xl border border-amber-500/25 bg-amber-500/[0.08] p-4">
            <p class="text-[11px] font-bold uppercase tracking-wide text-amber-400">Reason</p>
            <p class="mt-1.5 text-sm leading-relaxed text-amber-200">{{ detailPayment.last_error }}</p>
          </div>

          <!-- Screenshot -->
          <div class="mt-4">
            <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">Screenshot</p>
            <img
              v-if="detailPayment.screenshot_path"
              :src="route('admin.payments.screenshot', detailPayment.id)"
              class="w-full rounded-2xl border border-white/10 object-contain"
              alt="Payment screenshot"
            />
            <p v-else class="text-sm text-slate-600">Screenshot မတင်ရသေးပါ။</p>
          </div>

          <!-- Actions -->
          <div v-if="isReviewable(detailPayment.status)" class="mt-5 flex gap-2">
            <button
              type="button"
              :disabled="processingId === detailPayment.id"
              @click="approve(detailPayment)"
              class="flex-1 rounded-xl bg-emerald-500/15 py-2.5 text-sm font-bold text-emerald-300 hover:bg-emerald-500/25 disabled:opacity-50"
            >
              Approve
            </button>
            <button
              type="button"
              :disabled="processingId === detailPayment.id"
              @click="reject(detailPayment)"
              class="flex-1 rounded-xl bg-rose-500/15 py-2.5 text-sm font-bold text-rose-300 hover:bg-rose-500/25 disabled:opacity-50"
            >
              Reject
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import AdminNav from '@/Components/Admin/AdminNav.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  payments: Object,
  filters: Object,
});

const statusTabs = [
  { value: 'manual_review', label: 'Awaiting Review' },
  { value: 'success', label: 'Approved' },
  { value: 'failed', label: 'Failed' },
  { value: 'cancelled', label: 'Cancelled' },
  { value: 'all', label: 'All' },
];

const statusLabel = computed(
  () => statusTabs.find(t => t.value === props.filters.status)?.label ?? 'Payments'
);

const processingId = ref(null);
const detailPayment = ref(null);

function openDetail(payment) {
  detailPayment.value = payment;
}

function closeDetail() {
  detailPayment.value = null;
}

function isReviewable(status) {
  return status === 'manual_review' || status === 'processing';
}

function statusBadgeClass(status) {
  return {
    'bg-emerald-500/15 text-emerald-300': status === 'success',
    'bg-amber-500/15 text-amber-300': status === 'manual_review',
    'bg-rose-500/15 text-rose-300': status === 'failed' || status === 'cancelled',
    'bg-white/[0.06] text-slate-400': status === 'pending' || status === 'processing',
  };
}

function approve(payment) {
  if (!confirm(`${payment.ref_code} ကို approve လုပ်ပြီး Plan ပေးမလား?`)) return;
  processingId.value = payment.id;
  router.post(
    route('admin.payments.approve', payment.id),
    {},
    { preserveScroll: true, onFinish: () => { processingId.value = null; closeDetail(); } }
  );
}

function reject(payment) {
  const reason = prompt('Reject လုပ်ရတဲ့ အကြောင်းရင်း (optional):') ?? '';
  processingId.value = payment.id;
  router.post(
    route('admin.payments.reject', payment.id),
    { reason },
    { preserveScroll: true, onFinish: () => { processingId.value = null; closeDetail(); } }
  );
}
</script>

<style scoped>
.dash-root {
  background: #080b14;
  color: #f1f5f9;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  position: relative;
  overflow: hidden;
}
.orb { position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0; }
.orb-violet { width: 560px; height: 560px; background: rgba(124, 58, 237, 0.18); top: -120px; left: -160px; }
.orb-cyan   { width: 460px; height: 460px; background: rgba(6, 182, 212, 0.14); top: 400px; right: -160px; }
.orb-gold   { width: 280px; height: 280px; background: rgba(245, 158, 11, 0.08); bottom: -100px; left: 30%; }
.dash-main {
  position: relative; z-index: 5;
  max-width: 1100px; margin: 0 auto;
  padding: 40px 24px 100px;
  display: flex; flex-direction: column; gap: 28px;
}
@media (max-width: 768px) {
  .dash-main { padding: 80px 16px 80px; gap: 20px; }
}

/* Thin, theme-matched scrollbar — same as BuyNowModal.vue's modal-scroll */
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
<template>
  <Head title="Job History" />

  <AppSidebar :auth="$page.props.auth">

  <div class="dash-root">
    <!-- Ambient brand orbs (same as Dashboard) -->
    <div class="orb orb-violet"></div>
    <div class="orb orb-cyan"></div>
    <div class="orb orb-gold"></div>

    <main class="dash-main">

      <!-- ═══════════════ PAGE HEADER ═══════════════ -->
      <section
        class="flex flex-wrap items-center justify-between gap-4 rounded-[24px] px-6 py-6 sm:px-8"
        style="background: rgba(255,255,255,0.035); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(18px);"
      >
        <div>
          <div class="card-eyebrow">Generation History</div>
          <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight" style="color:#F1F5F9;">
            Job History
          </h1>
        </div>

        <!-- Today usage mini box -->
        <div
          class="flex items-center gap-4 rounded-2xl px-5 py-3"
          style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);"
        >
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wide" style="color:#64748B;">Today Usage</p>
            <p class="text-lg font-extrabold" style="color:#F1F5F9;">
              {{ today_used }}<span class="text-sm font-semibold" style="color:#64748B;"> / {{ dailyLimitDisplay }}</span>
            </p>
          </div>
          <div class="usage-mini-track">
            <div
              class="usage-mini-fill"
              :class="todayUsageClass"
              :style="{ width: todayUsagePercent + '%' }"
            ></div>
          </div>
        </div>
      </section>

      <!-- ═══════════════ JOB HISTORY TABLE ═══════════════ -->
      <section class="posts-section">
        <div class="posts-header">
          <div>
            <div class="card-eyebrow">Recap Jobs</div>
            <h2 class="posts-title">All Generations</h2>
          </div>
        </div>

        <!-- Desktop table -->
        <div class="posts-table-wrap">
          <table class="posts-table">
            <thead>
              <tr>
                <th>Status</th>
                <th>Started</th>
                <th>Duration</th>
                <th>Expires</th>
                <th class="th-right">Download</th>
              </tr>
            </thead>
            <tbody v-if="jobs.data && jobs.data.length">
              <tr v-for="job in jobs.data" :key="job.id">
                <td>
                  <span class="status-pill" :class="statusPillClass(job.status)">
                    <span class="status-dot"></span>
                    {{ statusLabel(job.status) }}
                  </span>
                  <p v-if="job.status === 'failed' && job.error" class="job-error">{{ job.error }}</p>
                </td>

                <td class="td-date">{{ job.started_at || formatDate(job.created_at) }}</td>

                <td class="td-date">{{ job.duration || (job.status !== 'success' && job.status !== 'failed' ? 'Running…' : '—') }}</td>

                <td class="td-date">
                  <span v-if="job.expires_at" :class="job.is_expired ? 'expiry-expired' : 'expiry-active'">
                    {{ timeLeftLabel(job.expires_at, job.is_expired) }}
                  </span>
                  <span v-else>—</span>
                </td>

                <td class="td-actions">
                  <DownloadButton :job="job" :is-paid="is_paid" :role-name="role_name" />
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="5" class="empty-row">No job history yet.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile cards -->
        <div class="posts-cards">
          <div v-for="job in jobs.data" :key="job.id" class="post-card">
            <div class="post-card-body w-full">
              <div class="post-card-meta" style="display:flex; justify-content:space-between; align-items:center;">
                <span class="status-pill" :class="statusPillClass(job.status)">
                  <span class="status-dot"></span>
                  {{ statusLabel(job.status) }}
                </span>
                <span class="td-date">{{ job.started_at || formatDate(job.created_at) }}</span>
              </div>

              <div style="display:flex; justify-content:space-between; margin-top:4px;">
                <span class="progress-text">{{ job.duration || (job.status !== 'success' && job.status !== 'failed' ? 'Running…' : '—') }}</span>
                <span class="td-date">
                  <span v-if="job.expires_at" :class="job.is_expired ? 'expiry-expired' : 'expiry-active'">
                    {{ timeLeftLabel(job.expires_at, job.is_expired) }}
                  </span>
                </span>
              </div>

              <p v-if="job.status === 'failed' && job.error" class="job-error">{{ job.error }}</p>

              <div class="mt-3">
                <DownloadButton :job="job" :is-paid="is_paid" :role-name="role_name" full-width />
              </div>
            </div>
          </div>
          <p v-if="!jobs.data || jobs.data.length === 0" class="empty-row empty-row-mobile">No job history yet.</p>
        </div>

        <div class="pagination-wrap">
          <Pagination v-if="jobs.links" :links="jobs.links" />
        </div>
      </section>

    </main>
  </div>

  <!-- ═══════════════ ERROR / WARNING / INFO DIALOG ═══════════════ -->
  <transition name="alert-fade">
    <div v-if="showErrorOverlay"
      class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 px-4"
      @click.self="closeErrorPopup">
      <div class="w-full max-w-sm rounded-2xl shadow-2xl p-6 border" :class="{
        'bg-[#0D1120] border-red-500/25': alertType === 'error',
        'bg-[#0D1120] border-amber-500/25': alertType === 'warning',
        'bg-[#0D1120] border-[#7C3AED]/25': alertType === 'info',
      }">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
          <div v-if="alertType === 'error'"
            class="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
          </div>
          <div v-else-if="alertType === 'warning'"
            class="w-16 h-16 rounded-full bg-amber-500/10 flex items-center justify-center">
            <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
          </div>
          <div v-else class="w-16 h-16 rounded-full bg-[#7C3AED]/10 flex items-center justify-center">
            <svg class="w-8 h-8 text-[#A78BFA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
        </div>
        <!-- Title -->
        <h3 class="text-center text-lg font-bold text-[#F1F5F9] mb-2">
          <span v-if="alertType === 'error'">လုပ်ဆောင်မှု မအောင်မြင်ပါ</span>
          <span v-else-if="alertType === 'warning'">သတိပေးချက်</span>
          <span v-else>Unavailable</span>
        </h3>
        <p class="text-center text-sm text-[#94A3B8] mb-6 leading-relaxed">{{ errorPopupMsg }}</p>
        <!-- Buttons -->
        <div class="flex flex-col gap-2">
          <button @click="closeErrorPopup"
            class="w-full bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] hover:opacity-90 text-white font-semibold py-2.5 rounded-xl border-none cursor-pointer transition-opacity">
            နားလည်ပါပြီ
          </button>
          <a v-if="alertType === 'warning'" :href="telegramUrl" target="_blank" rel="noopener"
            class="block text-center w-full bg-[rgba(255,255,255,0.06)] hover:bg-[rgba(255,255,255,0.1)] text-[#94A3B8] text-sm font-medium py-2.5 rounded-xl no-underline transition-colors">
            Telegram Group ကို ဝင်ရောက်ရန်
          </a>
        </div>
      </div>
    </div>
  </transition>

  </AppSidebar>
</template>

<script setup>
import AppSidebar from '@/Components/AppSidebar.vue';
import { Head } from '@inertiajs/vue3';
import { computed, defineComponent, h, ref, onMounted, onUnmounted } from 'vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  jobs: Object,
  is_paid: Boolean,
  role_name: String,
  today_used: { type: Number, default: 0 },
  daily_limit: { type: Number, default: 0 },
});

/* ───────── Today usage mini bar ───────── */
const dailyLimitDisplay = computed(() => (props.daily_limit > 0 ? props.daily_limit : '∞'));
const todayUsagePercent = computed(() => {
  if (props.daily_limit <= 0) return 0;
  return Math.min((props.today_used / props.daily_limit) * 100, 100);
});
const todayUsageClass = computed(() => {
  if (props.daily_limit > 0 && props.today_used >= props.daily_limit) return 'usage-mini-danger';
  if (todayUsagePercent.value > 70) return 'usage-mini-warning';
  return 'usage-mini-ok';
});

/* ───────── Live "time left" countdown for expiry ───────── */
const nowTick = ref(Date.now());
let tickTimer = null;
onMounted(() => {
  tickTimer = setInterval(() => { nowTick.value = Date.now(); }, 30000);
});
onUnmounted(() => clearInterval(tickTimer));

function timeLeftLabel(expiresAt, isExpired) {
  if (!expiresAt) return '—';
  if (isExpired) return 'Expired';

  const diffMs = new Date(expiresAt).getTime() - nowTick.value;
  if (diffMs <= 0) return 'Expired';

  const diffMin = Math.floor(diffMs / 60000);
  if (diffMin < 1) return 'Under 1m left';
  if (diffMin < 60) return `${diffMin}m left`;

  const diffH = Math.floor(diffMin / 60);
  const remMin = diffMin % 60;
  if (diffH < 24) return `${diffH}h ${remMin}m left`;

  const diffD = Math.floor(diffH / 24);
  const remH = diffH % 24;
  return `${diffD}d ${remH}h left`;
}
function statusLabel(status) {
  if (status === 'success') return 'Success';
  if (status === 'failed') return 'Failed';
  return 'Processing';
}
function statusPillClass(status) {
  if (status === 'success') return 'status-active';
  if (status === 'failed') return 'status-inactive';
  return 'status-processing';
}
function formatDate(value) {
  if (!value) return '—';
  const d = new Date(value);
  if (isNaN(d.getTime())) return value;
  return d.toLocaleString(undefined, {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
}

/* ─────────────────────────────────────────────────────────────
   Error / warning / info dialog (replaces raw JSON / plain text)
───────────────────────────────────────────────────────────────*/
const showErrorOverlay = ref(false);
const alertType = ref('error'); // 'error' | 'warning' | 'info'
const errorPopupMsg = ref('');
const telegramUrl = 'https://t.me/your_support_group'; // TODO: replace with your real group link

function showError(msg, type = 'error') {
  errorPopupMsg.value = msg;
  alertType.value = type;
  showErrorOverlay.value = true;
}
function closeErrorPopup() {
  showErrorOverlay.value = false;
}

/**
 * Never trust the upstream (FastAPI) message body for user-facing text —
 * it can leak internal details ("Authentication failed: Session ...").
 * Map by HTTP status to a friendly, translated message instead.
 */
function friendlyDownloadError(status) {
  switch (status) {
    case 401:
      return { msg: 'Login သက်တမ်း ကုန်သွားပါပြီ။ ပြန်လည် Login ဝင်ပြီး ထပ်ကြိုးစားပါ။', type: 'info' };
    case 403:
      return { msg: 'ဤဖိုင်ကို Download ဆွဲရန် ခွင့်ပြုချက် မရှိပါ။', type: 'warning' };
    case 404:
      return { msg: 'ဖိုင်ကို ရှာမတွေ့ပါ။ Generate လုပ်ဆောင်မှု ပြီးမြောက်ခြင်း ရှိမရှိ စစ်ဆေးပါ။', type: 'error' };
    case 410:
      return { msg: 'Download link သက်တမ်း ကုန်သွားပါပြီ။', type: 'warning' };
    case 429:
      return { msg: 'ဒီနေ့အတွက် Download limit ပြည့်သွားပါပြီ။ မနက်ဖြန် ထပ်ကြိုးစားပါ။', type: 'warning' };
    case 502:
    case 503:
    case 504:
      return { msg: 'Server နှင့် ချိတ်ဆက်မှု ခေတ္တ မအောင်မြင်ပါ။ ခဏနေမှ ထပ်ကြိုးစားပါ။', type: 'error' };
    default:
      return { msg: 'Download လုပ်ဆောင်မှု မအောင်မြင်ပါ။ ခဏနေမှ ထပ်ကြိုးစားပါ။', type: 'error' };
  }
}

/* ─────────────────────────────────────────────────────────────
   Download flow — fetch + blob, NO page navigation/redirect
───────────────────────────────────────────────────────────────*/
const downloadingIds = ref(new Set());

async function downloadJob(job) {
  if (downloadingIds.value.has(job.id)) return;
  downloadingIds.value.add(job.id);

  try {
    const res = await fetch(route('jobs.download', job.id), {
      headers: {
        'Accept': 'application/json',
        'X-Session-ID': window.APP_SESSION_ID,
      },
      credentials: 'same-origin',
    });

    if (!res.ok) {
      // Log real detail for debugging, but never show it to the user.
      try {
        const errJson = await res.json();
        console.warn('Download failed:', res.status, errJson);
      } catch (_) {
        console.warn('Download failed:', res.status);
      }
      const { msg, type } = friendlyDownloadError(res.status);
      showError(msg, type);
      return;
    }

    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'Recap_Ready.mp4';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  } catch (err) {
    console.warn('Download network error:', err);
    showError('Internet ချိတ်ဆက်မှု ပြဿနာ ဖြစ်နေပါသည်။ ချိတ်ဆက်မှုကို စစ်ဆေးပြီး ထပ်ကြိုးစားပါ။', 'error');
  } finally {
    downloadingIds.value.delete(job.id);
  }
}

/* ─────────────────────────────────────────────────────────────
   Inline DownloadButton
   - tester role                          -> disabled "Upgrade" + dialog
   - success + no expires_at recorded     -> disabled "Not available"
     (this happens for jobs generated back when the account was
     still on the tester role — no expiry was ever tracked for
     those, so we can't safely offer them for download)
   - success + expires_at passed          -> disabled "Expired"
   - anything else / still processing     -> disabled "Not ready"
   - success + expires_at + not expired   -> real Download
───────────────────────────────────────────────────────────────*/
function showUpgradeRequired() {
  showError('ဤ Recap ဗီဒီယိုကို Download ဆွဲရန် Paid Plan လိုအပ်ပါသည်။', 'warning');
}
function showNotAvailable() {
  showError('ဤ Recap ဗီဒီယိုသည် Tester စမ်းသပ်ကာလအတွင်း ထုတ်လုပ်ခဲ့ခြင်းဖြစ်၍ Download အတွက် မရနိုင်တော့ပါ။', 'info');
}

const DownloadButton = defineComponent({
  props: { job: Object, roleName: String, isPaid: Boolean, fullWidth: Boolean },
  setup(p) {
    return () => {
      const isSuccess = p.job.status === 'success';
      const noExpiryRecorded = isSuccess && !p.job.expires_at;

      // ✅ FIX: current role (isTester) ကို check မလုပ်တော့ဘူး —
      //    job ကိုယ်တိုင်က success + expires_at ရှိ + မကုန်သေး ဆိုရင်
      //    user ရဲ့ CURRENT role ဘာဖြစ်ဖြစ် (rollback ဖြစ်ခဲ့ရင်တောင်)
      //    ဒီ job ကို download ခွင့်ပြုရမယ် — paid time က generate
      //    ခဲ့တာမို့ ဒီ file ရဲ့ entitlement ဟာ job ရဲ့ expires_at
      //    ကနေပဲ ဆုံးဖြတ်သင့်တယ်, current role ကနေ မဟုတ်ပါ
      const canDownload = isSuccess && !noExpiryRecorded && !p.job.is_expired;
      const isDownloading = downloadingIds.value.has(p.job.id);

      if (canDownload) {
        return h('button', {
          type: 'button',
          disabled: isDownloading,
          class: ['dl-btn', 'dl-btn-active', p.fullWidth ? 'w-full justify-center' : ''],
          onClick: () => downloadJob(p.job),
        }, [dlIcon(), ' ' + (isDownloading ? 'ဆွဲနေသည်...' : 'Download')]);
      }

      // ── Download မရနိုင်တဲ့ path (canDownload=false) — ဘာလို့လဲ label ခွဲပြပါ ──
      let label, onRestrictedClick;

      if (noExpiryRecorded) {
        // ✅ FIX: expires_at ကို job တစ်ခုချင်းစီအလိုက်ပဲ ကြည့်တယ်.
        //    Tester job (local storage, expiry မရှိ) က ဒီနေရာကို ရောက်လာမယ်
        //    — current role ကို ကြည့်ပြီး message ကို personalize လုပ်ပေးရုံ
        //    (behavior ကတော့ "Not available/Upgrade" ၂ ခုလုံး disabled အတူတူပါ)
        if (p.roleName === 'tester') {
          label = 'Upgrade';
          onRestrictedClick = showUpgradeRequired;
        } else {
          label = 'Not available';
          onRestrictedClick = showNotAvailable;
        }
      } else if (p.job.is_expired) {
        label = 'Expired';
        onRestrictedClick = null;
      } else {
        label = 'Not ready';
        onRestrictedClick = null;
      }

      return h('button', {
        type: 'button',
        class: ['dl-btn', 'dl-btn-disabled', p.fullWidth ? 'w-full justify-center' : ''],
        onClick: () => { if (onRestrictedClick) onRestrictedClick(); },
      }, [dlIcon(), ' ' + label]);
    };
  },
});

function dlIcon() {
  return h('svg', {
    width: 14, height: 14, viewBox: '0 0 24 24', fill: 'none',
    stroke: 'currentColor', 'stroke-width': 2.2,
  }, [
    h('path', { d: 'M12 3v12m0 0l-4-4m4 4l4-4', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }),
    h('path', { d: 'M5 19h14', 'stroke-linecap': 'round' }),
  ]);
}
</script>

<style scoped>
/* ─── Reuse Dashboard.vue tokens ─── */
.card-eyebrow {
  font-size: 11px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 1.5px; color: #7C3AED; margin-bottom: 10px;
}

.posts-section {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 22px; padding: 28px;
}
.posts-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.posts-title { font-size: 22px; font-weight: 800; color: #F1F5F9; letter-spacing: -0.4px; }

.posts-table-wrap { overflow-x: auto; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; }
.posts-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.posts-table thead { background: rgba(255,255,255,0.03); }
.posts-table th { text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #64748B; }
.th-right { text-align: right; }
.posts-table td { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,0.05); vertical-align: middle; }
.posts-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.td-date { color: #64748B; font-size: 13px; }
.td-actions { text-align: right; white-space: nowrap; }
.empty-row { text-align: center; padding: 40px 16px; color: #64748B; font-size: 14px; }

.status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 100px; }
.status-active     { background: rgba(52,211,153,0.12); color: #34D399; }
.status-inactive    { background: rgba(239,68,68,0.12); color: #F87171; }
.status-processing { background: rgba(6,182,212,0.12); color: #67E8F9; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.progress-track { width: 100%; height: 8px; border-radius: 100px; background: rgba(255,255,255,0.07); overflow: hidden; }
.progress-fill { height: 100%; border-radius: 100px; transition: width 0.4s ease; }
.progress-success { background: #34D399; }
.progress-failed  { background: #F87171; }
.progress-text { font-size: 11.5px; color: #94A3B8; margin-top: 4px; display: inline-block; }
.job-error { font-size: 11.5px; color: #F87171; margin-top: 4px; }

.expiry-active  { color: #94A3B8; }
.expiry-expired { color: #F87171; font-weight: 600; }

/* Mobile cards */
.posts-cards { display: none; flex-direction: column; gap: 14px; }
.post-card { display: flex; gap: 14px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 14px; }
.post-card-body { flex: 1; min-width: 0; }
.post-card-meta { margin-bottom: 10px; }
.empty-row-mobile { text-align: center; padding: 30px 0; color: #64748B; font-size: 14px; }

.pagination-wrap { margin-top: 24px; }

/* Download button */
.dl-btn {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12.5px; font-weight: 700; padding: 8px 14px;
  border-radius: 100px; text-decoration: none; cursor: pointer;
  font-family: inherit; border: 1px solid transparent; transition: all 0.15s ease;
}
.dl-btn-active {
  background: rgba(124,58,237,0.15); border-color: rgba(124,58,237,0.4); color: #C4B5FD;
}
.dl-btn-active:hover { background: rgba(124,58,237,0.25); }
.dl-btn-active:disabled { opacity: 0.6; cursor: wait; }
.dl-btn-disabled {
  background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.08); color: #475569;
  cursor: not-allowed;
}

/* Today usage mini bar */
.usage-mini-track { width: 90px; height: 6px; border-radius: 100px; background: rgba(255,255,255,0.07); overflow: hidden; }
.usage-mini-fill { height: 100%; border-radius: 100px; transition: width 0.4s ease; }
.usage-mini-ok      { background: #06B6D4; }
.usage-mini-warning { background: #F59E0B; }
.usage-mini-danger  { background: #F87171; }

/* Dialog transition */
.alert-fade-enter-active, .alert-fade-leave-active { transition: opacity 0.2s ease; }
.alert-fade-enter-from, .alert-fade-leave-to { opacity: 0; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .posts-section { padding: 20px; }
  .posts-table-wrap { display: none; }
  .posts-cards { display: flex; }
}
</style>
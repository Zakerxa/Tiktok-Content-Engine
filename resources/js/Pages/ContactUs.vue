<!--
  Drop this in as ContactUs.vue. Same pattern as Home.vue.

  TODO before publishing:
  - Replace the placeholder email / Telegram / TikTok links below with your real ones
    (search "REPLACE_ME" in this file).
  - The contact form currently builds a mailto: link on submit, so it works with zero
    backend. If you'd rather POST it to a real endpoint, swap out `submitForm()` for
    a fetch() call — the form fields are already collected for you.

  Suggested route: /contact
-->
<template>
  <div class="bg-[#080B14] text-[#F1F5F9] font-[Inter,_Segoe_UI,_sans-serif] min-h-screen overflow-x-hidden">

    <AppNavbar :auth="auth" />

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="relative pt-[120px] px-6 pb-16 overflow-hidden text-center">
      <div class="absolute rounded-full blur-[80px] pointer-events-none w-[500px] h-[500px] bg-[rgba(124,58,237,0.2)] -top-[100px] -left-[150px]"></div>
      <div class="absolute rounded-full blur-[80px] pointer-events-none w-[400px] h-[400px] bg-[rgba(6,182,212,0.15)] top-[80px] -right-[100px]"></div>

      <div class="relative z-10 max-w-[700px] mx-auto">
        <div class="inline-flex items-center gap-2 bg-[rgba(124,58,237,0.15)] border border-[rgba(124,58,237,0.35)] text-[#A78BFA] text-[13px] font-medium px-4 py-1.5 rounded-full mb-8">
          <span class="w-[7px] h-[7px] bg-[#7C3AED] rounded-full"></span>
          Get in Touch
        </div>

        <h1 class="text-[clamp(36px,6vw,56px)] font-black leading-[1.1] tracking-[-1.5px] text-[#F1F5F9] mb-5">
          Questions? We read<br/>
          <span class="bg-gradient-to-br from-[#7C3AED] via-[#06B6D4] to-[#F59E0B] bg-clip-text text-transparent">everything.</span>
        </h1>

        <p class="text-[17px] text-[#94A3B8] leading-[1.7]">
          Bug report, billing question, copyright concern, or just an idea — reach out and a real
          person gets back to you, usually within a day.
        </p>
      </div>
    
      <div class="max-w-[1100px] mx-auto grid grid-cols-1 lg:grid-cols-5 gap-10 mt-40">

        <!-- LEFT: channels -->
        <div class="lg:col-span-2 flex flex-col gap-4">
          <a href="mailto:admin@zakerxa.com" class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-5 flex items-center gap-4 no-underline hover:border-[rgba(124,58,237,0.35)] hover:bg-[rgba(124,58,237,0.06)] transition-colors">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-[rgba(124,58,237,0.15)] flex items-center justify-center text-xl">📧</span>
            <div>
              <h3 class="text-sm font-bold text-[#F1F5F9]">Email</h3>
              <p class="text-sm text-[#94A3B8]">admin@zakerxa.com</p>
            </div>
          </a>

          <a href="https://t.me/+PgQX0UwLGIRhYTU1" target="_blank" rel="noopener" class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-5 flex items-center gap-4 no-underline hover:border-[rgba(6,182,212,0.35)] hover:bg-[rgba(6,182,212,0.06)] transition-colors">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-[rgba(6,182,212,0.15)] flex items-center justify-center text-xl">📣</span>
            <div>
              <h3 class="text-sm font-bold text-[#F1F5F9]">Telegram Channel</h3>
              <p class="text-sm text-[#94A3B8]">Product updates &amp; announcements</p>
            </div>
          </a>

          <a href="https://t.me/+6hc4y3AceQJmNTQ1" target="_blank" rel="noopener" class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-5 flex items-center gap-4 no-underline hover:border-[rgba(6,182,212,0.35)] hover:bg-[rgba(6,182,212,0.06)] transition-colors">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-[rgba(6,182,212,0.15)] flex items-center justify-center text-xl">💬</span>
            <div>
              <h3 class="text-sm font-bold text-[#F1F5F9]">Telegram Group</h3>
              <p class="text-sm text-[#94A3B8]">Get help &amp; talk with other creators</p>
            </div>
          </a>

          <a href="https://www.tiktok.com/@z.a.k.e.r.x.a" target="_blank" rel="noopener" class="bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-2xl p-5 flex items-center gap-4 no-underline hover:border-[rgba(245,158,11,0.35)] hover:bg-[rgba(245,158,11,0.06)] transition-colors">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-[rgba(245,158,11,0.15)] flex items-center justify-center text-xl">🎵</span>
            <div>
              <h3 class="text-sm font-bold text-[#F1F5F9]">TikTok</h3>
              <p class="text-sm text-[#94A3B8]">Follow along, see what's possible</p>
            </div>
          </a>

          <p class="text-xs text-[#475569] mt-2 leading-relaxed">
            Copyright or DMCA concern about a recap made with our platform? Email us with the
            "Copyright / DMCA" subject so it gets routed straight to the right person.
          </p>
        </div>

        <!-- RIGHT: form -->
        <div class="lg:col-span-3 bg-[rgba(255,255,255,0.03)] border border-[rgba(255,255,255,0.08)] rounded-3xl p-8">
          <h2 class="text-xl font-bold text-[#F1F5F9] mb-6">Send a message</h2>

          <form @submit.prevent="submitForm" class="flex flex-col gap-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">Name</label>
                <input v-model="form.name" type="text" required
                  class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] outline-none transition-all focus:border-[#7C3AED] focus:ring-4 focus:ring-[#7C3AED]/15 placeholder:text-[#64748B]"
                  placeholder="Your name" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">Email</label>
                <input v-model="form.email" type="email" required
                  class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] outline-none transition-all focus:border-[#7C3AED] focus:ring-4 focus:ring-[#7C3AED]/15 placeholder:text-[#64748B]"
                  placeholder="you@example.com" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">Topic</label>
              <select v-model="form.topic"
                class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] cursor-pointer outline-none focus:border-[#7C3AED]">
                <option value="General">General question</option>
                <option value="Billing">Billing &amp; plans</option>
                <option value="Bug">Bug report</option>
                <option value="Feature">Feature request</option>
                <option value="Copyright">Copyright / DMCA</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-[#CBD5E1] mb-2">Message</label>
              <textarea v-model="form.message" required rows="5"
                class="w-full px-4 py-3 rounded-xl border border-[rgba(255,255,255,0.1)] bg-[rgba(255,255,255,0.03)] text-sm text-[#F1F5F9] outline-none transition-all focus:border-[#7C3AED] focus:ring-4 focus:ring-[#7C3AED]/15 placeholder:text-[#64748B] resize-none"
                placeholder="What's on your mind?"></textarea>
            </div>

            <button type="submit"
              class="w-full bg-gradient-to-br from-[#7C3AED] to-[#06B6D4] hover:opacity-90 text-white font-bold py-3.5 rounded-xl border-none cursor-pointer transition-opacity">
              Send Message
            </button>
          </form>
        </div>
      </div>
    </section>

    <AppFooter />
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';

defineProps({
  auth: Object,
});

const form = reactive({
  name: '',
  email: '',
  topic: 'General',
  message: '',
});

// No backend wired up yet — this opens the user's email client with everything
// pre-filled, so the form works the moment you ship it. Swap this out for a
// fetch() POST to your own endpoint whenever you're ready to handle it server-side.
function submitForm() {
  const subject = encodeURIComponent(`[${form.topic}] Message from ${form.name}`);
  const body = encodeURIComponent(`${form.message}\n\n— ${form.name} (${form.email})`);
  window.location.href = `mailto:admin@zakerxa.com?subject=${subject}&body=${body}`;
}
</script>
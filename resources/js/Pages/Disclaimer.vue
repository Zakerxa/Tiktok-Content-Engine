<!--
  Drop this in as Disclaimer.vue. Same pattern as Home.vue.

  IMPORTANT — read before publishing:
  This is a draft, not legal advice. The copyright/fair-use section in particular
  is the one section I'd strongly recommend a lawyer look at — "fair use" and
  copyright risk for movie-recap content is genuinely fact-specific and varies
  by jurisdiction, and TikTok's own enforcement is automated and can flag content
  regardless of what any disclaimer says. This page sets expectations with your
  users; it doesn't change what TikTok's copyright system will or won't flag.

  Replace admin placeholders before shipping.

  Suggested route: /disclaimer
-->
<template>
  <div class="bg-[#080B14] text-[#F1F5F9] font-[Inter,_Segoe_UI,_sans-serif] min-h-screen overflow-x-hidden">

    <AppNavbar :auth="auth" />

    <!-- ═══════════════ HEADER ═══════════════ -->
    <section class="pt-[120px] pb-12 px-6 text-center">
      <div class="text-xs font-bold uppercase tracking-[2px] text-[#7C3AED] mb-3">Legal</div>
      <h1 class="text-[clamp(32px,5vw,48px)] font-extrabold text-[#F1F5F9] tracking-[-1px] mb-3">Disclaimer</h1>
      <p class="text-sm text-[#64748B]">Last updated: June 29, 2026</p>
    </section>

    <!-- ═══════════════ BODY ═══════════════ -->
    <section class="pb-24 px-6">
      <div class="max-w-[1100px] mx-auto grid grid-cols-1 lg:grid-cols-4 gap-12">

        <!-- TOC -->
        <nav class="lg:col-span-1 lg:sticky lg:top-24 self-start hidden lg:block">
          <p class="text-xs font-bold uppercase tracking-[1.5px] text-[#64748B] mb-3">On this page</p>
          <ul class="flex flex-col gap-2.5 text-sm">
            <li><a href="#general" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">General</a></li>
            <li><a href="#copyright" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Copyright &amp; Fair Use</a></li>
            <li><a href="#responsibility" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Your Responsibility</a></li>
            <li><a href="#ai" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">AI-Generated Content</a></li>
            <li><a href="#warranty" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">No Warranty</a></li>
            <li><a href="#liability" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Limitation of Liability</a></li>
            <li><a href="#thirdparty" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Third-Party Links</a></li>
            <li><a href="#availability" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Service Availability</a></li>
            <li><a href="#changes" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Changes</a></li>
            <li><a href="#contact" class="text-[#94A3B8] hover:text-[#A78BFA] no-underline transition-colors">Contact Us</a></li>
          </ul>
        </nav>

        <!-- Content -->
        <div class="lg:col-span-3 flex flex-col gap-10 text-[15px] text-[#94A3B8] leading-[1.8]">

          <div id="general">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">General</h2>
            <p>
              Z.A.K.E.R.X.A provides tools to help you create movie recap videos and TikTok content.
              This disclaimer explains the limits of what we can promise about that output. It should
              be read alongside our <a href="/privacy-policy" class="text-[#A78BFA] no-underline hover:underline">Privacy Policy</a>.
            </p>
          </div>

          <div id="copyright">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Copyright &amp; Fair Use</h2>
            <p class="mb-3">
              Our blur, watermark, and editing tools are designed to <strong class="text-[#CBD5E1]">reduce</strong> the
              chance of a copyright claim against your recap — they do not, and cannot, guarantee that
              a video will be free of copyright issues. Whether a specific recap qualifies as fair use
              or transformative work depends on the facts of that video, the law in your jurisdiction,
              and TikTok's own (automated and frequently changing) copyright enforcement systems.
            </p>
            <p>
              We make no representation or warranty that content created using Z.A.K.E.R.X.A will not
              be flagged, muted, taken down, or result in a strike against your TikTok account.
            </p>
          </div>

          <div id="responsibility">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Your Responsibility</h2>
            <p>
              You are solely responsible for the source material you upload and for ensuring your
              final content complies with applicable copyright law, TikTok's Community Guidelines and
              Terms of Service, and any other platform you publish to. Do not upload content you do
              not have the right to use.
            </p>
          </div>

          <div id="ai">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">AI-Generated Content</h2>
            <p>
              Subtitles, voiceovers, translations, and blog/image prompts produced by Z.A.K.E.R.X.A
              are generated automatically and may occasionally contain errors, mistranslations, or
              inaccuracies — particularly around names, idioms, and culturally specific phrases. Review
              generated content before publishing it.
            </p>
          </div>

          <div id="warranty">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">No Warranty</h2>
            <p>
              The platform is provided "as is" and "as available," without warranties of any kind,
              whether express or implied, including but not limited to merchantability, fitness for a
              particular purpose, and non-infringement.
            </p>
          </div>

          <div id="liability">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Limitation of Liability</h2>
            <p>
              To the maximum extent permitted by law, Z.A.K.E.R.X.A and its team will not be liable
              for any indirect, incidental, or consequential damages — including lost revenue, lost
              data, or account suspension on any third-party platform — arising from your use of the
              service.
            </p>
          </div>

          <div id="thirdparty">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Third-Party Links</h2>
            <p>
              Our site may link to third-party services (Telegram, TikTok, payment processors, and
              similar). We don't control those services and aren't responsible for their content,
              policies, or practices.
            </p>
          </div>

          <div id="availability">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Service Availability</h2>
            <p>
              We aim for reliable uptime but don't guarantee the platform will be available
              uninterrupted or error-free at all times. Processing jobs may occasionally fail and
              need to be retried.
            </p>
          </div>

          <div id="changes">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Changes</h2>
            <p>
              We may update this disclaimer as the platform evolves. Continued use of Z.A.K.E.R.X.A
              after an update means you accept the revised terms.
            </p>
          </div>

          <div id="contact">
            <h2 class="text-xl font-bold text-[#F1F5F9] mb-3 scroll-mt-24">Contact Us</h2>
            <p>
              Questions about this disclaimer? Email
              <a href="mailto:admin@zakerxa.com" class="text-[#A78BFA] no-underline hover:underline">admin@zakerxa.com</a>
              or visit our <a href="/ContactUs" class="text-[#A78BFA] no-underline hover:underline">Contact page</a>.
            </p>
          </div>

        </div>
      </div>
    </section>

    <AppFooter />
  </div>
</template>

<script setup>
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';

defineProps({
  auth: Object,
});
</script>
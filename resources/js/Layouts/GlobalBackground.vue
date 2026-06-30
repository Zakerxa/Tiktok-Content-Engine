<!--
  Add this ONCE to your root layout (App.vue, or whatever <div> wraps
  the whole page: navbar + all sections + footer).

  This replaces any per-section bg-[#05070D] / grid-pattern divs —
  remove those from individual sections so there's only one
  background source for the entire site.
-->

<template>
  <div class="relative min-h-screen bg-[#05070D] text-slate-50">
    <!-- Fixed global grid pattern, sits behind every section -->
    <div class="pointer-events-none fixed inset-0 z-0">
      <div
        class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.025)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.025)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_80%_at_50%_0%,black,transparent)]"
      ></div>
    </div>

    <!-- Everything else renders above the background -->
    <div class="relative z-10">
      <!-- <NavBar />, <router-view /> or all your sections go here -->
      <slot />
    </div>
  </div>
</template>
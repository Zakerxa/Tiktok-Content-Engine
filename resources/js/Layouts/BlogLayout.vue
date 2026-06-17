<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const open = ref(false);
const page = usePage();
const topics = computed(() => page.props.topicStats ?? []);
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="border-b border-slate-800 bg-slate-900/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-3 text-white">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-fuchsia-500/90 text-lg font-bold shadow-lg shadow-fuchsia-500/20">
                        TT
                    </span>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-300">
                            TikTok Content Engine
                        </p>
                        <p class="text-xs text-slate-500">Responsive blog system</p>
                    </div>
                </Link>

                <div class="hidden items-center gap-4 lg:flex">
                    <Link href="/" class="rounded-full border border-slate-700 px-4 py-2 text-sm font-medium text-slate-200 transition hover:border-slate-500 hover:text-white">
                        Home
                    </Link>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs uppercase tracking-[0.25em] text-slate-500">Topics</span>
                        <div class="flex flex-wrap gap-2">
                            <Link
                                v-for="topic in topics.slice(0, 5)"
                                :key="topic.slug"
                                :href="route('topics.show', topic.slug)"
                                class="rounded-full bg-slate-800 px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-700"
                            >
                                {{ topic.topic }}
                            </Link>
                        </div>
                    </div>
                </div>

                <button
                    @click="open = !open"
                    type="button"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-700 bg-slate-900 text-slate-200 transition hover:border-slate-500 hover:bg-slate-800 lg:hidden"
                    aria-label="Toggle navigation"
                >
                    <svg
                        v-if="!open"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg
                        v-else
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div v-if="open" class="border-t border-slate-800 bg-slate-900/95 lg:hidden">
                <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:px-6 lg:px-8">
                    <Link href="/" class="rounded-2xl border border-slate-700 px-4 py-3 text-sm font-medium text-slate-200 transition hover:border-slate-500 hover:text-white">
                        Home
                    </Link>
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Topics</p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <Link
                                v-for="topic in topics"
                                :key="topic.slug"
                                :href="route('topics.show', topic.slug)"
                                class="rounded-2xl bg-slate-800 px-4 py-3 text-sm text-slate-200 transition hover:bg-slate-700"
                            >
                                {{ topic.topic }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="bg-slate-950 text-slate-100">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BlogLayout from '@/Layouts/BlogLayout.vue';

const props = defineProps({
    selectedTopic: String,
    topicSlug: String,
    posts: Array,
    topicStats: Array,
});

const excerpt = (value) => {
    if (!value) {
        return '';
    }

    return value.length > 110 ? `${value.slice(0, 110).trim()}...` : value;
};
</script>

<template>
    <BlogLayout>
        <Head :title="`Topic: ${props.selectedTopic}`" />

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-8">
            <div class="rounded-[2rem] bg-slate-900/90 p-8 shadow-2xl shadow-slate-950/40 ring-1 ring-white/10">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-300">Category page</p>
                        <h1 class="mt-3 text-4xl font-semibold text-white">{{ props.selectedTopic }}</h1>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">
                            Posts filtered by topic, updated dynamically from the TikTok Content Engine database.
                        </p>
                    </div>
                    <Link
                        href="/"
                        class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-950/80 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:border-slate-500 hover:bg-slate-900"
                    >
                        Back to home
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-8 shadow-xl shadow-slate-950/20">
                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-400">
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-slate-200">Topic</span>
                            <span class="text-white">{{ props.selectedTopic }}</span>
                            <span class="mx-2 hidden h-1 w-1 rounded-full bg-slate-500 sm:inline-block"></span>
                            <span>{{ props.posts.length }} posts</span>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-2">
                        <article
                            v-for="post in props.posts"
                            :key="post.id"
                            class="overflow-hidden rounded-[2rem] bg-slate-900 shadow-2xl shadow-slate-950/40"
                        >
                            <Link :href="route('posts.show', post.id)" class="block relative h-72 overflow-hidden bg-slate-800">
                                <div
                                    class="absolute inset-0 bg-cover bg-center transition duration-500 group-hover:scale-105"
                                    :style="{ backgroundImage: `url('${post.image_path}')` }"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent" />
                                <div class="absolute bottom-0 left-0 right-0 p-6">
                                    <span class="inline-flex rounded-full bg-fuchsia-500/20 px-3 py-1 text-xs uppercase tracking-[0.32em] text-fuchsia-200">
                                        {{ post.topic }}
                                    </span>
                                    <h2 class="mt-4 text-2xl font-semibold text-white">{{ post.cover_title_burmese }}</h2>
                                    <p class="mt-3 text-sm leading-6 text-slate-300">{{ excerpt(post.content) }}</p>
                                </div>
                            </Link>
                            <div class="flex items-center justify-between gap-3 border-t border-slate-800 px-6 py-5 text-sm text-slate-400">
                                <span>{{ post.model_used || 'AI model' }}</span>
                                <span>{{ new Date(post.created_at).toLocaleDateString() }}</span>
                            </div>
                        </article>
                    </div>

                    <div v-if="props.posts.length === 0" class="rounded-[2rem] border border-dashed border-slate-700 bg-slate-900/80 p-8 text-center text-slate-400">
                        No posts were found for this topic.
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-800 bg-slate-900/95 p-6 shadow-xl shadow-slate-950/20">
                        <p class="text-sm uppercase tracking-[0.3em] text-fuchsia-300">Topics snapshot</p>
                        <div class="mt-6 space-y-3">
                            <div
                                v-for="stat in props.topicStats"
                                :key="stat.slug"
                                class="flex items-center justify-between rounded-3xl bg-slate-950/80 px-4 py-3"
                            >
                                <span>{{ stat.topic }}</span>
                                <span class="font-semibold text-fuchsia-300">{{ stat.count }}</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    </BlogLayout>
</template>

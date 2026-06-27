<template>
    <Head title="Edit Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit TikTok Post</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cover Title (Burmese)</label>
                            <input v-model="form.cover_title_burmese" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Script Content</label>
                            <textarea v-model="form.content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                            <div v-if="post.image_path" class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Current Image:</p>
                                <img :src="post.image_path" class="w-32 h-20 object-cover rounded border" />
                            </div>
                            <input @input="form.image = $event.target.files[0]" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                            <Link :href="route('dashboard')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm hover:bg-gray-50 text-sm">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-indigo-700 text-sm font-semibold disabled:opacity-50">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    post: Object
});

const form = useForm({
    _method: 'POST', // 💡 File Upload Update ဖြစ်လို့ ဝင်အောင် ခံပေးရတာပါ
    title: props.post.title || '',
    slug: props.post.slug || '',
    cover_title_burmese: props.post.cover_title_burmese || '',
    content: props.post.content || '',
    topic: props.post.topic || '',
    model_used: props.post.model_used || '',
    b_roll_animation_suggestion: props.post.b_roll_animation_suggestion || '',
    hashtags: props.post.hashtags || '',
    image_prompt: props.post.image_prompt || '',
    image: null,
});

const submit = () => {
    form.post(route('posts.update', props.post.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Post updated successfully!');
        }
    });
};
</script>
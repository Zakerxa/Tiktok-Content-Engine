<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Control Panel</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">System Settings</h3>
                        <p class="text-sm text-gray-500 mt-1">Local machine JSON data management and API configurations will be available here.</p>
                    </div>
                    <button class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-700 transition">
                        Configure Settings
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <div class="flex flex-col sm:flex-row justify-between mb-6 gap-4">
                            <div class="w-full sm:w-1/2 md:w-1/3 relative">
                                <input 
                                    v-model="search" 
                                    type="text" 
                                    placeholder="Search by title..." 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div class="w-full sm:w-1/3">
                                <select 
                                    v-model="topic" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="stat in stats" :key="stat.clean" :value="stat.clean">
                                        {{ stat.clean }} ({{ stat.total }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto relative border border-gray-200 rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Image</th>
                                        <th scope="col" class="px-6 py-3">Title</th>
                                        <th scope="col" class="px-6 py-3">Topic</th>
                                        <th scope="col" class="px-6 py-3">Date</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="post in posts.data" :key="post.id" class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <img v-if="post.image_path" :src="post.image_path" class="w-16 h-12 object-cover rounded-md border" />
                                            <div v-else class="w-16 h-12 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-400">No Img</div>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ post.title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ post.topic }}</span>
                                        </td>
                                        <td class="px-6 py-4">{{ post.created_at }}</td>
                                        <td class="px-6 py-4 text-right space-x-3">
                                            
                                            <a :href="route('posts.show', post.slug || post.id)" target="_blank" class="font-medium text-green-600 hover:underline cursor-pointer">
                                                View
                                            </a>
                                            
                                            <Link :href="route('posts.edit', post.id)" class="font-medium text-blue-600 hover:underline cursor-pointer">
                                                Edit
                                            </Link>
                                            
                                            <button @click="deletePost(post.id)" class="font-medium text-red-600 hover:underline cursor-pointer">
                                                Delete
                                            </button>

                                        </td>
                                    </tr>
                                    <tr v-if="posts.data.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No posts found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <Pagination v-if="posts.links" :links="posts.links" />
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3'; // 💡 ဤနေရာတွင် Link ပါဝင်မှုကို သေချာအောင်လုပ်ထားပါသည်
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    posts: Object,
    stats: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const topic = ref(props.filters.topic || '');

watch([search, topic], debounce(function ([newSearch, newTopic]) {
    router.get(route('dashboard'), { 
        search: newSearch, 
        topic: newTopic 
    }, {
        preserveState: true,
        replace: true
    });
}, 300));

const deletePost = (id) => {
    if (confirm('Are you sure you want to delete this post and its image?')) {
        router.delete(route('posts.destroy', id), {
            preserveScroll: true,
            onSuccess: () => alert('Successfully deleted!')
        });
    }
};
</script>
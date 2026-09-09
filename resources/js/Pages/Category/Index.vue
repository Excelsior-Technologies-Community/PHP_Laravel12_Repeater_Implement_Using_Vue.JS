<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const props = defineProps({
    categories: Array,
});

const searchQuery = ref('');
const showModal = ref(false);
const isEditing = ref(false);
const editingCategory = ref(null);

const form = useForm({
    id: null,
    name: '',
    slug: '',
    description: '',
});

const filteredCategories = computed(() => {
    if (!searchQuery.value.trim()) return props.categories || [];
    const q = searchQuery.value.toLowerCase();
    return (props.categories || []).filter(c => 
        c.name.toLowerCase().includes(q) || 
        (c.description && c.description.toLowerCase().includes(q))
    );
});

const openCreateModal = () => {
    isEditing.value = false;
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (cat) => {
    isEditing.value = true;
    editingCategory.value = cat;
    form.id = cat.id;
    form.name = cat.name;
    form.slug = cat.slug;
    form.description = cat.description || '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(`/category/${form.id}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post('/category', {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCategory = (id, name) => {
    if (confirm(`Are you sure you want to delete category "${name}"?`)) {
        router.delete(`/category/${id}`);
    }
};
</script>

<template>
    <Head title="Category Master" />

    <div class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800">
        <AppHeader activeTab="categories" />
        <FlashMessage />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>📁</span> Category Master
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">Organize your product hierarchy and catalog taxonomy.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button
                        @click="openCreateModal"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Category
                    </button>
                </div>
            </div>

            <!-- Search & Info Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-80">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search categories..."
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                    />
                </div>
                <div class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                    Total: {{ filteredCategories.length }} Categories
                </div>
            </div>

            <!-- Grid Cards -->
            <div v-if="filteredCategories.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div 
                    v-for="cat in filteredCategories" 
                    :key="cat.id"
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between group"
                >
                    <div>
                        <div class="flex items-start justify-between">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-500 to-sky-400 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                📁
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ cat.products_count ?? 0 }} Products
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-4 group-hover:text-indigo-600 transition-colors">
                            {{ cat.name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                            {{ cat.description || 'No description provided.' }}
                        </p>
                    </div>

                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span class="font-mono">slug: {{ cat.slug || 'n/a' }}</span>
                        <div class="flex items-center gap-2">
                            <button
                                @click="openEditModal(cat)"
                                type="button"
                                class="px-2.5 py-1 font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteCategory(cat.id, cat.name)"
                                type="button"
                                class="px-2.5 py-1 font-medium text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl p-12 text-center border border-dashed border-slate-300">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-2xl text-slate-400 mb-3">
                    📁
                </div>
                <h3 class="text-base font-semibold text-slate-900">No categories found</h3>
                <p class="text-sm text-slate-500 mt-1">Get started by creating a new category for your products.</p>
                <div class="mt-4">
                    <button
                        @click="openCreateModal"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        Add Category
                    </button>
                </div>
            </div>
        </main>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-xl transition-all sm:w-full sm:max-w-md border border-slate-100">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ isEditing ? 'Edit Category' : 'Create New Category' }}
                        </h3>
                        <button @click="closeModal" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Category Name *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Footwear, Electronics"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                required
                            />
                            <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Slug (Optional)
                            </label>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="e.g. footwear, smart-electronics"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            />
                            <p v-if="form.errors.slug" class="text-xs text-rose-500 mt-1">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                placeholder="Brief description..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                            ></textarea>
                            <p v-if="form.errors.description" class="text-xs text-rose-500 mt-1">{{ form.errors.description }}</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button
                                @click="closeModal"
                                type="button"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-colors disabled:opacity-50 shadow-sm"
                            >
                                {{ form.processing ? 'Saving...' : (isEditing ? 'Update Category' : 'Save Category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

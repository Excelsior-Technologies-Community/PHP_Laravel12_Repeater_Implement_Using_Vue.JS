<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppHeader from '@/Components/AppHeader.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import QuickViewModal from '@/Components/QuickViewModal.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    filters: Object,
    metrics: Object,
});

const search = ref(props.filters.search || '');
const categoryId = ref(props.filters.category_id || '');
const brandId = ref(props.filters.brand_id || '');
const status = ref(props.filters.status || 'all');
const stockStatus = ref(props.filters.stock_status || 'all');
const sort = ref(props.filters.sort || 'latest');

const selectedProducts = ref([]);
const selectAll = ref(false);
const quickViewProduct = ref(null);

let debounceTimer = null;

const applyFilters = () => {
    router.get(
        '/product',
        {
            search: search.value,
            category_id: categoryId.value,
            brand_id: brandId.value,
            status: status.value,
            stock_status: stockStatus.value,
            sort: sort.value,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    );
};

watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 300);
});

watch([categoryId, brandId, status, stockStatus, sort], () => {
    applyFilters();
});

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedProducts.value = props.products.data.map(p => p.id);
    } else {
        selectedProducts.value = [];
    }
};

const deleteProduct = (id, title) => {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        router.delete(`/product/${id}`);
    }
};

const duplicateProduct = (id) => {
    router.post(`/product/${id}/duplicate`);
};

const bulkDelete = () => {
    if (confirm(`Are you sure you want to delete ${selectedProducts.value.length} selected products?`)) {
        router.post('/products/bulk-delete', {
            ids: selectedProducts.value,
        }, {
            onSuccess: () => {
                selectedProducts.value = [];
                selectAll.value = false;
            }
        });
    }
};

const openQuickView = (product) => {
    quickViewProduct.value = product;
};

const closeQuickView = () => {
    quickViewProduct.value = null;
};
</script>

<template>
    <Head title="Products Master & Repeaters" />

    <div class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800">
        <AppHeader activeTab="products" />
        <FlashMessage />

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="sm:flex sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>📦</span> Product Catalog
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Manage products with dynamic multi-repeaters (Variants, Specs, FAQs, Highlights, & Images).
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center gap-3">
                    <a
                        href="/products/export/csv"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 shadow-sm border border-slate-200 hover:bg-slate-50 transition-all duration-200"
                    >
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </a>
                    <Link
                        href="/product/create"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </Link>
                </div>
            </div>

            <!-- Metrics Overview Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                        📦
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Products</div>
                        <div class="text-xl font-bold text-slate-900">{{ metrics?.total ?? 0 }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                        🟢
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Active</div>
                        <div class="text-xl font-bold text-emerald-600">{{ metrics?.active ?? 0 }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                        ⚠️
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Low Stock</div>
                        <div class="text-xl font-bold text-amber-600">{{ metrics?.low_stock ?? 0 }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg">
                        ⚪
                    </div>
                    <div>
                        <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Inactive</div>
                        <div class="text-xl font-bold text-slate-600">{{ metrics?.inactive ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 mb-6 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                    <!-- Search Input -->
                    <div class="md:col-span-2 relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search title, SKU, description..."
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select
                            v-model="categoryId"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="">All Categories</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div>
                        <select
                            v-model="brandId"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="">All Brands</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            v-model="status"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="all">Status: All</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <select
                            v-model="sort"
                            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-sm text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                        >
                            <option value="latest">Sort: Newest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="title_asc">Title: A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Bulk Selection Banner -->
                <div v-if="selectedProducts.length > 0" class="flex items-center justify-between bg-indigo-50/80 border border-indigo-100 rounded-xl px-4 py-2 text-sm text-indigo-900">
                    <span class="font-medium">
                        {{ selectedProducts.length }} product(s) selected
                    </span>
                    <button
                        @click="bulkDelete"
                        type="button"
                        class="px-3 py-1 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-500 transition-colors shadow-sm"
                    >
                        Delete Selected
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 divide-y divide-slate-100">
                        <thead class="bg-slate-50/80 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th scope="col" class="p-4 w-4">
                                    <input
                                        v-model="selectAll"
                                        @change="toggleSelectAll"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4"
                                    />
                                </th>
                                <th scope="col" class="px-4 py-3.5">Product</th>
                                <th scope="col" class="px-4 py-3.5">Category / Brand</th>
                                <th scope="col" class="px-4 py-3.5">Price & Stock</th>
                                <th scope="col" class="px-4 py-3.5">Repeater Badges</th>
                                <th scope="col" class="px-4 py-3.5">Status</th>
                                <th scope="col" class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="product in products?.data || []"
                                :key="product.id"
                                class="hover:bg-slate-50/70 transition-colors group"
                            >
                                <td class="p-4 w-4">
                                    <input
                                        v-model="selectedProducts"
                                        :value="product.id"
                                        type="checkbox"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 h-4 w-4"
                                    />
                                </td>

                                <!-- Product Info -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                            <img
                                                v-if="product.images && product.images.length > 0"
                                                :src="'/storage/' + product.images[0].image_path"
                                                :alt="product.title"
                                                class="w-full h-full object-cover"
                                            />
                                            <span v-else class="text-xl">📦</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                                {{ product.title }}
                                            </div>
                                            <div class="text-xs font-mono text-slate-400">
                                                SKU: {{ product.sku || 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Brand -->
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span v-if="product.category" class="inline-flex items-center text-xs font-medium text-slate-700">
                                            📁 {{ product.category.name }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400">No Category</span>
                                        <span v-if="product.brand" class="inline-flex items-center text-xs text-slate-500">
                                            🏷️ {{ product.brand.name }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Price & Stock -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900">${{ Number(product.price).toFixed(2) }}</div>
                                    <div class="text-xs mt-0.5">
                                        <span
                                            v-if="product.stock_quantity <= 0"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-rose-50 text-rose-700"
                                        >
                                            Out of stock
                                        </span>
                                        <span
                                            v-else-if="product.stock_quantity <= (product.low_stock_threshold || 5)"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700"
                                        >
                                            Low: {{ product.stock_quantity }} left
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700"
                                        >
                                            In stock: {{ product.stock_quantity }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Repeaters Badges -->
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5 max-w-xs">
                                        <span
                                            v-if="product.images_count > 0"
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100"
                                            title="Product Images"
                                        >
                                            🖼️ {{ product.images_count }}
                                        </span>
                                        <span
                                            v-if="product.variants_count > 0"
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100"
                                            title="Variants"
                                        >
                                            🔀 {{ product.variants_count }}
                                        </span>
                                        <span
                                            v-if="product.specifications_count > 0"
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"
                                            title="Specifications"
                                        >
                                            📋 {{ product.specifications_count }}
                                        </span>
                                        <span
                                            v-if="product.faqs_count > 0"
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100"
                                            title="FAQs"
                                        >
                                            ❓ {{ product.faqs_count }}
                                        </span>
                                        <span
                                            v-if="product.highlights_count > 0"
                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-100"
                                            title="Highlights"
                                        >
                                            ✨ {{ product.highlights_count }}
                                        </span>
                                        <span v-if="!product.variants_count && !product.specifications_count && !product.faqs_count && !product.highlights_count && !product.images_count" class="text-xs text-slate-400">
                                            No repeaters
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4">
                                    <span
                                        v-if="product.status === 'active'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Quick View -->
                                        <button
                                            @click="openQuickView(product)"
                                            type="button"
                                            title="Quick View"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Duplicate -->
                                        <button
                                            @click="duplicateProduct(product.id)"
                                            type="button"
                                            title="Duplicate Product"
                                            class="p-1.5 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                            </svg>
                                        </button>

                                        <!-- Edit -->
                                        <Link
                                            :href="'/product/' + product.id + '/edit'"
                                            title="Edit"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </Link>

                                        <!-- Delete -->
                                        <button
                                            @click="deleteProduct(product.id, product.title)"
                                            type="button"
                                            title="Delete"
                                            class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="!products?.data || products.data.length === 0">
                                <td colspan="7" class="p-12 text-center">
                                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-2xl text-slate-400 mb-3">
                                        📦
                                    </div>
                                    <h3 class="text-base font-semibold text-slate-900">No products found</h3>
                                    <p class="text-sm text-slate-500 mt-1">Try changing filters or add your first product.</p>
                                    <div class="mt-4">
                                        <Link
                                            href="/product/create"
                                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                        >
                                            Add New Product
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products?.links && products.links.length > 3" class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Showing <span class="font-semibold text-slate-800">{{ products.from ?? 0 }}</span> to <span class="font-semibold text-slate-800">{{ products.to ?? 0 }}</span> of <span class="font-semibold text-slate-800">{{ products.total }}</span> items
                    </div>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, key) in products.links" :key="key">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors',
                                    link.active
                                        ? 'bg-indigo-600 text-white shadow-sm'
                                        : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50'
                                ]"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 border border-slate-100 bg-slate-50 cursor-not-allowed"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- Quick View Modal Component -->
        <QuickViewModal
            v-if="quickViewProduct"
            :product="quickViewProduct"
            @close="closeQuickView"
        />
    </div>
</template>

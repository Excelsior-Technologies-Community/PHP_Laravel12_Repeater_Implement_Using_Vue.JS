<script setup>
import { computed } from 'vue'
import AppHeader from '@/Components/AppHeader.vue'

const props = defineProps({
    statistics: {
        type: Object,
        required: true,
    },
    categoryStats: {
        type: Array,
        default: () => [],
    },
    brandStats: {
        type: Array,
        default: () => [],
    },
    recentProducts: {
        type: Array,
        default: () => [],
    },
    lowStockProducts: {
        type: Array,
        default: () => [],
    },
})

const inventoryValue = computed(() => {
    return Number(
        props.statistics.total_inventory_value ?? 0
    ).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
})
</script>

<template>
    <div class="min-h-screen bg-slate-50 pb-16">
        <AppHeader activeTab="dashboard" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Dashboard Title Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span>📊 Product & Repeater Master Analytics</span>
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Comprehensive overview of products, 5 repeaters (Images, Variants, Specs, FAQs, Highlights), and inventory health.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="/product"
                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-md transition"
                    >
                        📦 Product Listing
                    </a>

                    <a
                        href="/product/create"
                        class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-xs transition"
                    >
                        + Create Product
                    </a>
                </div>
            </div>

            <!-- Top Metric Cards (5 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <!-- 1. Total Products -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                        📦
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Products</p>
                        <p class="text-2xl font-black text-slate-900">{{ statistics.total_products }}</p>
                    </div>
                </div>

                <!-- 2. Total Variants -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold">
                        🔀
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Variants</p>
                        <p class="text-2xl font-black text-slate-900">{{ statistics.total_variants }}</p>
                        <p class="text-[10px] text-purple-600 font-semibold mt-0.5">Avg {{ statistics.avg_variants_per_product }}/item</p>
                    </div>
                </div>

                <!-- 3. Total Images -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold">
                        🖼️
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Images</p>
                        <p class="text-2xl font-black text-slate-900">{{ statistics.total_images }}</p>
                        <p class="text-[10px] text-blue-600 font-semibold mt-0.5">Avg {{ statistics.avg_images_per_product }}/item</p>
                    </div>
                </div>

                <!-- 4. Total Units -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                        📊
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Units</p>
                        <p class="text-2xl font-black text-slate-900">{{ statistics.total_stock }}</p>
                    </div>
                </div>

                <!-- 5. Inventory Value -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold">
                        💰
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory Value</p>
                        <p class="text-xl font-black text-slate-900">₹{{ inventoryValue }}</p>
                    </div>
                </div>
            </div>

            <!-- Inventory Health Alerts Bar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="p-5 rounded-3xl border border-emerald-200 bg-emerald-50/60 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">In Stock Products</p>
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="text-3xl font-black text-emerald-900 mt-2">{{ statistics.in_stock }}</p>
                    <p class="text-xs text-emerald-700 mt-1">Sufficient inventory</p>
                </div>

                <div class="p-5 rounded-3xl border border-amber-200 bg-amber-50/60 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Low Stock Warning</p>
                        <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                    </div>
                    <p class="text-3xl font-black text-amber-900 mt-2">{{ statistics.low_stock }}</p>
                    <p class="text-xs text-amber-700 mt-1">Below alert threshold</p>
                </div>

                <div class="p-5 rounded-3xl border border-rose-200 bg-rose-50/60 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-800">Out of Stock</p>
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    </div>
                    <p class="text-3xl font-black text-rose-900 mt-2">{{ statistics.out_of_stock }}</p>
                    <p class="text-xs text-rose-700 mt-1">Requires immediate restocking</p>
                </div>
            </div>

            <!-- Distribution Charts (Categories & Brands) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Category Distribution -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span>🏷️</span>
                            <span>Products by Category ({{ statistics.total_categories }} categories)</span>
                        </h2>
                        <a href="/category" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Manage →</a>
                    </div>

                    <div v-if="categoryStats.length === 0" class="py-8 text-center text-xs text-slate-400">
                        No categories found.
                    </div>
                    <div v-else class="space-y-3.5 max-h-64 overflow-y-auto pr-1">
                        <div v-for="c in categoryStats" :key="c.id" class="group">
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">{{ c.name }}</span>
                                <span class="text-slate-900 font-bold">{{ c.products_count }} products</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div
                                    class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                    :style="{ width: Math.min(100, (c.products_count / Math.max(...categoryStats.map(i => i.products_count), 1)) * 100) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brand Distribution -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <span>🏢</span>
                            <span>Products by Brand ({{ statistics.total_brands }} brands)</span>
                        </h2>
                        <a href="/brand" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Manage →</a>
                    </div>

                    <div v-if="brandStats.length === 0" class="py-8 text-center text-xs text-slate-400">
                        No brands found.
                    </div>
                    <div v-else class="space-y-3.5 max-h-64 overflow-y-auto pr-1">
                        <div v-for="b in brandStats" :key="b.id" class="group">
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">{{ b.name }}</span>
                                <span class="text-slate-900 font-bold">{{ b.products_count }} products</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div
                                    class="bg-purple-600 h-2 rounded-full transition-all duration-500"
                                    :style="{ width: Math.min(100, (b.products_count / Math.max(...brandStats.map(i => i.products_count), 1)) * 100) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Products with Repeater Badges -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span>✨</span>
                        <span>Recent Products & Repeater Health</span>
                    </h2>
                    <a href="/product" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">View All Products →</a>
                </div>

                <div v-if="recentProducts.length === 0" class="py-12 text-center text-xs text-slate-400">
                    No products added yet.
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="py-3 px-4">Product</th>
                                <th class="py-3 px-4">Category / Brand</th>
                                <th class="py-3 px-4">Price</th>
                                <th class="py-3 px-4">Repeaters Breakdown</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="p in recentProducts" :key="p.id" class="hover:bg-slate-50/60 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                            <img
                                                v-if="p.primary_image || (p.images && p.images.length > 0)"
                                                :src="'/products/' + (p.primary_image ? p.primary_image.image : p.images[0].image)"
                                                class="w-full h-full object-cover"
                                            />
                                            <span v-else class="text-base">📦</span>
                                        </div>
                                        <span class="font-bold text-slate-900">{{ p.name }}</span>
                                    </div>
                                </td>

                                <td class="py-3 px-4">
                                    <span v-if="p.category" class="font-semibold text-indigo-600 mr-2">{{ p.category.name }}</span>
                                    <span v-if="p.brand" class="text-slate-500">{{ p.brand.name }}</span>
                                </td>

                                <td class="py-3 px-4 font-black text-slate-900">
                                    ₹{{ Number(p.price).toFixed(2) }}
                                </td>

                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold">
                                            🖼️ {{ p.images_count }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold">
                                            🔀 {{ p.variants_count }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold">
                                            📋 {{ p.specifications_count }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                            ❓ {{ p.faqs_count }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-right">
                                    <a
                                        :href="'/product/' + p.id + '/edit'"
                                        class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition"
                                    >
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

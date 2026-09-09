<script setup>
import { Link } from '@inertiajs/vue3'
import FlashMessage from '@/Components/FlashMessage.vue'

defineProps({
    activeTab: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <FlashMessage />
    <header class="bg-white border-b border-slate-200 shadow-xs sticky top-0 z-30 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <Link href="/product-dashboard" class="flex items-center gap-2.5 text-xl font-black text-indigo-600 hover:text-indigo-700 transition">
                        <span class="text-2xl p-1.5 bg-indigo-50 rounded-xl border border-indigo-100">🔁</span>
                        <span class="tracking-tight text-slate-900">Repeater<span class="text-indigo-600">Master</span></span>
                    </Link>
                </div>

                <!-- Center Navigation Tabs -->
                <nav class="flex items-center gap-1 sm:gap-2">
                    <Link
                        href="/product-dashboard"
                        :class="[
                            'flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition',
                            activeTab === 'dashboard'
                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                        ]"
                    >
                        <span>📊</span>
                        <span class="hidden sm:inline">Dashboard</span>
                    </Link>

                    <Link
                        href="/product"
                        :class="[
                            'flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition',
                            activeTab === 'products'
                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                        ]"
                    >
                        <span>📦</span>
                        <span>Products</span>
                    </Link>

                    <Link
                        href="/category"
                        :class="[
                            'flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition',
                            activeTab === 'categories'
                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                        ]"
                    >
                        <span>🏷️</span>
                        <span>Categories</span>
                    </Link>

                    <Link
                        href="/brand"
                        :class="[
                            'flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl text-sm font-semibold transition',
                            activeTab === 'brands'
                                ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
                        ]"
                    >
                        <span>🏢</span>
                        <span>Brands</span>
                    </Link>
                </nav>

                <!-- Quick Action Button -->
                <div class="flex items-center gap-2">
                    <Link
                        href="/product/create"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-sm hover:shadow-md transition active:scale-95"
                    >
                        <span class="text-base font-black">+</span>
                        <span class="hidden sm:inline">New Product</span>
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    show: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close'])

const selectedImageIndex = ref(0)
const activeTab = ref('variants') // 'variants', 'specs', 'highlights', 'faqs'

watch(
    () => props.product,
    () => {
        selectedImageIndex.value = 0
        activeTab.value = 'variants'
    }
)

const currentImage = computed(() => {
    if (!props.product || !props.product.images || props.product.images.length === 0) {
        return null
    }
    return props.product.images[selectedImageIndex.value] || props.product.images[0]
})

const close = () => {
    emit('close')
}
</script>

<template>
    <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="show && product"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
            @click.self="close"
        >
            <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-slate-200">
                <!-- Modal Top Bar -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">👁️</span>
                        <h2 class="text-lg font-black text-slate-900">Product Quick View</h2>
                        <span class="text-xs font-mono font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md">
                            #{{ product.id }}
                        </span>
                    </div>

                    <button
                        @click="close"
                        type="button"
                        class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition"
                    >
                        ✕
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto flex-1 space-y-6">
                    <!-- Top section: Gallery + Main Specs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left: Image Gallery -->
                        <div>
                            <div class="aspect-4/3 rounded-2xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center relative">
                                <img
                                    v-if="currentImage"
                                    :src="'/products/' + currentImage.image"
                                    :alt="product.name"
                                    class="w-full h-full object-contain p-2"
                                />
                                <div v-else class="text-slate-400 text-center p-4">
                                    <span class="text-4xl block mb-2">🖼️</span>
                                    <span class="text-xs font-semibold">No Image Available</span>
                                </div>

                                <span
                                    v-if="currentImage && currentImage.is_primary"
                                    class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs"
                                >
                                    ★ Primary Image
                                </span>
                            </div>

                            <!-- Thumbnails -->
                            <div
                                v-if="product.images && product.images.length > 1"
                                class="flex items-center gap-2 mt-3 overflow-x-auto pb-2"
                            >
                                <button
                                    v-for="(img, idx) in product.images"
                                    :key="img.id"
                                    type="button"
                                    @click="selectedImageIndex = idx"
                                    class="w-14 h-14 rounded-xl border-2 overflow-hidden flex-shrink-0 p-0.5 transition"
                                    :class="selectedImageIndex === idx ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-slate-200 hover:border-slate-300'"
                                >
                                    <img :src="'/products/' + img.image" class="w-full h-full object-cover rounded-lg" />
                                </button>
                            </div>
                        </div>

                        <!-- Right: Info -->
                        <div class="flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        v-if="product.category"
                                        class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200"
                                    >
                                        🏷️ {{ product.category.name }}
                                    </span>
                                    <span
                                        v-if="product.brand"
                                        class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200"
                                    >
                                        🏢 {{ product.brand.name }}
                                    </span>
                                    <span class="text-xs font-mono text-slate-400">
                                        {{ product.sku }}
                                    </span>
                                </div>

                                <h3 class="text-2xl font-black text-slate-900 tracking-tight mt-2">
                                    {{ product.name }}
                                </h3>

                                <div class="mt-3 flex items-baseline gap-3">
                                    <span class="text-3xl font-black text-slate-900">
                                        ₹{{ Number(product.price).toFixed(2) }}
                                    </span>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold"
                                        :class="[
                                            product.stock_quantity > product.low_stock_threshold
                                                ? 'bg-emerald-100 text-emerald-800'
                                                : product.stock_quantity > 0
                                                    ? 'bg-amber-100 text-amber-800'
                                                    : 'bg-rose-100 text-rose-800'
                                        ]"
                                    >
                                        {{ product.stock_quantity }} in stock ({{ product.stock_status }})
                                    </span>
                                </div>

                                <p class="text-sm text-slate-600 mt-4 leading-relaxed line-clamp-4">
                                    {{ product.details }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 mt-4 flex items-center gap-3">
                                <a
                                    :href="'/product/' + product.id + '/edit'"
                                    class="flex-1 text-center py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition"
                                >
                                    ✏️ Edit Product & Repeaters
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom: Repeaters Tabs (Variants, Specs, Highlights, FAQs) -->
                    <div class="border-t border-slate-200 pt-5">
                        <!-- Navigation Tabs for Repeaters -->
                        <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto">
                            <button
                                type="button"
                                @click="activeTab = 'variants'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5"
                                :class="activeTab === 'variants' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                            >
                                <span>🔀 Variants</span>
                                <span class="bg-black/20 text-white px-1.5 py-0.2 rounded-full text-[10px]">
                                    {{ product.variants?.length ?? 0 }}
                                </span>
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'specs'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5"
                                :class="activeTab === 'specs' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                            >
                                <span>📋 Specifications</span>
                                <span class="bg-black/20 text-white px-1.5 py-0.2 rounded-full text-[10px]">
                                    {{ product.specifications?.length ?? 0 }}
                                </span>
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'highlights'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5"
                                :class="activeTab === 'highlights' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                            >
                                <span>✨ Highlights</span>
                                <span class="bg-black/20 text-white px-1.5 py-0.2 rounded-full text-[10px]">
                                    {{ product.highlights?.length ?? 0 }}
                                </span>
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'faqs'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5"
                                :class="activeTab === 'faqs' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                            >
                                <span>❓ FAQs</span>
                                <span class="bg-black/20 text-white px-1.5 py-0.2 rounded-full text-[10px]">
                                    {{ product.faqs?.length ?? 0 }}
                                </span>
                            </button>
                        </div>

                        <!-- Tab 1: Variants Repeater List -->
                        <div v-if="activeTab === 'variants'" class="pt-4">
                            <div v-if="!product.variants || product.variants.length === 0" class="text-center py-6 text-xs text-slate-400">
                                No variants added for this product.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold">
                                        <tr>
                                            <th class="py-2.5 px-3">Size</th>
                                            <th class="py-2.5 px-3">Color</th>
                                            <th class="py-2.5 px-3">SKU</th>
                                            <th class="py-2.5 px-3">Price</th>
                                            <th class="py-2.5 px-3 text-right">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="v in product.variants" :key="v.id" class="hover:bg-slate-50">
                                            <td class="py-2.5 px-3 font-bold text-slate-900">{{ v.size || 'Standard' }}</td>
                                            <td class="py-2.5 px-3 text-slate-600">{{ v.color || 'Standard' }}</td>
                                            <td class="py-2.5 px-3 font-mono text-slate-500">{{ v.sku }}</td>
                                            <td class="py-2.5 px-3 font-semibold text-slate-900">₹{{ Number(v.price).toFixed(2) }}</td>
                                            <td class="py-2.5 px-3 text-right font-bold text-emerald-600">{{ v.stock_quantity }} units</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab 2: Specifications Repeater -->
                        <div v-if="activeTab === 'specs'" class="pt-4">
                            <div v-if="!product.specifications || product.specifications.length === 0" class="text-center py-6 text-xs text-slate-400">
                                No specifications added.
                            </div>
                            <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div
                                    v-for="s in product.specifications"
                                    :key="s.id"
                                    class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between text-xs"
                                >
                                    <span class="font-bold text-slate-600">{{ s.spec_key }}</span>
                                    <span class="font-medium text-slate-900">{{ s.spec_value }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Highlights Repeater -->
                        <div v-if="activeTab === 'highlights'" class="pt-4">
                            <div v-if="!product.highlights || product.highlights.length === 0" class="text-center py-6 text-xs text-slate-400">
                                No key highlights added.
                            </div>
                            <ul v-else class="space-y-2">
                                <li
                                    v-for="h in product.highlights"
                                    :key="h.id"
                                    class="flex items-start gap-2.5 text-xs text-slate-700"
                                >
                                    <span class="text-indigo-600 font-bold text-sm leading-none">•</span>
                                    <span>{{ h.highlight_text }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab 4: FAQs Repeater -->
                        <div v-if="activeTab === 'faqs'" class="pt-4">
                            <div v-if="!product.faqs || product.faqs.length === 0" class="text-center py-6 text-xs text-slate-400">
                                No FAQs added for this product.
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="f in product.faqs"
                                    :key="f.id"
                                    class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80"
                                >
                                    <p class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                        <span class="text-indigo-600">Q:</span>
                                        <span>{{ f.question }}</span>
                                    </p>
                                    <p class="text-xs text-slate-600 mt-1 pl-4">
                                        {{ f.answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

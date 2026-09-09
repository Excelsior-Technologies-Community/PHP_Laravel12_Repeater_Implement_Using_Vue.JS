<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    brands: {
        type: Array,
        default: () => [],
    },
})

// Active Section Tab
const currentTab = ref('basic') // 'basic', 'images', 'variants', 'specs', 'highlights', 'faqs'

// Main Form
const form = useForm({
    _method: 'PUT',
    name: props.product.name,
    sku: props.product.sku,
    details: props.product.details,
    price: props.product.price,
    category_id: props.product.category_id ?? '',
    brand_id: props.product.brand_id ?? '',
    stock_quantity: props.product.stock_quantity ?? 0,
    low_stock_threshold: props.product.low_stock_threshold ?? 5,

    // 1. New Images
    images: [],

    // 2. Variants Repeater
    variants: (props.product.variants && props.product.variants.length > 0)
        ? props.product.variants.map(v => ({
            size: v.size ?? '',
            color: v.color ?? '',
            sku: v.sku ?? '',
            price: v.price ?? props.product.price,
            stock_quantity: v.stock_quantity ?? 0,
        }))
        : [{ size: '', color: '', sku: '', price: props.product.price, stock_quantity: 0 }],

    // 3. Specifications Repeater
    specifications: (props.product.specifications && props.product.specifications.length > 0)
        ? props.product.specifications.map(s => ({
            spec_key: s.spec_key ?? '',
            spec_value: s.spec_value ?? '',
        }))
        : [{ spec_key: '', spec_value: '' }],

    // 4. FAQs Repeater
    faqs: (props.product.faqs && props.product.faqs.length > 0)
        ? props.product.faqs.map(f => ({
            question: f.question ?? '',
            answer: f.answer ?? '',
        }))
        : [{ question: '', answer: '' }],

    // 5. Highlights Repeater
    highlights: (props.product.highlights && props.product.highlights.length > 0)
        ? props.product.highlights.map(h => ({
            highlight_text: h.highlight_text ?? '',
        }))
        : [{ highlight_text: '' }],
})

// New image previews
const imagePreviews = ref([])

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files)
    form.images = [...form.images, ...files]

    files.forEach(file => {
        const reader = new FileReader()
        reader.onload = (event) => {
            imagePreviews.value.push({
                url: event.target.result,
                file: file,
            })
        }
        reader.readAsDataURL(file)
    })
}

const removeNewImage = (index) => {
    form.images.splice(index, 1)
    imagePreviews.value.splice(index, 1)
}

// Existing Image Operations
const setPrimaryImage = (imageId) => {
    router.post(`/product/${props.product.id}/set-primary/${imageId}`, {}, {
        preserveScroll: true,
    })
}

const deleteExistingImage = (imageId) => {
    if (confirm('Delete this image from server?')) {
        router.delete(`/product/${props.product.id}/image/${imageId}`, {
            preserveScroll: true,
        })
    }
}

// Variants Repeater methods
const addVariant = () => {
    form.variants.push({ size: '', color: '', sku: '', price: form.price || '', stock_quantity: 0 })
}
const removeVariant = (index) => {
    form.variants.splice(index, 1)
}

// Specs Repeater methods
const addSpec = () => {
    form.specifications.push({ spec_key: '', spec_value: '' })
}
const removeSpec = (index) => {
    form.specifications.splice(index, 1)
}

// FAQs Repeater methods
const addFaq = () => {
    form.faqs.push({ question: '', answer: '' })
}
const removeFaq = (index) => {
    form.faqs.splice(index, 1)
}

// Highlights Repeater methods
const addHighlight = () => {
    form.highlights.push({ highlight_text: '' })
}
const removeHighlight = (index) => {
    form.highlights.splice(index, 1)
}

// Submit Update
const submit = () => {
    form.post(`/product/${props.product.id}`, {
        forceFormData: true,
    })
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 pb-20">
        <AppHeader activeTab="products" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                        <span>✏️ Edit Product #{{ product.id }}</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Manage basic information and 5 child repeaters (Images, Variants, Specs, FAQs, Highlights).
                    </p>
                </div>

                <a
                    href="/product"
                    class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition"
                >
                    ← Back to Products
                </a>
            </div>

            <!-- Tab Buttons -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6">
                <button
                    type="button"
                    @click="currentTab = 'basic'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'basic' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>📌 Basic Info</span>
                </button>

                <button
                    type="button"
                    @click="currentTab = 'images'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'images' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>🖼️ Images ({{ (product.images?.length ?? 0) + form.images.length }})</span>
                </button>

                <button
                    type="button"
                    @click="currentTab = 'variants'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'variants' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>🔀 Variants ({{ form.variants.length }})</span>
                </button>

                <button
                    type="button"
                    @click="currentTab = 'specs'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'specs' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>📋 Specs ({{ form.specifications.length }})</span>
                </button>

                <button
                    type="button"
                    @click="currentTab = 'highlights'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'highlights' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>✨ Highlights ({{ form.highlights.length }})</span>
                </button>

                <button
                    type="button"
                    @click="currentTab = 'faqs'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap"
                    :class="currentTab === 'faqs' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                >
                    <span>❓ FAQs ({{ form.faqs.length }})</span>
                </button>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8 space-y-6">

                <!-- 1. TAB: Basic Info -->
                <div v-show="currentTab === 'basic'" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Product Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                :class="form.errors.name ? 'border-rose-400' : 'border-slate-200'"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600 font-semibold">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                SKU / Code
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition font-mono"
                            />
                        </div>
                    </div>

                    <!-- Category, Brand, Price, Stock -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Category
                            </label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            >
                                <option value="">Select Category</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Brand
                            </label>
                            <select
                                v-model="form.brand_id"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            >
                                <option value="">Select Brand</option>
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Price (₹) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                :class="form.errors.price ? 'border-rose-400' : 'border-slate-200'"
                            />
                            <p v-if="form.errors.price" class="mt-1 text-xs text-rose-600 font-semibold">{{ form.errors.price }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Stock Units
                            </label>
                            <input
                                v-model="form.stock_quantity"
                                type="number"
                                min="0"
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            />
                        </div>
                    </div>

                    <!-- Details -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Product Description <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="form.details"
                            rows="4"
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            :class="form.errors.details ? 'border-rose-400' : 'border-slate-200'"
                        ></textarea>
                        <p v-if="form.errors.details" class="mt-1 text-xs text-rose-600 font-semibold">{{ form.errors.details }}</p>
                    </div>
                </div>

                <!-- 2. TAB: Images Repeater (Existing + Upload New) -->
                <div v-show="currentTab === 'images'" class="space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>🖼️</span>
                                <span>Product Images Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Manage existing images and upload additional photos.</p>
                        </div>
                    </div>

                    <!-- Existing Server Images -->
                    <div v-if="product.images && product.images.length > 0">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            Existing Uploaded Images ({{ product.images.length }})
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div
                                v-for="img in product.images"
                                :key="img.id"
                                class="relative rounded-2xl border-2 overflow-hidden bg-slate-100 aspect-square group transition"
                                :class="img.is_primary ? 'border-amber-500 ring-2 ring-amber-200' : 'border-slate-200'"
                            >
                                <img :src="'/products/' + img.image" class="w-full h-full object-cover" />

                                <!-- Primary Badge -->
                                <span
                                    v-if="img.is_primary"
                                    class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs"
                                >
                                    Primary
                                </span>

                                <!-- Actions Overlay -->
                                <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 p-2">
                                    <button
                                        v-if="!img.is_primary"
                                        type="button"
                                        @click="setPrimaryImage(img.id)"
                                        class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] rounded-lg transition"
                                    >
                                        Set Primary
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteExistingImage(img.id)"
                                        class="p-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg transition"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Additional Input Box -->
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-indigo-500 bg-slate-50/50 transition">
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            @change="handleImageUpload"
                            class="hidden"
                            id="imageUploadEditInput"
                        />
                        <label for="imageUploadEditInput" class="cursor-pointer block">
                            <span class="text-3xl block mb-2">📸</span>
                            <span class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Click to upload additional product images
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-1">PNG, JPG, WEBP up to 5MB each</span>
                        </label>
                    </div>

                    <!-- New Upload Previews Grid -->
                    <div v-if="imagePreviews.length > 0" class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                            New Images Pending Save ({{ imagePreviews.length }})
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div
                                v-for="(img, idx) in imagePreviews"
                                :key="idx"
                                class="relative rounded-2xl border border-indigo-300 overflow-hidden bg-slate-100 aspect-square group"
                            >
                                <img :src="img.url" class="w-full h-full object-cover" />
                                <button
                                    type="button"
                                    @click="removeNewImage(idx)"
                                    class="absolute top-2 right-2 w-6 h-6 bg-rose-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md hover:bg-rose-700 transition"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. TAB: Variants Repeater -->
                <div v-show="currentTab === 'variants'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>🔀</span>
                                <span>Product Variants Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Configure size, color, SKU, price, and inventory per variation.</p>
                        </div>

                        <button
                            type="button"
                            @click="addVariant"
                            class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-xl text-xs font-bold transition flex items-center gap-1"
                        >
                            <span>+ Add Variant Row</span>
                        </button>
                    </div>

                    <div v-if="form.variants.length === 0" class="text-center py-8 text-xs text-slate-400 bg-slate-50 rounded-2xl">
                        No variants added. Click "+ Add Variant Row" to create variations.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="(v, idx) in form.variants"
                            :key="idx"
                            class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row items-center gap-3"
                        >
                            <span class="text-xs font-mono font-bold text-slate-400 self-start sm:self-center">#{{ idx + 1 }}</span>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 flex-1 w-full">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Size</label>
                                    <input
                                        v-model="v.size"
                                        type="text"
                                        placeholder="e.g. M, XL"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Color</label>
                                    <input
                                        v-model="v.color"
                                        type="text"
                                        placeholder="e.g. Black"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">SKU</label>
                                    <input
                                        v-model="v.sku"
                                        type="text"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 font-mono"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Price (₹)</label>
                                    <input
                                        v-model="v.price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stock</label>
                                    <input
                                        v-model="v.stock_quantity"
                                        type="number"
                                        min="0"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeVariant(idx)"
                                class="p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. TAB: Specifications Repeater -->
                <div v-show="currentTab === 'specs'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>📋</span>
                                <span>Custom Specifications Key-Value Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Add technical details, material, warranty, dimensions, weight.</p>
                        </div>

                        <button
                            type="button"
                            @click="addSpec"
                            class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-xl text-xs font-bold transition flex items-center gap-1"
                        >
                            <span>+ Add Specification</span>
                        </button>
                    </div>

                    <div v-if="form.specifications.length === 0" class="text-center py-8 text-xs text-slate-400 bg-slate-50 rounded-2xl">
                        No specifications added. Click "+ Add Specification" to create key-value pairs.
                    </div>

                    <div v-else class="space-y-2.5">
                        <div
                            v-for="(s, idx) in form.specifications"
                            :key="idx"
                            class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-3"
                        >
                            <input
                                v-model="s.spec_key"
                                type="text"
                                placeholder="Specification Key"
                                class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                            <input
                                v-model="s.spec_value"
                                type="text"
                                placeholder="Specification Value"
                                class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                            <button
                                type="button"
                                @click="removeSpec(idx)"
                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 5. TAB: Highlights Repeater -->
                <div v-show="currentTab === 'highlights'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>✨</span>
                                <span>Highlights / Feature Bullet Points Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Key bullet points highlighting benefits and top features.</p>
                        </div>

                        <button
                            type="button"
                            @click="addHighlight"
                            class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-xl text-xs font-bold transition flex items-center gap-1"
                        >
                            <span>+ Add Bullet Point</span>
                        </button>
                    </div>

                    <div v-if="form.highlights.length === 0" class="text-center py-8 text-xs text-slate-400 bg-slate-50 rounded-2xl">
                        No highlights added.
                    </div>

                    <div v-else class="space-y-2.5">
                        <div
                            v-for="(h, idx) in form.highlights"
                            :key="idx"
                            class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-2"
                        >
                            <span class="text-indigo-600 font-bold">•</span>
                            <input
                                v-model="h.highlight_text"
                                type="text"
                                placeholder="Highlight bullet text..."
                                class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                            <button
                                type="button"
                                @click="removeHighlight(idx)"
                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition"
                            >
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 6. TAB: FAQs Repeater -->
                <div v-show="currentTab === 'faqs'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>❓</span>
                                <span>Product FAQs Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Frequently asked questions and answers.</p>
                        </div>

                        <button
                            type="button"
                            @click="addFaq"
                            class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-xl text-xs font-bold transition flex items-center gap-1"
                        >
                            <span>+ Add FAQ Row</span>
                        </button>
                    </div>

                    <div v-if="form.faqs.length === 0" class="text-center py-8 text-xs text-slate-400 bg-slate-50 rounded-2xl">
                        No FAQs added.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="(f, idx) in form.faqs"
                            :key="idx"
                            class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5 relative"
                        >
                            <button
                                type="button"
                                @click="removeFaq(idx)"
                                class="absolute top-3 right-3 text-rose-600 hover:text-rose-800 p-1"
                            >
                                ✕
                            </button>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Question</label>
                                <input
                                    v-model="f.question"
                                    type="text"
                                    class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Answer</label>
                                <textarea
                                    v-model="f.answer"
                                    rows="2"
                                    class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-200">
                    <a
                        href="/product"
                        class="px-5 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl shadow-md hover:shadow-lg transition disabled:opacity-50"
                    >
                        <span v-if="form.processing" class="animate-spin inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

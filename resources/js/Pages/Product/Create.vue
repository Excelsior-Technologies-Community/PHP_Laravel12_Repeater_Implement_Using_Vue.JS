<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppHeader from '@/Components/AppHeader.vue'

const props = defineProps({
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
    name: '',
    sku: '',
    details: '',
    price: '',
    category_id: '',
    brand_id: '',
    stock_quantity: 0,
    low_stock_threshold: 5,

    // 1. Images Repeater
    images: [],

    // 2. Variants Repeater
    variants: [
        { size: 'M', color: 'Black', sku: '', price: '', stock_quantity: 10 }
    ],

    // 3. Specifications Repeater
    specifications: [
        { spec_key: 'Material', spec_value: '100% Cotton' },
        { spec_key: 'Warranty', spec_value: '1 Year Manufacturer Warranty' }
    ],

    // 4. FAQs Repeater
    faqs: [
        { question: 'Is this item returnable?', answer: 'Yes, 7 days replacement and return policy applies.' }
    ],

    // 5. Highlights Repeater
    highlights: [
        { highlight_text: 'Premium durable build and authentic finish.' },
        { highlight_text: 'Designed for daily comfortable use.' }
    ],
})

// Image previews
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

// 2. Variants Repeater methods
const addVariant = () => {
    form.variants.push({ size: '', color: '', sku: '', price: form.price || '', stock_quantity: 0 })
}
const removeVariant = (index) => {
    form.variants.splice(index, 1)
}

// 3. Specs Repeater methods
const addSpec = () => {
    form.specifications.push({ spec_key: '', spec_value: '' })
}
const removeSpec = (index) => {
    form.specifications.splice(index, 1)
}

// 4. FAQs Repeater methods
const addFaq = () => {
    form.faqs.push({ question: '', answer: '' })
}
const removeFaq = (index) => {
    form.faqs.splice(index, 1)
}

// 5. Highlights Repeater methods
const addHighlight = () => {
    form.highlights.push({ highlight_text: '' })
}
const removeHighlight = (index) => {
    form.highlights.splice(index, 1)
}

// Submit
const submit = () => {
    form.post('/product', {
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
                        <span>✨ Create Product with 5 Repeaters</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Add images, variants, specifications, FAQs, and bullet highlights in one comprehensive form.
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
                    <span>🖼️ Images ({{ form.images.length }})</span>
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
                                placeholder="e.g. Classic Cotton Hoodie"
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
                                placeholder="Auto-generated if empty"
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
                                placeholder="0.00"
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
                            placeholder="Enter detailed description, usage instructions, warranty info..."
                            class="w-full px-4 py-2.5 text-sm bg-slate-50 border rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                            :class="form.errors.details ? 'border-rose-400' : 'border-slate-200'"
                        ></textarea>
                        <p v-if="form.errors.details" class="mt-1 text-xs text-rose-600 font-semibold">{{ form.errors.details }}</p>
                    </div>
                </div>

                <!-- 2. TAB: Images Repeater -->
                <div v-show="currentTab === 'images'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>🖼️</span>
                                <span>Product Images Repeater</span>
                            </h3>
                            <p class="text-xs text-slate-500">Upload multiple images. First image will be set as primary.</p>
                        </div>
                    </div>

                    <!-- Upload Input Box -->
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-indigo-500 bg-slate-50/50 transition">
                        <input
                            type="file"
                            multiple
                            accept="image/*"
                            @change="handleImageUpload"
                            class="hidden"
                            id="imageUploadInput"
                        />
                        <label for="imageUploadInput" class="cursor-pointer block">
                            <span class="text-3xl block mb-2">📸</span>
                            <span class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                Click to upload multiple product images
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-1">PNG, JPG, WEBP up to 5MB each</span>
                        </label>
                    </div>

                    <!-- Previews Grid -->
                    <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                        <div
                            v-for="(img, idx) in imagePreviews"
                            :key="idx"
                            class="relative rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 aspect-square group"
                        >
                            <img :src="img.url" class="w-full h-full object-cover" />
                            <span
                                v-if="idx === 0"
                                class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs"
                            >
                                Primary
                            </span>
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

                <!-- 3. TAB: Variants Repeater -->
                <div v-show="currentTab === 'variants'" class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <span>🔀</span>
                                <span>Product Variants Repeater (Size, Color, SKU, Price, Stock)</span>
                            </h3>
                            <p class="text-xs text-slate-500">Add dynamic size/color combinations with specific prices and stock.</p>
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
                        No variants added. Click "+ Add Variant Row" to create size/color variations.
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
                                        placeholder="e.g. M, XL, 32"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Color</label>
                                    <input
                                        v-model="v.color"
                                        type="text"
                                        placeholder="e.g. Navy Blue"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Variant SKU</label>
                                    <input
                                        v-model="v.sku"
                                        type="text"
                                        placeholder="Auto if blank"
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
                                        placeholder="Default price"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Stock</label>
                                    <input
                                        v-model="v.stock_quantity"
                                        type="number"
                                        min="0"
                                        placeholder="Units"
                                        class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="removeVariant(idx)"
                                class="p-2 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition"
                                title="Remove Variant"
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
                                placeholder="Specification Name (e.g. Fabric, Weight)"
                                class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                            <input
                                v-model="s.spec_value"
                                type="text"
                                placeholder="Specification Value (e.g. 100% Cotton, 450g)"
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
                        No highlights added. Click "+ Add Bullet Point" to add product highlights.
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
                                placeholder="e.g. Breathable organic fabric for all-day comfort"
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
                            <p class="text-xs text-slate-500">Frequently asked questions and answers for this product.</p>
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
                        No FAQs added. Click "+ Add FAQ Row" to add Q&A pairs.
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
                                    placeholder="e.g. How do I wash this garment?"
                                    class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Answer</label>
                                <textarea
                                    v-model="f.answer"
                                    rows="2"
                                    placeholder="e.g. Machine wash cold with similar colors..."
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
                        <span>Save Product & Repeaters</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

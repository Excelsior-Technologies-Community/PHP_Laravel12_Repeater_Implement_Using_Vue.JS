<script setup>
import { computed, ref, watch } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
    products: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {}
        })
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            sort: 'oldest',
            per_page: 5,
            min_price: '',
            max_price: '',
            image_filter: ''
        })
    },

    statistics: {
        type: Object,
        default: () => ({
            total_products: 0,
            total_images: 0,
            products_with_images: 0,
            products_without_images: 0
        })
    }
})

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const search = ref(props.filters.search || '')
const sort = ref(props.filters.sort || 'oldest')
const perPage = ref(Number(props.filters.per_page || 5))
const minPrice = ref(props.filters.min_price || '')
const maxPrice = ref(props.filters.max_price || '')
const imageFilter = ref(props.filters.image_filter || '')

const selectedProducts = ref([])

/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

const successMessage = computed(() => {
    return page.props.flash?.success || ''
})

/*
|--------------------------------------------------------------------------
| All Selected
|--------------------------------------------------------------------------
*/

const allSelected = computed(() => {
    if (!props.products.data.length) {
        return false
    }

    return props.products.data.every(product =>
        selectedProducts.value.includes(product.id)
    )
})

/*
|--------------------------------------------------------------------------
| Search / Filter
|--------------------------------------------------------------------------
*/

const applyFilters = () => {
    router.get(
        route('product.index'),
        {
            search: search.value,
            sort: sort.value,
            per_page: perPage.value,
            min_price: minPrice.value,
            max_price: maxPrice.value,
            image_filter: imageFilter.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    )
}

const resetFilters = () => {
    search.value = ''
    sort.value = 'oldest'
    perPage.value = 5
    minPrice.value = ''
    maxPrice.value = ''
    imageFilter.value = ''

    applyFilters()
}

/*
|--------------------------------------------------------------------------
| Auto Apply Filters
|--------------------------------------------------------------------------
*/

watch(perPage, () => {
    applyFilters()
})

watch(sort, () => {
    applyFilters()
})

watch(imageFilter, () => {
    applyFilters()
})

/*
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/

const toggleProduct = (id) => {
    if (selectedProducts.value.includes(id)) {
        selectedProducts.value =
            selectedProducts.value.filter(item => item !== id)
    } else {
        selectedProducts.value.push(id)
    }
}

const toggleAll = () => {
    if (allSelected.value) {
        selectedProducts.value = []
    } else {
        selectedProducts.value =
            props.products.data.map(product => product.id)
    }
}

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

const bulkDelete = () => {
    if (!selectedProducts.value.length) {
        alert('Please select at least one product.')
        return
    }

    if (
        confirm(
            `Are you sure you want to delete ${selectedProducts.value.length} product(s)?`
        )
    ) {
        router.delete(
            route('product.bulk.destroy'),
            {
                data: {
                    ids: selectedProducts.value
                },
                preserveScroll: true,
                onSuccess: () => {
                    selectedProducts.value = []
                }
            }
        )
    }
}

/*
|--------------------------------------------------------------------------
| Delete Product
|--------------------------------------------------------------------------
*/

const destroy = (id) => {
    if (
        confirm(
            'Are you sure you want to delete this product and all its images?'
        )
    ) {
        router.delete(
            route('product.destroy', id),
            {
                preserveScroll: true
            }
        )
    }
}

/*
|--------------------------------------------------------------------------
| Duplicate Product
|--------------------------------------------------------------------------
*/

const duplicate = (id) => {
    if (
        confirm(
            'Do you want to duplicate this product and all its images?'
        )
    ) {
        router.post(
            route('product.duplicate', id),
            {},
            {
                preserveScroll: true
            }
        )
    }
}

/*
|--------------------------------------------------------------------------
| CSV Export
|--------------------------------------------------------------------------
*/

const exportCsv = () => {
    const params = new URLSearchParams()

    if (search.value) {
        params.append('search', search.value)
    }

    if (sort.value) {
        params.append('sort', sort.value)
    }

    if (minPrice.value !== '') {
        params.append('min_price', minPrice.value)
    }

    if (maxPrice.value !== '') {
        params.append('max_price', maxPrice.value)
    }

    if (imageFilter.value) {
        params.append('image_filter', imageFilter.value)
    }

    const queryString = params.toString()

    window.location.href =
        route('product.export.csv') +
        (queryString ? '?' + queryString : '')
}

/*
|--------------------------------------------------------------------------
| Format Price
|--------------------------------------------------------------------------
*/

const formatPrice = (price) => {
    return Number(price).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 p-6">

        <div class="mx-auto max-w-7xl">

            <div class="rounded-xl bg-white p-6 shadow-lg">

                <!-- ===================================================== -->
                <!-- SUCCESS MESSAGE -->
                <!-- ===================================================== -->

                <div
                    v-if="successMessage"
                    class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700"
                >
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100"
                    >
                        ✅
                    </div>

                    <div class="font-medium">
                        {{ successMessage }}
                    </div>
                </div>

                <!-- ===================================================== -->
                <!-- HEADER -->
                <!-- ===================================================== -->

                <div
                    class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                >

                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            📦 Products
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Manage products and repeater image galleries.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">

                        <button
                            type="button"
                            @click="exportCsv"
                            class="rounded-lg bg-green-600 px-4 py-2 text-white hover:bg-green-700"
                        >
                            📥 Export CSV
                        </button>

                        <Link
                            :href="route('product.create')"
                            class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700"
                        >
                            ➕ Create Product
                        </Link>

                    </div>

                </div>

                <!-- ===================================================== -->
                <!-- STATISTICS -->
                <!-- ===================================================== -->

                <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">

                    <!-- Total Products -->

                    <div class="rounded-xl bg-blue-50 p-4">
                        <div class="text-sm font-medium text-blue-600">
                            Total Products
                        </div>

                        <div class="mt-1 text-2xl font-bold text-blue-800">
                            {{ statistics.total_products }}
                        </div>
                    </div>

                    <!-- Total Images -->

                    <div class="rounded-xl bg-purple-50 p-4">
                        <div class="text-sm font-medium text-purple-600">
                            Total Images
                        </div>

                        <div class="mt-1 text-2xl font-bold text-purple-800">
                            {{ statistics.total_images }}
                        </div>
                    </div>

                    <!-- With Images -->

                    <div class="rounded-xl bg-green-50 p-4">
                        <div class="text-sm font-medium text-green-600">
                            With Images
                        </div>

                        <div class="mt-1 text-2xl font-bold text-green-800">
                            {{ statistics.products_with_images }}
                        </div>
                    </div>

                    <!-- Without Images -->

                    <div class="rounded-xl bg-yellow-50 p-4">
                        <div class="text-sm font-medium text-yellow-600">
                            Without Images
                        </div>

                        <div class="mt-1 text-2xl font-bold text-yellow-800">
                            {{ statistics.products_without_images }}
                        </div>
                    </div>

                </div>

                <!-- ===================================================== -->
                <!-- SEARCH / FILTERS -->
                <!-- ===================================================== -->

                <div class="mb-6 rounded-xl bg-gray-50 p-4">

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">

                        <!-- Search -->

                        <div class="lg:col-span-2">

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Search
                            </label>

                            <input
                                v-model="search"
                                @keyup.enter="applyFilters"
                                type="text"
                                placeholder="Search name, details or ID..."
                                class="w-full rounded-lg border-gray-300"
                            />

                        </div>

                        <!-- Minimum Price -->

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Min Price
                            </label>

                            <input
                                v-model="minPrice"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="w-full rounded-lg border-gray-300"
                            />

                        </div>

                        <!-- Maximum Price -->

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Max Price
                            </label>

                            <input
                                v-model="maxPrice"
                                type="number"
                                min="0"
                                placeholder="100000"
                                class="w-full rounded-lg border-gray-300"
                            />

                        </div>

                        <!-- Image Filter -->

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Images
                            </label>

                            <select
                                v-model="imageFilter"
                                class="w-full rounded-lg border-gray-300"
                            >

                                <option value="">
                                    All
                                </option>

                                <option value="with_images">
                                    With Images
                                </option>

                                <option value="no_images">
                                    No Images
                                </option>

                                <option value="multiple_images">
                                    Multiple Images
                                </option>

                            </select>

                        </div>

                        <!-- Sort -->

                        <div>

                            <label
                                class="mb-1 block text-sm font-medium text-gray-700"
                            >
                                Sort
                            </label>

                            <select
                                v-model="sort"
                                class="w-full rounded-lg border-gray-300"
                            >

                                <option value="latest">
                                    Newest
                                </option>

                                <option value="oldest">
                                    Oldest
                                </option>

                                <option value="name_asc">
                                    Name A-Z
                                </option>

                                <option value="name_desc">
                                    Name Z-A
                                </option>

                                <option value="price_asc">
                                    Price Low-High
                                </option>

                                <option value="price_desc">
                                    Price High-Low
                                </option>

                            </select>

                        </div>

                    </div>

                    <!-- Buttons -->

                    <div class="mt-4 flex flex-wrap gap-2">

                        <button
                            type="button"
                            @click="applyFilters"
                            class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700"
                        >
                            🔎 Search / Filter
                        </button>

                        <button
                            type="button"
                            @click="resetFilters"
                            class="rounded-lg bg-gray-600 px-5 py-2 text-white hover:bg-gray-700"
                        >
                            ↻ Reset
                        </button>

                    </div>

                </div>

                <!-- ===================================================== -->
                <!-- BULK ACTION -->
                <!-- ===================================================== -->

                <div
                    v-if="selectedProducts.length"
                    class="mb-4 flex items-center justify-between rounded-lg bg-red-50 p-4"
                >

                    <div class="font-medium text-red-700">
                        {{ selectedProducts.length }}
                        product(s) selected
                    </div>

                    <button
                        type="button"
                        @click="bulkDelete"
                        class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                    >
                        🗑 Delete Selected
                    </button>

                </div>

                <!-- ===================================================== -->
                <!-- EMPTY -->
                <!-- ===================================================== -->

                <div
                    v-if="products.data.length === 0"
                    class="rounded-xl border-2 border-dashed py-16 text-center"
                >

                    <div class="mb-3 text-5xl">
                        📦
                    </div>

                    <h2 class="text-xl font-semibold text-gray-700">
                        No Products Found
                    </h2>

                    <p class="mt-1 text-gray-500">
                        Try changing your search or filters.
                    </p>

                    <Link
                        :href="route('product.create')"
                        class="mt-4 inline-block rounded-lg bg-blue-600 px-5 py-2 text-white"
                    >
                        Create Product
                    </Link>

                </div>

                <!-- ===================================================== -->
                <!-- TABLE -->
                <!-- ===================================================== -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table class="w-full border border-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <!-- Select -->

                                <th class="th text-center">
                                    <input
                                        type="checkbox"
                                        :checked="allSelected"
                                        @change="toggleAll"
                                    />
                                </th>

                                <!-- Product -->

                                <th class="th">
                                    Product
                                </th>

                                <!-- Price -->

                                <th class="th">
                                    Price
                                </th>

                                <!-- Primary -->

                                <th class="th">
                                    Primary
                                </th>

                                <!-- Gallery -->

                                <th class="th">
                                    Gallery
                                </th>

                                <!-- Images -->

                                <th class="th text-center">
                                    Images
                                </th>

                                <!-- Actions -->

                                <th class="th text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="product in products.data"
                                :key="product.id"
                                class="transition hover:bg-gray-50"
                            >

                                <!-- ================================================= -->
                                <!-- Checkbox -->
                                <!-- ================================================= -->

                                <td class="td text-center">

                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedProducts.includes(product.id)
                                        "
                                        @change="
                                            toggleProduct(product.id)
                                        "
                                    />

                                </td>

                                <!-- ================================================= -->
                                <!-- Product -->
                                <!-- ================================================= -->

                                <td class="td">

                                    <div class="font-semibold text-gray-800">
                                        {{ product.name }}
                                    </div>

                                    <div
                                        class="mt-1 max-w-xs text-xs text-gray-500"
                                    >
                                        {{ product.details }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-400">
                                        ID: {{ product.id }}
                                    </div>

                                </td>

                                <!-- ================================================= -->
                                <!-- Price -->
                                <!-- ================================================= -->

                                <td class="td">

                                    <span
                                        class="font-semibold text-green-600"
                                    >
                                        ₹ {{ formatPrice(product.price) }}
                                    </span>

                                </td>

                                <!-- ================================================= -->
                                <!-- Primary Image -->
                                <!-- ================================================= -->

                                <td class="td">

                                    <div
                                        v-if="product.primary_image"
                                        class="relative inline-block"
                                    >

                                        <img
                                            :src="`/${product.primary_image.image}`"
                                            class="h-20 w-20 rounded-lg border-2 border-yellow-400 object-cover"
                                        />

                                        <span
                                            class="absolute -right-2 -top-2 rounded-full bg-yellow-400 px-2 py-1 text-xs"
                                        >
                                            ⭐
                                        </span>

                                    </div>

                                    <div
                                        v-else
                                        class="flex h-20 w-20 items-center justify-center rounded-lg bg-gray-100 text-xs text-gray-400"
                                    >
                                        No Image
                                    </div>

                                </td>

                                <!-- ================================================= -->
                                <!-- Gallery -->
                                <!-- ================================================= -->

                                <td class="td">

                                    <div class="flex flex-wrap gap-2">

                                        <div
                                            v-for="(img, index) in product.images.slice(0, 5)"
                                            :key="img.id"
                                            class="relative"
                                        >

                                            <img
                                                :src="`/${img.image}`"
                                                class="h-12 w-12 rounded-lg border object-cover"
                                            />

                                            <span
                                                class="absolute -left-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-gray-800 text-[10px] text-white"
                                            >
                                                {{ index + 1 }}
                                            </span>

                                            <span
                                                v-if="img.is_primary"
                                                class="absolute -bottom-1 -right-1 text-xs"
                                            >
                                                ⭐
                                            </span>

                                        </div>

                                        <div
                                            v-if="product.images.length > 5"
                                            class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-200 text-xs font-semibold text-gray-600"
                                        >
                                            +{{ product.images.length - 5 }}
                                        </div>

                                    </div>

                                </td>

                                <!-- ================================================= -->
                                <!-- Image Count -->
                                <!-- ================================================= -->

                                <td class="td text-center">

                                    <span
                                        class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-700"
                                    >
                                        🖼️ {{ product.images_count }}
                                    </span>

                                </td>

                                <!-- ================================================= -->
                                <!-- Actions -->
                                <!-- ================================================= -->

                                <td class="td text-center">

                                    <div
                                        class="flex flex-wrap justify-center gap-2"
                                    >

                                        <Link
                                            :href="
                                                route(
                                                    'product.edit',
                                                    product.id
                                                )
                                            "
                                            class="font-medium text-blue-600 hover:text-blue-800"
                                        >
                                            ✏️ Edit
                                        </Link>

                                        <button
                                            type="button"
                                            @click="
                                                duplicate(product.id)
                                            "
                                            class="font-medium text-purple-600 hover:text-purple-800"
                                        >
                                            📋 Copy
                                        </button>

                                        <button
                                            type="button"
                                            @click="
                                                destroy(product.id)
                                            "
                                            class="font-medium text-red-600 hover:text-red-800"
                                        >
                                            🗑 Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <!-- ===================================================== -->
                <!-- PAGINATION -->
                <!-- ===================================================== -->

                <div
                    v-if="products.data.length"
                    class="mt-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                >

                    <!-- Per Page -->

                    <div class="flex items-center gap-2">

                        <span class="text-sm text-gray-600">
                            Per page:
                        </span>

                        <select
                            v-model.number="perPage"
                            class="rounded-lg border-gray-300 text-sm"
                        >

                            <option :value="5">
                                5
                            </option>

                            <option :value="10">
                                10
                            </option>

                            <option :value="25">
                                25
                            </option>

                            <option :value="50">
                                50
                            </option>

                            <option :value="100">
                                100
                            </option>

                        </select>

                    </div>

                    <!-- Pagination Links -->

                    <div class="flex flex-wrap gap-1">

                        <template
                            v-for="(link, index) in products.links"
                            :key="index"
                        >

                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                preserve-scroll
                                class="rounded-lg border px-3 py-2 text-sm"
                                :class="{
                                    'bg-blue-600 text-white':
                                        link.active,

                                    'bg-white text-gray-700 hover:bg-gray-100':
                                        !link.active
                                }"
                            />

                            <span
                                v-else
                                v-html="link.label"
                                class="rounded-lg border px-3 py-2 text-sm text-gray-400"
                            />

                        </template>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>
.th {
    @apply border-b px-4 py-3 text-left font-semibold text-gray-700;
}

.td {
    @apply border-b px-4 py-4 align-middle;
}
</style>
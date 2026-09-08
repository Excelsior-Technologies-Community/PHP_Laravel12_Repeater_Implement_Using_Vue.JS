<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    }
})

const destroy = (id) => {

    if (
        confirm(
            'Are you sure you want to delete this product and all its images?'
        )
    ) {
        router.delete(`/product/${id}`)
    }
}
</script>

<template>

    <div class="min-h-screen bg-gray-100 p-6">

        <div class="max-w-7xl mx-auto">

            <!-- Main Card -->

            <div class="bg-white shadow-lg rounded-xl p-6">

                <!-- Header -->

                <div class="flex justify-between items-center mb-6">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-800">
                            📦 Products
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Manage products and their image galleries.
                        </p>

                    </div>

                    <a
                        href="/product/create"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700"
                    >
                        ➕ Create Product
                    </a>

                </div>

                <!-- Statistics -->

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                    <div class="bg-blue-50 rounded-xl p-4">

                        <div class="text-sm text-blue-600 font-medium">
                            Total Products
                        </div>

                        <div class="text-2xl font-bold text-blue-800 mt-1">
                            {{ products.length }}
                        </div>

                    </div>

                    <div class="bg-purple-50 rounded-xl p-4">

                        <div class="text-sm text-purple-600 font-medium">
                            Total Images
                        </div>

                        <div class="text-2xl font-bold text-purple-800 mt-1">
                            {{
                                products.reduce(
                                    (total, product) =>
                                        total + product.images.length,
                                    0
                                )
                            }}
                        </div>

                    </div>

                    <div class="bg-yellow-50 rounded-xl p-4">

                        <div class="text-sm text-yellow-600 font-medium">
                            Primary Images
                        </div>

                        <div class="text-2xl font-bold text-yellow-800 mt-1">
                            {{
                                products.filter(
                                    product => product.primary_image
                                ).length
                            }}
                        </div>

                    </div>

                </div>

                <!-- Empty State -->

                <div
                    v-if="products.length === 0"
                    class="text-center py-16 border-2 border-dashed rounded-xl"
                >

                    <div class="text-5xl mb-3">
                        📦
                    </div>

                    <h2 class="text-xl font-semibold text-gray-700">
                        No Products Found
                    </h2>

                    <p class="text-gray-500 mt-1">
                        Create your first product to get started.
                    </p>

                    <a
                        href="/product/create"
                        class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-lg"
                    >
                        Create Product
                    </a>

                </div>

                <!-- Product Table -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table class="w-full border border-gray-200 rounded-lg">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="th">
                                    Product
                                </th>

                                <th class="th">
                                    Price
                                </th>

                                <th class="th">
                                    Primary Image
                                </th>

                                <th class="th">
                                    Gallery
                                </th>

                                <th class="th text-center">
                                    Images
                                </th>

                                <th class="th text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="product in products"
                                :key="product.id"
                                class="hover:bg-gray-50 transition"
                            >

                                <!-- Product -->

                                <td class="td">

                                    <div class="font-semibold text-gray-800">
                                        {{ product.name }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1 max-w-xs">
                                        {{ product.details }}
                                    </div>

                                </td>

                                <!-- Price -->

                                <td class="td">

                                    <span class="font-semibold text-green-600">
                                        ₹ {{ product.price }}
                                    </span>

                                </td>

                                <!-- Primary Image -->

                                <td class="td">

                                    <div
                                        v-if="product.primary_image"
                                        class="relative inline-block"
                                    >

                                        <img
                                            :src="`/${product.primary_image.image}`"
                                            class="w-20 h-20 object-cover rounded-lg border-2 border-yellow-400"
                                        />

                                        <span
                                            class="absolute -top-2 -right-2 bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded-full font-bold"
                                        >
                                            ⭐
                                        </span>

                                    </div>

                                    <div
                                        v-else
                                        class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs"
                                    >
                                        No Image
                                    </div>

                                </td>

                                <!-- Gallery -->

                                <td class="td">

                                    <div class="flex gap-2 flex-wrap">

                                        <div
                                            v-for="(img, index) in product.images.slice(0, 5)"
                                            :key="img.id"
                                            class="relative"
                                        >

                                            <img
                                                :src="`/${img.image}`"
                                                class="w-12 h-12 object-cover rounded-lg border"
                                            />

                                            <span
                                                class="absolute -top-1 -left-1 bg-gray-800 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full"
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
                                            class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600"
                                        >
                                            +{{ product.images.length - 5 }}
                                        </div>

                                    </div>

                                </td>

                                <!-- Image Count -->

                                <td class="td text-center">

                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 font-semibold text-sm"
                                    >
                                        🖼️ {{ product.images.length }}
                                    </span>

                                </td>

                                <!-- Actions -->

                                <td class="td text-center">

                                    <a
                                        :href="`/product/${product.id}/edit`"
                                        class="inline-block text-blue-600 hover:text-blue-800 font-medium mr-4"
                                    >
                                        ✏️ Edit
                                    </a>

                                    <button
                                        @click="destroy(product.id)"
                                        class="text-red-600 hover:text-red-800 font-medium"
                                    >
                                        🗑 Delete
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</template>

<style scoped>

.th {
    @apply px-4 py-3 text-left font-semibold text-gray-700 border-b;
}

.td {
    @apply px-4 py-4 border-b align-middle;
}

</style>
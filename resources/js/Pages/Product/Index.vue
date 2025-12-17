<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
  products: Array
})

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    router.delete(`/product/${id}`)
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 p-10">
    <div class="max-w-6xl mx-auto bg-white shadow rounded-xl p-6">

      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
          📦 Products
        </h1>

        <!-- Create Button -->
        <a
          href="/product/create"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
        >
          ➕ Create New Product
        </a>
      </div>

      <!-- Empty State -->
      <div v-if="products.length === 0" class="text-center text-gray-500 py-10">
        No products found.
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
          <thead class="bg-gray-50">
            <tr>
              <th class="th">Name</th>
              <th class="th">Images</th>
              <th class="th text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="p in products"
              :key="p.id"
              class="hover:bg-gray-50"
            >
              <td class="td font-medium text-gray-800">
                {{ p.name }}
              </td>

              <td class="td">
                <div class="flex gap-2">
                  <img
                    v-for="img in p.images"
                    :key="img.id"
                    :src="`/${img.image}`"
                    class="w-12 h-12 rounded border"
                  />
                </div>
              </td>

              <td class="td text-center">
                <a
                  :href="`/product/${p.id}/edit`"
                  class="text-blue-600 hover:underline mr-4"
                >
                  Edit
                </a>

                <button
                  @click="destroy(p.id)"
                  class="text-red-600 hover:underline"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>

<style scoped>
.th {
  @apply px-4 py-3 text-left font-semibold text-gray-700 border-b;
}

.td {
  @apply px-4 py-3 border-b;
}
</style>

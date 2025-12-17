<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({
  name: '',
  details: '',
  price: '',
  images: [] // repeater array
})

// add new empty image row
const addImage = () => {
  form.value.images.push({
    file: null,
    preview: null
  })
}

// remove image row
const removeImage = (index) => {
  form.value.images.splice(index, 1)
}

// handle image select
const handleImage = (e, index) => {
  const file = e.target.files[0]

  if (file) {
    form.value.images[index].file = file
    form.value.images[index].preview = URL.createObjectURL(file)
  }
}

// submit form
const store = () => {
  const data = new FormData()

  data.append('name', form.value.name)
  data.append('details', form.value.details)
  data.append('price', form.value.price)

  form.value.images.forEach((img, i) => {
    if (img.file) {
      data.append(`images[${i}]`, img.file)
    }
  })

  router.post('/product', data)
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex justify-center items-center">
    <div class="bg-white w-full max-w-lg shadow rounded-xl p-6">

      <h2 class="text-2xl font-semibold mb-6">➕ Create Product</h2>

      <!-- Name -->
      <input v-model="form.name" class="input mb-3" placeholder="Product name" />

      <!-- Details -->
      <textarea v-model="form.details" class="input mb-3" placeholder="Details"></textarea>

      <!-- Price -->
      <input v-model="form.price" type="number" class="input mb-4" placeholder="Price" />

      <!-- Repeater Images -->
      <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <label class="font-medium">Product Images</label>
          <button
            type="button"
            @click="addImage"
            class="text-blue-600 font-medium"
          >
            + Add Image
          </button>
        </div>

        <!-- Repeater rows -->
        <div
          v-for="(img, index) in form.images"
          :key="index"
          class="flex items-center gap-3 mb-3"
        >
          <input
            type="file"
            @change="e => handleImage(e, index)"
          />

          <!-- Preview -->
          <img
            v-if="img.preview"
            :src="img.preview"
            class="w-16 h-16 rounded border"
          />

          <!-- Remove -->
          <button
            type="button"
            @click="removeImage(index)"
            class="text-red-600"
          >
            Remove
          </button>
        </div>
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-3">
        <a href="/product" class="btn-secondary">Cancel</a>
        <button @click="store" class="btn-primary">Save</button>
      </div>

    </div>
  </div>
</template>

<style scoped>
.input {
  @apply w-full border border-gray-300 rounded-lg px-3 py-2;
}
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-lg;
}
.btn-secondary {
  @apply bg-gray-200 px-4 py-2 rounded-lg;
}
</style>

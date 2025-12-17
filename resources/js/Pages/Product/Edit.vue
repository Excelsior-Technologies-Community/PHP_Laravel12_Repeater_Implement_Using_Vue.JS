<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  product: Object
})

const form = ref({
  name: props.product.name,
  details: props.product.details,
  price: props.product.price,

  // repeater for NEW images
  new_images: [],

  // ids of removed EXISTING images
  remove_images: []
})

/* ============================
   EXISTING IMAGE REMOVE
============================ */
const removeExistingImage = (id) => {
  form.value.remove_images.push(id)
  props.product.images = props.product.images.filter(img => img.id !== id)
}

/* ============================
   REPEATER FUNCTIONS
============================ */
const addImageRow = () => {
  form.value.new_images.push({
    file: null,
    preview: null
  })
}

const removeImageRow = (index) => {
  form.value.new_images.splice(index, 1)
}

const handleImage = (e, index) => {
  const file = e.target.files[0]
  if (file) {
    form.value.new_images[index].file = file
    form.value.new_images[index].preview = URL.createObjectURL(file)
  }
}

/* ============================
   SUBMIT
============================ */
const update = () => {
  const data = new FormData()

  data.append('_method', 'PUT')
  data.append('name', form.value.name)
  data.append('details', form.value.details)
  data.append('price', form.value.price)

  // removed old images
  form.value.remove_images.forEach((id, i) => {
    data.append(`remove_images[${i}]`, id)
  })

  // new images from repeater
  form.value.new_images.forEach((img, i) => {
    if (img.file) {
      data.append(`images[${i}]`, img.file)
    }
  })

  router.post(`/product/${props.product.id}`, data)
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex justify-center items-center">
    <div class="bg-white w-full max-w-lg shadow rounded-xl p-6">

      <h2 class="text-2xl font-semibold mb-6">✏️ Edit Product</h2>

      <!-- Basic fields -->
      <input v-model="form.name" class="input mb-3" placeholder="Name" />
      <textarea v-model="form.details" class="input mb-3" placeholder="Details"></textarea>
      <input v-model="form.price" type="number" class="input mb-4" placeholder="Price" />

      <!-- EXISTING IMAGES -->
      <div class="mb-6">
        <label class="label">Existing Images</label>
        <div class="flex gap-3 flex-wrap mt-2">
          <div
            v-for="img in product.images"
            :key="img.id"
            class="relative"
          >
            <img
              :src="`/${img.image}`"
              class="w-20 h-20 rounded border"
            />
            <button
              type="button"
              @click="removeExistingImage(img.id)"
              class="absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 rounded-full text-sm"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- NEW IMAGE REPEATER -->
      <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
          <label class="label">Add New Images</label>
          <button
            type="button"
            @click="addImageRow"
            class="text-blue-600 font-medium"
          >
            + Add Image
          </button>
        </div>

        <div
          v-for="(img, index) in form.new_images"
          :key="index"
          class="flex items-center gap-3 mb-3"
        >
          <input type="file" @change="e => handleImage(e, index)" />

          <img
            v-if="img.preview"
            :src="img.preview"
            class="w-16 h-16 rounded border"
          />

          <button
            type="button"
            @click="removeImageRow(index)"
            class="text-red-600"
          >
            Remove
          </button>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3">
        <a href="/product" class="btn-secondary">Cancel</a>
        <button @click="update" class="btn-primary">Update</button>
      </div>

    </div>
  </div>
</template>

<style scoped>
.label {
  @apply block mb-1 font-medium text-gray-700;
}
.input {
  @apply w-full border border-gray-300 rounded-lg px-3 py-2;
}
.btn-primary {
  @apply bg-green-600 text-white px-4 py-2 rounded-lg;
}
.btn-secondary {
  @apply bg-gray-200 px-4 py-2 rounded-lg;
}
</style>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const form = ref({
    name: '',
    details: '',
    price: '',
    images: []
})

/*
|--------------------------------------------------------------------------
| Add image row
|--------------------------------------------------------------------------
*/
const addImage = () => {
    form.value.images.push({
        file: null,
        preview: null
    })
}

/*
|--------------------------------------------------------------------------
| Remove image row
|--------------------------------------------------------------------------
*/
const removeImage = (index) => {

    const image = form.value.images[index]

    if (image.preview) {
        URL.revokeObjectURL(image.preview)
    }

    form.value.images.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| Handle image
|--------------------------------------------------------------------------
*/
const handleImage = (event, index) => {

    const file = event.target.files[0]

    if (!file) {
        return
    }

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image.')
        return
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB.')
        event.target.value = ''
        return
    }

    form.value.images[index].file = file
    form.value.images[index].preview = URL.createObjectURL(file)
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/
const store = () => {

    if (!form.value.name.trim()) {
        alert('Please enter product name.')
        return
    }

    if (!form.value.details.trim()) {
        alert('Please enter product details.')
        return
    }

    if (!form.value.price) {
        alert('Please enter product price.')
        return
    }

    const data = new FormData()

    data.append('name', form.value.name)
    data.append('details', form.value.details)
    data.append('price', form.value.price)

    form.value.images.forEach((img, index) => {

        if (img.file) {
            data.append(
                `images[${index}]`,
                img.file
            )
        }
    })

    router.post('/product', data)
}
</script>

<template>

    <div class="min-h-screen bg-gray-100 p-6">

        <div class="max-w-2xl mx-auto">

            <div class="bg-white shadow-lg rounded-xl p-6">

                <!-- Header -->

                <div class="flex justify-between items-center mb-6">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-800">
                            ➕ Create Product
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Add product information and multiple images.
                        </p>

                    </div>

                    <a
                        href="/product"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Back
                    </a>

                </div>

                <!-- Product Name -->

                <div class="mb-4">

                    <label class="label">
                        Product Name
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="input"
                        placeholder="Enter product name"
                    />

                </div>

                <!-- Details -->

                <div class="mb-4">

                    <label class="label">
                        Product Details
                    </label>

                    <textarea
                        v-model="form.details"
                        rows="4"
                        class="input"
                        placeholder="Enter product details"
                    ></textarea>

                </div>

                <!-- Price -->

                <div class="mb-6">

                    <label class="label">
                        Price
                    </label>

                    <input
                        v-model="form.price"
                        type="number"
                        min="0"
                        step="0.01"
                        class="input"
                        placeholder="Enter product price"
                    />

                </div>

                <!-- Image Repeater -->

                <div class="border rounded-xl p-4 bg-gray-50">

                    <div class="flex justify-between items-center mb-4">

                        <div>

                            <h2 class="font-semibold text-gray-800">
                                🖼️ Product Images
                            </h2>

                            <p class="text-xs text-gray-500">
                                First image will automatically become primary.
                            </p>

                        </div>

                        <button
                            type="button"
                            @click="addImage"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                        >
                            + Add Image
                        </button>

                    </div>

                    <!-- Empty -->

                    <div
                        v-if="form.images.length === 0"
                        class="text-center py-8 border-2 border-dashed rounded-lg bg-white"
                    >

                        <div class="text-4xl mb-2">
                            📷
                        </div>

                        <p class="text-gray-500">
                            No images added yet.
                        </p>

                        <button
                            type="button"
                            @click="addImage"
                            class="text-blue-600 mt-2 font-medium"
                        >
                            Add your first image
                        </button>

                    </div>

                    <!-- Repeater -->

                    <div
                        v-for="(img, index) in form.images"
                        :key="index"
                        class="bg-white border rounded-lg p-3 mb-3"
                    >

                        <div class="flex items-center gap-4">

                            <!-- Number -->

                            <div
                                class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-700 rounded-full font-bold"
                            >
                                {{ index + 1 }}
                            </div>

                            <!-- File -->

                            <div class="flex-1">

                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    @change="e => handleImage(e, index)"
                                    class="w-full text-sm"
                                />

                            </div>

                            <!-- Preview -->

                            <img
                                v-if="img.preview"
                                :src="img.preview"
                                class="w-16 h-16 object-cover rounded-lg border"
                            />

                            <!-- Remove -->

                            <button
                                type="button"
                                @click="removeImage(index)"
                                class="text-red-600 hover:text-red-800"
                            >
                                ✕
                            </button>

                        </div>

                        <div
                            v-if="img.preview"
                            class="mt-2 text-xs text-green-600"
                        >
                            ✓ Image selected
                        </div>

                    </div>

                    <!-- Summary -->

                    <div
                        v-if="form.images.length"
                        class="mt-4 p-3 bg-blue-50 rounded-lg text-sm"
                    >

                        <div class="flex justify-between">

                            <span>
                                Total Image Rows
                            </span>

                            <strong>
                                {{ form.images.length }}
                            </strong>

                        </div>

                        <div class="flex justify-between mt-1">

                            <span>
                                Selected Images
                            </span>

                            <strong>
                                {{ form.images.filter(img => img.file).length }}
                            </strong>

                        </div>

                    </div>

                </div>

                <!-- Actions -->

                <div class="flex justify-end gap-3 mt-6">

                    <a
                        href="/product"
                        class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Cancel
                    </a>

                    <button
                        type="button"
                        @click="store"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Save Product
                    </button>

                </div>

            </div>

        </div>

    </div>

</template>

<style scoped>

.label {
    @apply block mb-2 font-medium text-gray-700;
}

.input {
    @apply w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring focus:ring-blue-100;
}

</style>
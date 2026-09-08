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
    new_images: [],
    remove_images: []
})

/*
|--------------------------------------------------------------------------
| Existing Images
|--------------------------------------------------------------------------
*/

const existingImages = ref(
    [...props.product.images]
)

/*
|--------------------------------------------------------------------------
| Remove existing image
|--------------------------------------------------------------------------
*/

const removeExistingImage = (id) => {

    const image = existingImages.value.find(
        img => img.id === id
    )

    if (!image) {
        return
    }

    if (
        !confirm(
            'Are you sure you want to remove this image?'
        )
    ) {
        return
    }

    form.value.remove_images.push(id)

    existingImages.value =
        existingImages.value.filter(
            img => img.id !== id
        )
}

/*
|--------------------------------------------------------------------------
| Add new image row
|--------------------------------------------------------------------------
*/

const addImageRow = () => {

    form.value.new_images.push({
        file: null,
        preview: null
    })
}

/*
|--------------------------------------------------------------------------
| Remove new image row
|--------------------------------------------------------------------------
*/

const removeImageRow = (index) => {

    const image =
        form.value.new_images[index]

    if (image.preview) {
        URL.revokeObjectURL(image.preview)
    }

    form.value.new_images.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| Handle new image
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

    form.value.new_images[index].file = file

    form.value.new_images[index].preview =
        URL.createObjectURL(file)
}

/*
|--------------------------------------------------------------------------
| Set Primary Image
|--------------------------------------------------------------------------
*/

const setPrimary = (imageId) => {

    router.post(
        `/product/${props.product.id}/set-primary/${imageId}`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {

                existingImages.value =
                    existingImages.value.map(img => ({
                        ...img,
                        is_primary: img.id === imageId
                    }))
            }
        }
    )
}

/*
|--------------------------------------------------------------------------
| Move Image Up
|--------------------------------------------------------------------------
*/

const moveUp = (index) => {

    if (index === 0) {
        return
    }

    const images = existingImages.value

    const temp = images[index - 1]

    images[index - 1] = images[index]
    images[index] = temp
}

/*
|--------------------------------------------------------------------------
| Move Image Down
|--------------------------------------------------------------------------
*/

const moveDown = (index) => {

    if (index === existingImages.value.length - 1) {
        return
    }

    const images = existingImages.value

    const temp = images[index + 1]

    images[index + 1] = images[index]
    images[index] = temp
}

/*
|--------------------------------------------------------------------------
| Save image order
|--------------------------------------------------------------------------
*/

const saveImageOrder = () => {

    const imageIds =
        existingImages.value.map(
            img => img.id
        )

    router.post(
        `/product/${props.product.id}/reorder-images`,
        {
            images: imageIds
        },
        {
            preserveScroll: true
        }
    )
}

/*
|--------------------------------------------------------------------------
| Update Product
|--------------------------------------------------------------------------
*/

const update = () => {

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

    data.append(
        '_method',
        'PUT'
    )

    data.append(
        'name',
        form.value.name
    )

    data.append(
        'details',
        form.value.details
    )

    data.append(
        'price',
        form.value.price
    )

    /*
    |--------------------------------------------------------------------------
    | Removed images
    |--------------------------------------------------------------------------
    */

    form.value.remove_images.forEach(
        (id, index) => {

            data.append(
                `remove_images[${index}]`,
                id
            )
        }
    )

    /*
    |--------------------------------------------------------------------------
    | New images
    |--------------------------------------------------------------------------
    */

    form.value.new_images.forEach(
        (img, index) => {

            if (img.file) {

                data.append(
                    `images[${index}]`,
                    img.file
                )
            }
        }
    )

    router.post(
        `/product/${props.product.id}`,
        data
    )
}
</script>

<template>

    <div class="min-h-screen bg-gray-100 p-6">

        <div class="max-w-3xl mx-auto">

            <div class="bg-white shadow-lg rounded-xl p-6">

                <!-- Header -->

                <div class="flex justify-between items-center mb-6">

                    <div>

                        <h1 class="text-2xl font-bold text-gray-800">
                            ✏️ Edit Product
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Manage product details and image gallery.
                        </p>

                    </div>

                    <a
                        href="/product"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Back
                    </a>

                </div>

                <!-- Product Information -->

                <div class="border rounded-xl p-4 mb-6">

                    <h2 class="font-semibold text-lg mb-4">
                        📦 Product Information
                    </h2>

                    <div class="mb-4">

                        <label class="label">
                            Product Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="input"
                        />

                    </div>

                    <div class="mb-4">

                        <label class="label">
                            Details
                        </label>

                        <textarea
                            v-model="form.details"
                            rows="4"
                            class="input"
                        ></textarea>

                    </div>

                    <div>

                        <label class="label">
                            Price
                        </label>

                        <input
                            v-model="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="input"
                        />

                    </div>

                </div>

                <!-- Existing Images -->

                <div class="border rounded-xl p-4 mb-6">

                    <div class="flex justify-between items-center mb-4">

                        <div>

                            <h2 class="font-semibold text-lg">
                                🖼️ Image Gallery
                            </h2>

                            <p class="text-sm text-gray-500">
                                Set primary image and change image order.
                            </p>

                        </div>

                        <div
                            class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm"
                        >
                            {{ existingImages.length }} Images
                        </div>

                    </div>

                    <!-- Empty -->

                    <div
                        v-if="existingImages.length === 0"
                        class="text-center py-8 text-gray-500 border-2 border-dashed rounded-lg"
                    >
                        No existing images.
                    </div>

                    <!-- Existing image cards -->

                    <div
                        v-for="(img, index) in existingImages"
                        :key="img.id"
                        class="border rounded-xl p-3 mb-3"
                        :class="img.is_primary
                            ? 'border-yellow-400 bg-yellow-50'
                            : 'bg-gray-50'"
                    >

                        <div class="flex items-center gap-4">

                            <!-- Order -->

                            <div
                                class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-200 font-bold"
                            >
                                {{ index + 1 }}
                            </div>

                            <!-- Image -->

                            <img
                                :src="`/${img.image}`"
                                class="w-24 h-24 object-cover rounded-lg border"
                            />

                            <!-- Details -->

                            <div class="flex-1">

                                <div
                                    v-if="img.is_primary"
                                    class="inline-block bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-bold mb-2"
                                >
                                    ⭐ PRIMARY IMAGE
                                </div>

                                <div
                                    v-else
                                    class="text-sm text-gray-500 mb-2"
                                >
                                    Product Image
                                </div>

                                <div class="flex flex-wrap gap-2">

                                    <!-- Primary -->

                                    <button
                                        v-if="!img.is_primary"
                                        type="button"
                                        @click="setPrimary(img.id)"
                                        class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-sm hover:bg-yellow-200"
                                    >
                                        ⭐ Set Primary
                                    </button>

                                    <span
                                        v-else
                                        class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-lg text-sm"
                                    >
                                        Primary
                                    </span>

                                    <!-- Up -->

                                    <button
                                        type="button"
                                        @click="moveUp(index)"
                                        :disabled="index === 0"
                                        class="px-3 py-1 bg-gray-200 rounded-lg text-sm disabled:opacity-40"
                                    >
                                        ↑ Up
                                    </button>

                                    <!-- Down -->

                                    <button
                                        type="button"
                                        @click="moveDown(index)"
                                        :disabled="index === existingImages.length - 1"
                                        class="px-3 py-1 bg-gray-200 rounded-lg text-sm disabled:opacity-40"
                                    >
                                        ↓ Down
                                    </button>

                                    <!-- Remove -->

                                    <button
                                        type="button"
                                        @click="removeExistingImage(img.id)"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200"
                                    >
                                        🗑 Remove
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Save Order -->

                    <button
                        v-if="existingImages.length > 1"
                        type="button"
                        @click="saveImageOrder"
                        class="mt-3 w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700"
                    >
                        🔢 Save Image Order
                    </button>

                </div>

                <!-- Add New Images -->

                <div class="border rounded-xl p-4 mb-6 bg-gray-50">

                    <div class="flex justify-between items-center mb-4">

                        <div>

                            <h2 class="font-semibold text-lg">
                                ➕ Add New Images
                            </h2>

                            <p class="text-sm text-gray-500">
                                Add new images using the repeater.
                            </p>

                        </div>

                        <button
                            type="button"
                            @click="addImageRow"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                        >
                            + Add Image
                        </button>

                    </div>

                    <!-- Empty -->

                    <div
                        v-if="form.new_images.length === 0"
                        class="text-center py-6 border-2 border-dashed rounded-lg bg-white"
                    >

                        <p class="text-gray-500">
                            No new images added.
                        </p>

                        <button
                            type="button"
                            @click="addImageRow"
                            class="text-blue-600 font-medium mt-2"
                        >
                            Add Image
                        </button>

                    </div>

                    <!-- Repeater -->

                    <div
                        v-for="(img, index) in form.new_images"
                        :key="index"
                        class="bg-white border rounded-lg p-3 mb-3"
                    >

                        <div class="flex items-center gap-4">

                            <div
                                class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-700 rounded-full font-bold"
                            >
                                {{ index + 1 }}
                            </div>

                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="e => handleImage(e, index)"
                                class="flex-1 text-sm"
                            />

                            <img
                                v-if="img.preview"
                                :src="img.preview"
                                class="w-16 h-16 object-cover rounded-lg border"
                            />

                            <button
                                type="button"
                                @click="removeImageRow(index)"
                                class="text-red-600"
                            >
                                ✕
                            </button>

                        </div>

                    </div>

                    <!-- New image summary -->

                    <div
                        v-if="form.new_images.length"
                        class="mt-4 p-3 bg-blue-50 rounded-lg text-sm"
                    >

                        <div class="flex justify-between">

                            <span>
                                New Image Rows
                            </span>

                            <strong>
                                {{ form.new_images.length }}
                            </strong>

                        </div>

                        <div class="flex justify-between mt-1">

                            <span>
                                Selected Images
                            </span>

                            <strong>
                                {{ form.new_images.filter(img => img.file).length }}
                            </strong>

                        </div>

                    </div>

                </div>

                <!-- Gallery Summary -->

                <div class="grid grid-cols-3 gap-3 mb-6">

                    <div class="bg-blue-50 rounded-xl p-4 text-center">

                        <div class="text-2xl font-bold text-blue-600">
                            {{ existingImages.length }}
                        </div>

                        <div class="text-xs text-gray-600">
                            Existing Images
                        </div>

                    </div>

                    <div class="bg-green-50 rounded-xl p-4 text-center">

                        <div class="text-2xl font-bold text-green-600">
                            {{ form.new_images.filter(img => img.file).length }}
                        </div>

                        <div class="text-xs text-gray-600">
                            New Images
                        </div>

                    </div>

                    <div class="bg-yellow-50 rounded-xl p-4 text-center">

                        <div class="text-2xl font-bold text-yellow-600">
                            {{ existingImages.filter(img => img.is_primary).length }}
                        </div>

                        <div class="text-xs text-gray-600">
                            Primary
                        </div>

                    </div>

                </div>

                <!-- Actions -->

                <div class="flex justify-end gap-3">

                    <a
                        href="/product"
                        class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Cancel
                    </a>

                    <button
                        type="button"
                        @click="update"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    >
                        ✓ Update Product
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
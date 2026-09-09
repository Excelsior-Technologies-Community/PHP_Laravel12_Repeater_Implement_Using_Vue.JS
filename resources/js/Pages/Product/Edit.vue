<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const form = useForm({
    name: props.product.name,
    details: props.product.details,
    price: props.product.price,
    images: [],
    remove_images: []
})

const previewImages = ref([])

/*
|--------------------------------------------------------------------------
| New Image Preview
|--------------------------------------------------------------------------
*/

const handleFiles = (event) => {
    const files = Array.from(event.target.files)

    form.images = files

    previewImages.value = files.map(file => ({
        name: file.name,
        url: URL.createObjectURL(file)
    }))
}

/*
|--------------------------------------------------------------------------
| Remove Existing Image During Update
|--------------------------------------------------------------------------
*/

const removeExistingImage = (id) => {
    if (!form.remove_images.includes(id)) {
        form.remove_images.push(id)
    }
}

/*
|--------------------------------------------------------------------------
| Delete Individual Image Immediately
|--------------------------------------------------------------------------
*/

const deleteImage = (image) => {
    if (
        confirm(
            'Are you sure you want to permanently delete this image?'
        )
    ) {
        router.delete(
            route(
                'product.image.destroy',
                {
                    product: props.product.id,
                    image: image.id
                }
            ),
            {
                preserveScroll: true
            }
        )
    }
}

/*
|--------------------------------------------------------------------------
| Set Primary
|--------------------------------------------------------------------------
*/

const setPrimary = (image) => {
    router.post(
        route(
            'product.image.primary',
            {
                product: props.product.id,
                image: image.id
            }
        ),
        {},
        {
            preserveScroll: true
        }
    )
}

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

const submit = () => {
    form.post(
        route(
            'product.update',
            props.product.id
        ),
        {
            _method: 'put',
            forceFormData: true,
            preserveScroll: true
        }
    )
}
</script>

<template>

    <Head :title="`Edit ${product.name}`" />

    <div class="min-h-screen bg-gray-100 p-6">

        <div class="mx-auto max-w-5xl">

            <div class="rounded-xl bg-white p-6 shadow-lg">

                <!-- Header -->

                <div
                    class="mb-6 flex items-center justify-between"
                >

                    <div>

                        <h1 class="text-2xl font-bold text-gray-800">
                            ✏️ Edit Product
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Update product information and repeater images.
                        </p>

                    </div>

                    <Link
                        :href="route('product.index')"
                        class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
                    >
                        ← Back
                    </Link>

                </div>

                <!-- Form -->

                <form
                    @submit.prevent="submit"
                    class="space-y-6"
                >

                    <!-- Name -->

                    <div>

                        <label
                            class="mb-1 block font-medium text-gray-700"
                        >
                            Product Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full rounded-lg border-gray-300"
                        />

                        <div
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </div>

                    </div>

                    <!-- Details -->

                    <div>

                        <label
                            class="mb-1 block font-medium text-gray-700"
                        >
                            Details
                        </label>

                        <textarea
                            v-model="form.details"
                            rows="5"
                            class="w-full rounded-lg border-gray-300"
                        ></textarea>

                        <div
                            v-if="form.errors.details"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.details }}
                        </div>

                    </div>

                    <!-- Price -->

                    <div>

                        <label
                            class="mb-1 block font-medium text-gray-700"
                        >
                            Price
                        </label>

                        <input
                            v-model="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-lg border-gray-300"
                        />

                        <div
                            v-if="form.errors.price"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.price }}
                        </div>

                    </div>

                    <!-- Existing Images -->

                    <div>

                        <h2 class="mb-3 text-lg font-semibold text-gray-800">
                            🖼️ Existing Images
                        </h2>

                        <div
                            v-if="product.images.length"
                            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
                        >

                            <div
                                v-for="image in product.images"
                                :key="image.id"
                                class="relative rounded-xl border p-2"
                            >

                                <img
                                    :src="`/${image.image}`"
                                    class="h-32 w-full rounded-lg object-cover"
                                />

                                <!-- Primary -->

                                <div
                                    v-if="image.is_primary"
                                    class="mt-2 text-center text-sm font-semibold text-yellow-600"
                                >
                                    ⭐ Primary Image
                                </div>

                                <button
                                    v-else
                                    type="button"
                                    @click="setPrimary(image)"
                                    class="mt-2 w-full rounded-lg bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700 hover:bg-yellow-200"
                                >
                                    ⭐ Make Primary
                                </button>

                                <!-- Delete -->

                                <button
                                    type="button"
                                    @click="deleteImage(image)"
                                    class="mt-2 w-full rounded-lg bg-red-100 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-200"
                                >
                                    🗑 Delete Image
                                </button>

                            </div>

                        </div>

                        <div
                            v-else
                            class="rounded-lg bg-gray-50 p-6 text-center text-gray-500"
                        >
                            No images available.
                        </div>

                    </div>

                    <!-- New Images -->

                    <div>

                        <label
                            class="mb-2 block font-medium text-gray-700"
                        >
                            Add New Images
                        </label>

                        <input
                            type="file"
                            multiple
                            accept=".jpg,.jpeg,.png,.webp"
                            @change="handleFiles"
                            class="block w-full rounded-lg border border-gray-300 p-2"
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            JPG, JPEG, PNG, WEBP. Maximum 2MB per image.
                        </p>

                    </div>

                    <!-- New Image Preview -->

                    <div
                        v-if="previewImages.length"
                        class="grid grid-cols-2 gap-4 sm:grid-cols-4"
                    >

                        <div
                            v-for="image in previewImages"
                            :key="image.name"
                            class="rounded-lg border p-2"
                        >

                            <img
                                :src="image.url"
                                class="h-28 w-full rounded-lg object-cover"
                            />

                            <div
                                class="mt-1 truncate text-xs text-gray-500"
                            >
                                {{ image.name }}
                            </div>

                        </div>

                    </div>

                    <!-- Submit -->

                    <div class="flex gap-3">

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Updating...'
                                    : 'Update Product'
                            }}
                        </button>

                        <Link
                            :href="route('product.index')"
                            class="rounded-lg bg-gray-500 px-6 py-3 text-white hover:bg-gray-600"
                        >
                            Cancel
                        </Link>

                    </div>

                </form>

            </div>

        </div>

    </div>
</template>
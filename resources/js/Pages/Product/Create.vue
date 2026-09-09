<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    details: '',
    price: '',
    images: []
})

const previews = ref([])

/*
|--------------------------------------------------------------------------
| Image Selection
|--------------------------------------------------------------------------
*/

const handleFiles = (event) => {
    const files = Array.from(event.target.files)

    form.images = files

    previews.value = files.map(file => ({
        name: file.name,
        url: URL.createObjectURL(file)
    }))
}

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {
    form.post(
        route('product.store'),
        {
            forceFormData: true
        }
    )
}
</script>

<template>

    <Head title="Create Product" />

    <div class="min-h-screen bg-gray-100 p-6">

        <div class="mx-auto max-w-5xl">

            <div class="rounded-xl bg-white p-6 shadow-lg">

                <!-- Header -->

                <div
                    class="mb-6 flex items-center justify-between"
                >

                    <div>

                        <h1 class="text-2xl font-bold text-gray-800">
                            ➕ Create Product
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Add product information and multiple images.
                        </p>

                    </div>

                    <Link
                        :href="route('product.index')"
                        class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
                    >
                        ← Back
                    </Link>

                </div>

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
                            placeholder="Enter product name"
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
                            placeholder="Enter product details"
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
                            placeholder="Enter price"
                            class="w-full rounded-lg border-gray-300"
                        />

                        <div
                            v-if="form.errors.price"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.price }}
                        </div>

                    </div>

                    <!-- Images -->

                    <div>

                        <label
                            class="mb-2 block font-medium text-gray-700"
                        >
                            Product Images
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

                    <!-- Preview -->

                    <div
                        v-if="previews.length"
                        class="grid grid-cols-2 gap-4 sm:grid-cols-4"
                    >

                        <div
                            v-for="preview in previews"
                            :key="preview.name"
                            class="rounded-lg border p-2"
                        >

                            <img
                                :src="preview.url"
                                class="h-32 w-full rounded-lg object-cover"
                            />

                            <p
                                class="mt-1 truncate text-xs text-gray-500"
                            >
                                {{ preview.name }}
                            </p>

                        </div>

                    </div>

                    <!-- Buttons -->

                    <div class="flex gap-3">

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Saving...'
                                    : 'Save Product'
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
<script setup>
import { ref, watch, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const visible = ref(false)
const message = ref('')
const type = ref('success')
let timeoutId = null

const flash = computed(() => page.props.flash || {})

watch(
    () => [flash.value.success, flash.value.error],
    ([newSuccess, newError]) => {
        if (newSuccess) {
            type.value = 'success'
            message.value = newSuccess
            showToast()
        } else if (newError) {
            type.value = 'error'
            message.value = newError
            showToast()
        }
    },
    { immediate: true }
)

function showToast() {
    if (!message.value) return
    visible.value = true
    if (timeoutId) clearTimeout(timeoutId)
    timeoutId = setTimeout(() => {
        dismiss()
    }, 4500)
}

function dismiss() {
    visible.value = false
    if (timeoutId) clearTimeout(timeoutId)
}
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="visible && message"
            class="fixed bottom-5 right-5 z-50 max-w-md w-full shadow-2xl rounded-2xl p-4 flex items-center gap-3 border text-sm font-medium backdrop-blur-sm"
            :class="[
                type === 'success'
                    ? 'bg-emerald-50/95 border-emerald-300 text-emerald-900 shadow-emerald-100'
                    : 'bg-rose-50/95 border-rose-300 text-rose-900 shadow-rose-100'
            ]"
        >
            <div
                class="flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center font-bold text-base"
                :class="[
                    type === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'
                ]"
            >
                <span v-if="type === 'success'">✓</span>
                <span v-else>✕</span>
            </div>

            <div class="flex-1 text-sm leading-snug">
                <p class="font-bold text-xs uppercase tracking-wider opacity-70" v-if="type === 'success'">Success</p>
                <p class="font-bold text-xs uppercase tracking-wider opacity-70" v-else>Notice / Error</p>
                <p class="mt-0.5 font-medium">{{ message }}</p>
            </div>

            <button
                @click="dismiss"
                type="button"
                class="flex-shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-black/5 transition"
                title="Dismiss"
            >
                ✕
            </button>
        </div>
    </transition>
</template>

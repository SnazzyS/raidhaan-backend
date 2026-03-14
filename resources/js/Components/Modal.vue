<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/40" @click="emit('close')" />

                <div class="relative w-full max-w-md rounded-xl border border-slate-200 bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h3 class="text-base font-semibold text-slate-900">{{ title }}</h3>
                        <button class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="emit('close')">✕</button>
                    </div>
                    <div class="p-5">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

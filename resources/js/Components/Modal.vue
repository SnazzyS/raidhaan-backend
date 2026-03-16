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
                <div class="fixed inset-0 bg-slate-950/30 backdrop-blur-sm" @click="emit('close')" />

                <div class="relative w-full max-w-md rounded-xl border border-slate-200/90 bg-white shadow-[0_24px_64px_rgba(15,23,42,0.14)]">
                    <div class="flex items-center justify-between border-b border-slate-200/80 bg-[#fbfcfc] px-5 py-4">
                        <h3 class="text-base font-semibold text-slate-900">{{ title }}</h3>
                        <button class="rounded-md p-1 text-slate-400 transition hover:bg-teal-50 hover:text-teal-700" @click="emit('close')">✕</button>
                    </div>
                    <div class="p-5">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

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
                <div class="fixed inset-0 bg-gray-900/50" @click="emit('close')" />

                <div class="relative w-full max-w-md rounded-lg border border-gray-200 bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 class="text-sm font-medium text-gray-900">{{ title }}</h3>
                        <button class="text-gray-400 hover:text-gray-600" @click="emit('close')">✕</button>
                    </div>
                    <div class="p-4">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

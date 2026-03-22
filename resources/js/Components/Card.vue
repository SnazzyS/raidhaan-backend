<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div
            v-if="title || $slots.header || $slots.actions"
            class="flex flex-col gap-3 border-b border-slate-100 bg-white px-5 py-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <div>
                <slot name="header">
                    <h3 class="text-base font-semibold tracking-tight text-slate-900">{{ title }}</h3>
                    <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
                </slot>
            </div>
            <div>
                <slot name="actions" />
            </div>
        </div>
        <div class="p-5">
            <slot />
        </div>
    </section>
</template>

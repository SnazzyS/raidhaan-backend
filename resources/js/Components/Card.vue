<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <section class="card-surface overflow-hidden rounded-2xl">
        <div
            v-if="title || $slots.header || $slots.actions"
            class="flex flex-col gap-3 border-b border-emerald-100/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <slot name="header">
                    <h3 class="text-base font-semibold text-slate-900">{{ title }}</h3>
                </slot>
            </div>
            <div>
                <slot name="actions" />
            </div>
        </div>
        <div class="p-4">
            <slot />
        </div>
    </section>
</template>

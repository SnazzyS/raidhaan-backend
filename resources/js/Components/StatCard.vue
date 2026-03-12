<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    value: {
        type: [String, Number],
        default: '',
    },
});
</script>

<template>
    <div class="card-surface rounded-2xl p-4 page-reveal">
        <p class="text-sm font-medium text-slate-500">{{ title }}</p>
        <p class="mt-1 text-2xl font-semibold text-slate-900">{{ value }}</p>
    </div>
</template>

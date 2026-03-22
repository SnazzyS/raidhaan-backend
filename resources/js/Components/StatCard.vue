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
    <div class="rounded-xl border border-slate-200 bg-white p-5">
        <p class="text-sm font-medium text-slate-500">{{ title }}</p>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ value }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
    },
    status: {
        type: String,
        default: '',
    },
});

const variants = {
    default: 'border-slate-200 bg-slate-50 text-slate-700',
    primary: 'border-slate-200 bg-slate-100 text-slate-700',
    success: 'border-emerald-200 bg-emerald-50 text-emerald-700',
    warning: 'border-amber-200 bg-amber-50 text-amber-700',
    danger: 'border-rose-200 bg-rose-50 text-rose-700',
    info: 'border-sky-200 bg-sky-50 text-sky-700',
};

const statusMap = {
    pending: { variant: 'warning', label: 'Pending' },
    printed: { variant: 'info', label: 'Printed' },
    completed: { variant: 'success', label: 'Completed' },
    cancelled: { variant: 'danger', label: 'Cancelled' },
    voided: { variant: 'danger', label: 'Voided' },
    available: { variant: 'success', label: 'Available' },
    processing: { variant: 'info', label: 'Processing' },
};

const resolved = computed(() => {
    if (props.status && statusMap[props.status]) {
        return statusMap[props.status];
    }

    return {
        variant: props.variant,
        label: null,
    };
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium"
        :class="variants[resolved.variant] || variants.default"
    >
        <slot>{{ resolved.label || status || '' }}</slot>
    </span>
</template>

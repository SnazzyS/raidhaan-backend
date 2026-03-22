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
    default: 'bg-slate-100 text-slate-700',
    primary: 'bg-indigo-50 text-indigo-700',
    success: 'bg-emerald-50 text-emerald-700',
    warning: 'bg-amber-50 text-amber-800',
    danger: 'bg-rose-50 text-rose-700',
    info: 'bg-sky-50 text-sky-700',
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

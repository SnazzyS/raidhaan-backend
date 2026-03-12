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
    default: 'bg-gray-100 text-gray-700',
    primary: 'bg-gray-100 text-gray-700',
    success: 'bg-emerald-50 text-emerald-700',
    warning: 'bg-amber-50 text-amber-700',
    danger: 'bg-rose-50 text-rose-700',
    info: 'bg-sky-50 text-sky-700',
};

const statusMap = {
    pending: { variant: 'warning', label: 'Pending' },
    completed: { variant: 'success', label: 'Completed' },
    cancelled: { variant: 'danger', label: 'Cancelled' },
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
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="variants[resolved.variant] || variants.default"
    >
        <slot>{{ resolved.label || status || '' }}</slot>
    </span>
</template>

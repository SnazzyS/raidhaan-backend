<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    type: { type: String, default: 'button' },
});

const variantClasses = {
    primary: 'bg-teal-600 text-white shadow-sm hover:bg-teal-700 focus-visible:outline-teal-600',
    secondary: 'border border-slate-200 bg-[#f7faf9] text-slate-700 shadow-sm hover:border-teal-200 hover:bg-teal-50 hover:text-teal-800 focus-visible:outline-teal-500',
    success: 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus-visible:outline-emerald-600',
    danger: 'bg-rose-600 text-white shadow-sm hover:bg-rose-700 focus-visible:outline-rose-600',
    ghost: 'bg-transparent text-slate-600 hover:bg-teal-50 hover:text-teal-800 focus-visible:outline-teal-500',
};

const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-2.5 text-sm',
};

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-lg border border-transparent font-medium',
    'transition-colors duration-150 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
    'disabled:cursor-not-allowed disabled:opacity-50',
    variantClasses[props.variant] || variantClasses.primary,
    sizeClasses[props.size] || sizeClasses.md,
]);
</script>

<template>
    <button :type="type" :disabled="disabled" :class="classes" v-bind="$attrs">
        <slot />
    </button>
</template>

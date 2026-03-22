<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    type: { type: String, default: 'button' },
});

const variantClasses = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:outline-indigo-600',
    secondary: 'bg-slate-100 text-slate-800 hover:bg-slate-200 focus-visible:outline-slate-300',
    success: 'bg-emerald-600 text-white hover:bg-emerald-700 focus-visible:outline-emerald-600',
    danger: 'bg-rose-600 text-white hover:bg-rose-700 focus-visible:outline-rose-600',
    ghost: 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-slate-200',
};

const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-2.5 text-base',
};

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-lg font-medium',
    'transition-colors duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
    'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-inherit',
    variantClasses[props.variant] || variantClasses.primary,
    sizeClasses[props.size] || sizeClasses.md,
]);
</script>

<template>
    <button :type="type" :disabled="disabled" :class="classes" v-bind="$attrs">
        <slot />
    </button>
</template>

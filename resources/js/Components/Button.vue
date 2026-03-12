<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    type: { type: String, default: 'button' },
});

const variantClasses = {
    primary: 'bg-emerald-700 text-white shadow-[0_8px_20px_rgba(4,120,87,0.25)] hover:bg-emerald-800 focus-visible:outline-emerald-700',
    secondary: 'border border-emerald-200 bg-white/80 text-emerald-900 shadow-sm hover:bg-emerald-50 focus-visible:outline-emerald-300',
    success: 'bg-teal-600 text-white shadow-[0_8px_18px_rgba(13,148,136,0.22)] hover:bg-teal-700 focus-visible:outline-teal-500',
    danger: 'bg-rose-600 text-white shadow-[0_8px_18px_rgba(225,29,72,0.24)] hover:bg-rose-700 focus-visible:outline-rose-500',
    ghost: 'text-slate-600 hover:bg-white/70 hover:text-slate-900 focus-visible:outline-slate-300',
};

const sizeClasses = {
    sm: 'px-2.5 py-1.5 text-xs',
    md: 'px-3.5 py-2 text-sm',
    lg: 'px-4 py-2.5 text-base',
};

const classes = computed(() => [
    'inline-flex items-center justify-center gap-2 rounded-xl border border-transparent font-semibold',
    'transition-all duration-200 ease-out hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2',
    'disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0',
    variantClasses[props.variant] || variantClasses.primary,
    sizeClasses[props.size] || sizeClasses.md,
]);
</script>

<template>
    <button :type="type" :disabled="disabled" :class="classes" v-bind="$attrs">
        <slot />
    </button>
</template>

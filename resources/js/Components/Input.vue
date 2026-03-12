<script setup>
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
        </label>
        <input
            :value="modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            class="w-full rounded-xl border border-emerald-200 bg-white/80 px-3 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100"
            :class="error ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100' : ''"
            v-bind="$attrs"
        >
        <p v-if="error" class="mt-1 text-xs text-rose-600">{{ error }}</p>
    </div>
</template>

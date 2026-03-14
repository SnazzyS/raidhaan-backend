<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    gst_percentage: Number(props.settings.gst_percentage || 0),
    gst_is_inclusive: Boolean(props.settings.gst_is_inclusive),
    service_charge_percentage: Number(props.settings.service_charge_percentage || 0),
    service_charge_is_inclusive: Boolean(props.settings.service_charge_is_inclusive),
});

const submit = () => {
    form.put('/settings');
};
</script>

<template>
    <AppLayout title="Settings">
        <Head title="Settings" />

        <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <Card title="Charge Settings" description="These values apply automatically to new deliveries and table bills.">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <Input
                            v-model="form.gst_percentage"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            label="GST %"
                            :error="form.errors.gst_percentage"
                        />
                        <Input
                            v-model="form.service_charge_percentage"
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            label="Service Charge %"
                            :error="form.errors.service_charge_percentage"
                        />
                    </div>

                    <div class="mt-4 space-y-4">
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <input v-model="form.gst_is_inclusive" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300">
                            <span>GST is already included in menu prices.</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                            <input v-model="form.service_charge_is_inclusive" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-300">
                            <span>Service charge is already included in menu prices.</span>
                        </label>
                    </div>
                </Card>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                <Card title="Current Rules" description="Use this panel to confirm what staff will see on every bill.">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">GST</span>
                            <span class="font-medium text-slate-900">{{ Number(form.gst_percentage || 0).toFixed(2) }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">GST pricing</span>
                            <span class="font-medium text-slate-900">{{ form.gst_is_inclusive ? 'Included in prices' : 'Added on bill' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Service</span>
                            <span class="font-medium text-slate-900">{{ Number(form.service_charge_percentage || 0).toFixed(2) }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Service pricing</span>
                            <span class="font-medium text-slate-900">{{ form.service_charge_is_inclusive ? 'Included in prices' : 'Added on bill' }}</span>
                        </div>
                    </div>

                    <Button type="submit" class="mt-5 w-full" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Settings' }}
                    </Button>
                </Card>
            </div>
        </form>
    </AppLayout>
</template>

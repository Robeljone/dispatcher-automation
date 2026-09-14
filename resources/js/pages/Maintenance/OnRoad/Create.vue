<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/OnRoadMaintenanceController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/maintenance/on-road';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type DriverOption, type VehicleOption } from './FormFields.vue';

setLayoutProps({
    title: 'Add on-road maintenance',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    vehicles: VehicleOption[];
    drivers: DriverOption[];
}>();
</script>

<template>
    <Head title="Add on-road maintenance" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <Form v-bind="store.form()" class="flex flex-col gap-5" #default="{ errors, processing }">
            <FormFields :errors="errors" :vehicles="vehicles" :drivers="drivers" />
            <div class="flex items-center justify-end gap-3">
                <Link :href="index.url()" class="text-sm text-slate-500">Cancel</Link>
                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Create record' }}
                </button>
            </div>
        </Form>
    </div>
</template>

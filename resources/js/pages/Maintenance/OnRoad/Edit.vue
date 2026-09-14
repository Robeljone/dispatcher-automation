<script setup lang="ts">
import { destroy, update } from '@/actions/App/Http/Controllers/OnRoadMaintenanceController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/maintenance/on-road';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type DriverOption, type OnRoadForm, type VehicleOption } from './FormFields.vue';

setLayoutProps({
    title: 'Edit on-road maintenance',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    record: OnRoadForm & { id: number };
    vehicles: VehicleOption[];
    drivers: DriverOption[];
}>();
</script>

<template>
    <Head title="Edit on-road maintenance" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <Form v-bind="update.form(props.record.id)" class="flex flex-col gap-5" #default="{ errors, processing }">
            <FormFields :errors="errors" :vehicles="vehicles" :drivers="drivers" :record="record" />
            <div class="flex items-center justify-between gap-3">
                <Link
                    :href="destroy.url(record.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete record
                </Link>
                <div class="flex items-center gap-3">
                    <Link :href="index.url()" class="text-sm text-slate-500">Cancel</Link>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                    >
                        {{ processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </div>
        </Form>
    </div>
</template>

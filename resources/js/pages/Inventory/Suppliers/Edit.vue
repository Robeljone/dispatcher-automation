<script setup lang="ts">
import { destroy, update } from '@/actions/App/Http/Controllers/SupplierController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/inventory/suppliers';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type StatusOption, type SupplierForm } from './FormFields.vue';

setLayoutProps({
    title: 'Edit supplier',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    supplier: SupplierForm & { id: number };
    statuses: StatusOption[];
}>();
</script>

<template>
    <Head title="Edit supplier" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <Form v-bind="update.form(props.supplier.id)" class="flex flex-col gap-5" #default="{ errors, processing }">
            <FormFields :errors="errors" :supplier="supplier" :statuses="statuses" />
            <div class="flex items-center justify-between gap-3">
                <Link
                    :href="destroy.url(supplier.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete supplier
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

<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/PurchaseController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as purchasesIndex } from '@/routes/inventory/purchases';
import { index as suppliersIndex } from '@/routes/inventory/suppliers';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type CatalogOption } from './FormFields.vue';

setLayoutProps({
    title: 'Add purchase',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    items: CatalogOption[];
    suppliers: CatalogOption[];
}>();
</script>

<template>
    <Head title="Add purchase" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <p class="mb-5 text-sm text-slate-500">
            Choose a supplier from
            <Link :href="suppliersIndex.url()" class="font-medium text-indigo-600">Suppliers</Link>
            if the list is empty.
        </p>
        <Form
            v-bind="store.form()"
            enctype="multipart/form-data"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <FormFields :errors="errors" :items="items" :suppliers="suppliers" />
            <div class="flex items-center justify-end gap-3">
                <Link :href="purchasesIndex.url()" class="text-sm text-slate-500">Cancel</Link>
                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Create purchase' }}
                </button>
            </div>
        </Form>
    </div>
</template>

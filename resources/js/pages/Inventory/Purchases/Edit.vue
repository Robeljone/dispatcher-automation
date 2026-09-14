<script setup lang="ts">
import { destroy, download, update } from '@/actions/App/Http/Controllers/PurchaseController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/inventory/purchases';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type CatalogOption, type PurchaseForm } from './FormFields.vue';

setLayoutProps({
    title: 'Edit purchase',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    purchase: PurchaseForm & { id: number; has_attachment: boolean; can_edit: boolean };
    items: CatalogOption[];
    suppliers: CatalogOption[];
}>();
</script>

<template>
    <Head title="Edit purchase" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <p v-if="!purchase.can_edit" class="mb-4 text-sm text-slate-500">
            Approved or rejected purchases can no longer be changed.
        </p>
        <Form
            v-bind="update.form(props.purchase.id)"
            enctype="multipart/form-data"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <fieldset :disabled="!purchase.can_edit" class="contents">
                <FormFields :errors="errors" :items="items" :suppliers="suppliers" :purchase="purchase" />
            </fieldset>
            <a
                v-if="purchase.has_attachment"
                :href="download.url(purchase.id)"
                class="text-sm font-medium text-indigo-600"
            >
                Download attachment
            </a>
            <div class="flex items-center justify-between gap-3">
                <Link
                    v-if="purchase.can_edit"
                    :href="destroy.url(purchase.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete purchase
                </Link>
                <div class="ml-auto flex items-center gap-3">
                    <Link :href="index.url()" class="text-sm text-slate-500">Cancel</Link>
                    <button
                        v-if="purchase.can_edit"
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

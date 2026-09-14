<script setup lang="ts">
import { destroy, update } from '@/actions/App/Http/Controllers/ItemRequestController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/inventory/item-requests';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type ItemRequestForm, type SelectOption, type StockOption } from './FormFields.vue';

setLayoutProps({
    title: 'Edit item request',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    itemRequest: ItemRequestForm & { id: number; can_edit: boolean };
    stocks: StockOption[];
    vehicles: SelectOption[];
    drivers: SelectOption[];
}>();
</script>

<template>
    <Head title="Edit item request" />
    <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <p v-if="!itemRequest.can_edit" class="mb-4 text-sm text-slate-500">
            Approved or rejected requests can no longer be changed.
        </p>
        <Form v-bind="update.form(props.itemRequest.id)" class="flex flex-col gap-5" #default="{ errors, processing }">
            <fieldset :disabled="!itemRequest.can_edit" class="contents">
                <FormFields :errors="errors" :stocks="stocks" :vehicles="vehicles" :drivers="drivers" :item-request="itemRequest" />
            </fieldset>
            <div class="flex items-center justify-between gap-3">
                <Link
                    v-if="itemRequest.can_edit"
                    :href="destroy.url(itemRequest.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete request
                </Link>
                <div class="ml-auto flex items-center gap-3">
                    <Link :href="index.url()" class="text-sm text-slate-500">Cancel</Link>
                    <button
                        v-if="itemRequest.can_edit"
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

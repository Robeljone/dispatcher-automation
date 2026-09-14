<script setup lang="ts">
import { destroy, update } from '@/actions/App/Http/Controllers/ItemController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/inventory/items';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';
import FormFields, { type ItemForm, type ItemStatusOption } from './FormFields.vue';

setLayoutProps({
    title: 'Edit item',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    item: ItemForm & { id: number };
    statuses: ItemStatusOption[];
}>();
</script>

<template>
    <Head title="Edit item" />
    <div
        class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <Form
            v-bind="update.form(props.item.id)"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <FormFields :errors="errors" :item="item" :statuses="statuses" />
            <div class="flex items-center justify-between gap-3">
                <Link
                    :href="destroy.url(item.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete item
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

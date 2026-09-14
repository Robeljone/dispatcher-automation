<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/inventory/item-requests';
import type { Paginated } from '@/types';
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive } from 'vue';

setLayoutProps({
    title: 'Item requests',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    itemRequests: Paginated<{
        id: number;
        item_name: string;
        quantity: number;
        price: string;
        total: string;
        vehicle: string;
        driver: string;
        date_requested: string;
        status: string;
        status_label: string;
    }>;
    filters: {
        search: string;
    };
}>();

const filters = reactive({
    search: props.filters.search,
});

function applyFilters(): void {
    router.get(index.url(), { search: filters.search }, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Item requests" />
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">Request parts for a vehicle. Approvals deduct quantity from store stock.</p>
            <Link
                :href="create.url()"
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                Add request
            </Link>
        </div>

        <form
            class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="applyFilters"
        >
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search item, plate, or driver"
                class="grow rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            />
            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white dark:bg-slate-100 dark:text-slate-900"
            >
                Search
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-medium">Item</th>
                            <th class="px-4 py-3 font-medium">Vehicle</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium">Driver</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="itemRequest in itemRequests.data"
                            :key="itemRequest.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">{{ itemRequest.item_name }}</td>
                            <td class="px-4 py-3">{{ itemRequest.vehicle }}</td>
                            <td class="px-4 py-3">{{ itemRequest.quantity }}</td>
                            <td class="px-4 py-3">{{ itemRequest.total }}</td>
                            <td class="px-4 py-3">{{ itemRequest.driver }}</td>
                            <td class="px-4 py-3">{{ itemRequest.status_label }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="edit.url(itemRequest.id)" class="font-medium text-indigo-600">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 px-4 py-3 dark:border-slate-800">
                <p class="text-sm text-slate-500">
                    {{ itemRequests.from }}-{{ itemRequests.to }} of {{ itemRequests.total }}
                </p>
                <Pagination :links="itemRequests.links" />
            </div>
        </div>
    </div>
</template>

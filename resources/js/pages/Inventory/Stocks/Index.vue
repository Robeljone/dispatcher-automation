<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/inventory/stocks';
import type { Paginated } from '@/types';
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive } from 'vue';

setLayoutProps({
    title: 'Store',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    stocks: Paginated<{
        id: number;
        item_name: string;
        item_brand: string;
        stock_quantity: number;
        min_quantity: number;
        buy_price: string;
        is_low: boolean;
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
    <Head title="Store" />
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">Track on-hand quantities, reorder points, and buy prices.</p>
            <Link
                :href="create.url()"
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                Add stock
            </Link>
        </div>

        <form
            class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="applyFilters"
        >
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search item name or brand"
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
                            <th class="px-4 py-3 font-medium">Brand</th>
                            <th class="px-4 py-3 font-medium">Quantity</th>
                            <th class="px-4 py-3 font-medium">Minimum</th>
                            <th class="px-4 py-3 font-medium">Buy price</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="stock in stocks.data"
                            :key="stock.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">{{ stock.item_name }}</td>
                            <td class="px-4 py-3">{{ stock.item_brand }}</td>
                            <td class="px-4 py-3">
                                {{ stock.stock_quantity }}
                                <span v-if="stock.is_low" class="ml-2 text-xs font-medium text-rose-600">Low</span>
                            </td>
                            <td class="px-4 py-3">{{ stock.min_quantity }}</td>
                            <td class="px-4 py-3">{{ stock.buy_price }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="edit.url(stock.id)" class="font-medium text-indigo-600">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 px-4 py-3 dark:border-slate-800">
                <p class="text-sm text-slate-500">{{ stocks.from }}-{{ stocks.to }} of {{ stocks.total }}</p>
                <Pagination :links="stocks.links" />
            </div>
        </div>
    </div>
</template>

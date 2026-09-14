<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/inventory/suppliers';
import type { Paginated } from '@/types';
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive } from 'vue';

setLayoutProps({
    title: 'Suppliers',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    suppliers: Paginated<{
        id: number;
        name: string;
        phone: string;
        email: string;
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
    <Head title="Suppliers" />
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">Manage suppliers used on inventory purchases.</p>
            <Link
                :href="create.url()"
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                Add supplier
            </Link>
        </div>

        <form
            class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="applyFilters"
        >
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search name, email, or phone"
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
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="supplier in suppliers.data"
                            :key="supplier.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">{{ supplier.name }}</td>
                            <td class="px-4 py-3">{{ supplier.phone }}</td>
                            <td class="px-4 py-3">{{ supplier.email }}</td>
                            <td class="px-4 py-3">{{ supplier.status_label }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="edit.url(supplier.id)" class="font-medium text-indigo-600">Edit</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 px-4 py-3 dark:border-slate-800">
                <p class="text-sm text-slate-500">{{ suppliers.from }}-{{ suppliers.to }} of {{ suppliers.total }}</p>
                <Pagination :links="suppliers.links" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Paginated } from '@/types';
import { create, edit, index } from '@/routes/users';
import { Head, Link, router, setLayoutProps } from '@inertiajs/vue3';
import { reactive } from 'vue';

setLayoutProps({
    title: 'Users',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    users: Paginated<{
        id: number;
        name: string;
        email: string;
        role: string;
        role_label: string;
        is_active: boolean;
        last_login_at: string | null;
        created_at: string | null;
    }>;
    filters: {
        search: string;
        role: string;
        status: string;
    };
}>();

const filters = reactive({
    search: props.filters.search,
    role: props.filters.role,
    status: props.filters.status,
});

function applyFilters(): void {
    router.get(
        index.url(),
        {
            search: filters.search,
            role: filters.role,
            status: filters.status,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}
</script>

<template>
    <Head title="Users" />
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500">
                Search, filter, and manage people who can access the system.
            </p>
            <Link
                :href="create.url()"
                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                Add user
            </Link>
        </div>

        <form
            class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:grid-cols-4 dark:border-slate-800 dark:bg-slate-900"
            @submit.prevent="applyFilters"
        >
            <input
                v-model="filters.search"
                type="search"
                placeholder="Search name or email"
                class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
            />
            <select
                v-model="filters.role"
                class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
                @change="applyFilters"
            >
                <option value="">All roles</option>
                <option value="admin">Administrator</option>
                <option value="user">User</option>
            </select>
            <select
                v-model="filters.status"
                class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-950"
                @change="applyFilters"
            >
                <option value="">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button
                type="submit"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white dark:bg-slate-100 dark:text-slate-900"
            >
                Filter
            </button>
        </form>

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Last login</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ user.name }}
                            </td>
                            <td class="px-4 py-3">{{ user.email }}</td>
                            <td class="px-4 py-3">{{ user.role_label }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-1 text-xs font-medium"
                                    :class="
                                        user.is_active
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                                            : 'bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300'
                                    "
                                >
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">
                                {{ user.last_login_at ?? 'Never' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="edit.url(user.id)"
                                    class="font-medium text-indigo-600"
                                >
                                    Edit
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                class="flex items-center justify-between border-t border-slate-100 px-4 py-3 dark:border-slate-800"
            >
                <p class="text-sm text-slate-500">
                    {{ users.from }}-{{ users.to }} of {{ users.total }}
                </p>
                <Pagination :links="users.links" />
            </div>
        </div>
    </div>
</template>

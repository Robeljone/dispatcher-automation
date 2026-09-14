<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index as usersIndex } from '@/routes/users';
import { Head, Link, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Dashboard',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    stats: {
        users: number;
        admins: number;
        active: number;
    };
    recentUsers: {
        id: number;
        name: string;
        email: string;
        role: string;
        is_active: boolean;
        created_at: string;
    }[];
    recentActivities: {
        id: number;
        action: string;
        description: string;
        user: string;
        created_at: string;
    }[];
}>();
</script>

<template>
    <Head title="Dashboard" />
    <div class="flex flex-col gap-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <p class="text-sm text-slate-500">Users</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.users }}</p>
            </article>
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <p class="text-sm text-slate-500">Administrators</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.admins }}</p>
            </article>
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <p class="text-sm text-slate-500">Active accounts</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.active }}</p>
            </article>
        </section>

        <section>
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-semibold">Recent activity</h2>
                </div>
                <ul v-if="recentActivities.length" class="flex flex-col gap-3">
                    <li
                        v-for="activity in recentActivities"
                        :key="activity.id"
                        class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60"
                    >
                        <p class="text-sm font-medium">{{ activity.action }}</p>
                        <p class="text-sm text-slate-500">
                            {{ activity.user }} · {{ activity.description }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ activity.created_at }}
                        </p>
                    </li>
                </ul>
                <p v-else class="text-sm text-slate-500">No activity yet.</p>
            </article>
        </section>

        <article
            v-if="recentUsers.length"
            class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Newest users</h2>
                <Link
                    :href="usersIndex.url()"
                    class="text-sm font-medium text-indigo-600"
                >
                    Manage users
                </Link>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-slate-500">
                        <tr>
                            <th class="py-2 font-medium">Name</th>
                            <th class="py-2 font-medium">Email</th>
                            <th class="py-2 font-medium">Role</th>
                            <th class="py-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in recentUsers"
                            :key="user.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="py-3">{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td class="capitalize">{{ user.role }}</td>
                            <td>
                                {{ user.is_active ? 'Active' : 'Inactive' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</template>

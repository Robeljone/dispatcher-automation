<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Paginated } from '@/types';
import { Head, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Activity',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    activities: Paginated<{
        id: number;
        action: string;
        description: string;
        user: string;
        email: string | null;
        ip_address: string | null;
        created_at: string | null;
    }>;
}>();
</script>

<template>
    <Head title="Activity" />
    <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Event</th>
                        <th class="px-4 py-3 font-medium">Person</th>
                        <th class="px-4 py-3 font-medium">Details</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                        <th class="px-4 py-3 font-medium">When</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="activity in activities.data"
                        :key="activity.id"
                        class="border-t border-slate-100 dark:border-slate-800"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ activity.action }}
                        </td>
                        <td class="px-4 py-3">
                            {{ activity.user }}
                            <span
                                v-if="activity.email"
                                class="block text-xs text-slate-500"
                            >
                                {{ activity.email }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ activity.description }}</td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ activity.ip_address ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            {{ activity.created_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-4 py-3 dark:border-slate-800">
            <Pagination :links="activities.links" />
        </div>
    </div>
</template>

<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
import { dashboard, login } from '@/routes';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineOptions({
    layout: GuestLayout,
});

const page = usePage();
</script>

<template>
    <Head title="Welcome" />
    <div class="flex flex-col gap-8">
        <div class="max-w-2xl">
            <h1 class="text-4xl font-semibold tracking-tight">
                Run your system from one admin panel.
            </h1>
            <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">
                {{ page.props.name }} covers operations, finance, maintenance,
                inventory, and settings with user management and activity
                history.
            </p>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <h2 class="font-semibold">Modules</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Work from Operations, Finance, Maintenance, and Inventory
                    as dedicated areas of the panel.
                </p>
            </article>
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <h2 class="font-semibold">Users</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Create staff accounts, assign admin or user roles, and
                    deactivate access without deleting history.
                </p>
            </article>
            <article
                class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"
            >
                <h2 class="font-semibold">Activity</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Every sign-in, profile change, and user update is recorded
                    for a simple audit trail.
                </p>
            </article>
        </div>
        <div>
            <Link
                :href="page.props.auth.user ? dashboard.url() : login.url()"
                class="inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500"
            >
                {{ page.props.auth.user ? 'Open the panel' : 'Sign in to continue' }}
            </Link>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Finance',
});

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <Head title="Finance" />
    <section
        class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <h2 class="text-lg font-semibold">Finance</h2>
        <p class="mt-2 text-sm text-slate-500">
            Billing, costs, and financial records will live in this module.
        </p>
    </section>
</template>

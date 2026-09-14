<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Distribution',
});

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <Head title="Distribution" />
    <section
        class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <h2 class="text-lg font-semibold">Distribution</h2>
        <p class="mt-2 text-sm text-slate-500">
            Distribution work will live in this operations area.
        </p>
    </section>
</template>

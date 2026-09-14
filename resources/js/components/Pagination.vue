<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: { url: string | null; label: string; active: boolean }[];
}>();
</script>

<template>
    <nav class="flex flex-wrap items-center justify-end gap-1">
        <template v-for="(link, index) in links" :key="index">
            <span
                v-if="!link.url"
                class="rounded-md px-3 py-1.5 text-sm text-slate-400"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded-md px-3 py-1.5 text-sm"
                :class="
                    link.active
                        ? 'bg-indigo-600 text-white'
                        : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                "
                v-html="link.label"
            />
        </template>
    </nav>
</template>

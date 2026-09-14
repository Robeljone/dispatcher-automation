<script setup lang="ts">
import { dashboard } from '@/routes';
import { login } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        title?: string;
    }>(),
    {
        title: 'Welcome',
    },
);

const page = usePage();
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    >
        <header
            class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4 dark:border-slate-800 dark:bg-slate-900"
        >
            <Link href="/" class="text-lg font-semibold tracking-tight">
                {{ page.props.name }}
            </Link>
            <nav class="flex items-center gap-3 text-sm">
                <Link
                    v-if="page.props.auth.user"
                    :href="dashboard.url()"
                    class="rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-500"
                >
                    Open panel
                </Link>
                <Link
                    v-else
                    :href="login.url()"
                    class="rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-500"
                >
                    Sign in
                </Link>
            </nav>
        </header>
        <main class="mx-auto w-full max-w-5xl px-6 py-12">
            <p
                class="text-sm font-medium uppercase tracking-widest text-indigo-600"
            >
                {{ title }}
            </p>
            <slot />
        </main>
    </div>
</template>

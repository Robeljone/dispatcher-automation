<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/ProfileController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Profile',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    user: {
        name: string;
        email: string;
    };
}>();
</script>

<template>
    <Head title="Profile" />
    <div
        class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <Form
            v-bind="update.form()"
            class="flex flex-col gap-5"
            #default="{ errors, processing, recentlySuccessful }"
        >
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        required
                        :value="user.name"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                    <p v-if="errors.name" class="text-sm text-rose-600">
                        {{ errors.name }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        :value="user.email"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                    <p v-if="errors.email" class="text-sm text-rose-600">
                        {{ errors.email }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="password">
                        New password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                    <p v-if="errors.password" class="text-sm text-rose-600">
                        {{ errors.password }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="password_confirmation">
                        Confirm password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                </div>
            </div>
            <div class="flex items-center justify-end gap-3">
                <p v-if="recentlySuccessful" class="text-sm text-emerald-600">
                    Saved.
                </p>
                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Save profile' }}
                </button>
            </div>
        </Form>
    </div>
</template>

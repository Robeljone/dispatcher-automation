<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/UserController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/users';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Add user',
});

defineOptions({
    layout: AppLayout,
});

defineProps<{
    roles: { value: string; label: string }[];
}>();
</script>

<template>
    <Head title="Add user" />
    <div
        class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <Form
            v-bind="store.form()"
            class="flex flex-col gap-5"
            #default="{ errors, processing }"
        >
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        required
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
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                    <p v-if="errors.email" class="text-sm text-rose-600">
                        {{ errors.email }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="role">Role</label>
                    <select
                        id="role"
                        name="role"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    >
                        <option
                            v-for="role in roles"
                            :key="role.value"
                            :value="role.value"
                        >
                            {{ role.label }}
                        </option>
                    </select>
                    <p v-if="errors.role" class="text-sm text-rose-600">
                        {{ errors.role }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="password">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
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
                        required
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950"
                    />
                </div>
                <label class="flex items-center gap-2 pt-7 text-sm">
                    <input type="hidden" name="is_active" value="0" />
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        checked
                    />
                    Active account
                </label>
            </div>
            <div class="flex items-center justify-end gap-3">
                <Link :href="index.url()" class="text-sm text-slate-500">
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Create user' }}
                </button>
            </div>
        </Form>
    </div>
</template>

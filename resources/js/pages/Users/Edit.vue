<script setup lang="ts">
import { destroy, update } from '@/actions/App/Http/Controllers/UserController';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/users';
import { Form, Head, Link, setLayoutProps } from '@inertiajs/vue3';

setLayoutProps({
    title: 'Edit user',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        role: string;
        is_active: boolean;
    };
    roles: { value: string; label: string }[];
}>();
</script>

<template>
    <Head title="Edit user" />
    <div
        class="w-full rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900"
    >
        <Form
            v-bind="update.form(props.user.id)"
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
                    <label class="text-sm font-medium" for="role">Role</label>
                    <select
                        id="role"
                        name="role"
                        :value="user.role"
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
                <label class="flex items-center gap-2 pt-7 text-sm">
                    <input type="hidden" name="is_active" value="0" />
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        :checked="user.is_active"
                    />
                    Active account
                </label>
            </div>
            <div class="flex items-center justify-between gap-3">
                <Link
                    :href="destroy.url(user.id)"
                    method="delete"
                    as="button"
                    class="text-sm font-medium text-rose-600"
                >
                    Delete user
                </Link>
                <div class="flex items-center gap-3">
                    <Link :href="index.url()" class="text-sm text-slate-500">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"
                    >
                        {{ processing ? 'Saving...' : 'Save changes' }}
                    </button>
                </div>
            </div>
        </Form>
    </div>
</template>

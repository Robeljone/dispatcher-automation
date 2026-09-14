<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import { home } from '@/routes';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <Head title="Sign in" />
    <div
        class="flex min-h-screen items-center justify-center bg-white px-6 py-12"
    >
        <div class="w-full max-w-md">
            <Link
                :href="home.url()"
                class="text-lg font-semibold text-slate-900"
            >
                {{ page.props.name }}
            </Link>
            <h1 class="mt-8 text-2xl font-semibold text-slate-900">Sign in</h1>
            <p class="mt-2 text-sm text-slate-500">
                Use your work email and password to open the panel.
            </p>

            <div
                v-if="page.props.flash.error"
                class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"
            >
                {{ page.props.flash.error }}
            </div>

            <Form
                v-bind="store.form()"
                class="mt-8 flex flex-col gap-5"
                #default="{ errors, processing }"
            >
                <div class="flex flex-col gap-2">
                    <label
                        for="email"
                        class="text-sm font-medium text-slate-700"
                    >
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none ring-indigo-500 focus:ring-2"
                    />
                    <p v-if="errors.email" class="text-sm text-rose-600">
                        {{ errors.email }}
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label
                        for="password"
                        class="text-sm font-medium text-slate-700"
                    >
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none ring-indigo-500 focus:ring-2"
                    />
                    <p v-if="errors.password" class="text-sm text-rose-600">
                        {{ errors.password }}
                    </p>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded border-slate-300"
                    />
                    Remember me
                </label>

                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-60"
                >
                    {{ processing ? 'Signing in...' : 'Sign in' }}
                </button>
            </Form>
        </div>
    </div>
</template>

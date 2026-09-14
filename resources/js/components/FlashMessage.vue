<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const success = computed(() => page.props.flash.success);
const error = computed(() => page.props.flash.error);
</script>

<template>
    <div v-if="success || error" class="mb-6">
        <div
            v-if="success"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200"
        >
            {{ success }}
        </div>
        <div
            v-if="error"
            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-200"
        >
            {{ error }}
        </div>
    </div>
</template>

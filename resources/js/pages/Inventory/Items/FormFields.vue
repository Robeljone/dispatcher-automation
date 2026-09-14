<script setup lang="ts">
export type ItemForm = {
    name: string;
    brand: string;
    status: string;
};

export type ItemStatusOption = {
    value: string;
    label: string;
};

defineProps<{
    errors: Record<string, string>;
    statuses: ItemStatusOption[];
    item?: ItemForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="name">Name</label>
            <input id="name" name="name" required :value="item?.name" :class="fieldClass" />
            <p v-if="errors.name" class="text-sm text-rose-600">{{ errors.name }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="brand">Brand</label>
            <input id="brand" name="brand" required :value="item?.brand" :class="fieldClass" />
            <p v-if="errors.brand" class="text-sm text-rose-600">{{ errors.brand }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="status">Status</label>
            <select id="status" name="status" required :value="item?.status ?? 'active'" :class="fieldClass">
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                    {{ status.label }}
                </option>
            </select>
            <p v-if="errors.status" class="text-sm text-rose-600">{{ errors.status }}</p>
        </div>
    </div>
</template>

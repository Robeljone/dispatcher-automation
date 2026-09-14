<script setup lang="ts">
export type SupplierForm = {
    name: string;
    address: string;
    phone: string;
    email: string;
    status: string;
};

export type StatusOption = {
    value: string;
    label: string;
};

defineProps<{
    errors: Record<string, string>;
    statuses: StatusOption[];
    supplier?: SupplierForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="name">Name</label>
            <input id="name" name="name" required :value="supplier?.name" :class="fieldClass" />
            <p v-if="errors.name" class="text-sm text-rose-600">{{ errors.name }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="phone">Phone</label>
            <input id="phone" name="phone" required :value="supplier?.phone" :class="fieldClass" />
            <p v-if="errors.phone" class="text-sm text-rose-600">{{ errors.phone }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="email">Email</label>
            <input id="email" name="email" type="email" required :value="supplier?.email" :class="fieldClass" />
            <p v-if="errors.email" class="text-sm text-rose-600">{{ errors.email }}</p>
        </div>
        <div class="flex flex-col gap-2 sm:col-span-2">
            <label class="text-sm font-medium" for="address">Address</label>
            <input id="address" name="address" required :value="supplier?.address" :class="fieldClass" />
            <p v-if="errors.address" class="text-sm text-rose-600">{{ errors.address }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="status">Status</label>
            <select id="status" name="status" required :value="supplier?.status ?? 'active'" :class="fieldClass">
                <option v-for="status in statuses" :key="status.value" :value="status.value">
                    {{ status.label }}
                </option>
            </select>
            <p v-if="errors.status" class="text-sm text-rose-600">{{ errors.status }}</p>
        </div>
    </div>
</template>

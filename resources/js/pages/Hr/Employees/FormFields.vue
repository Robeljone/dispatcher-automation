<script setup lang="ts">
export type EmployeeForm = {
    full_name: string;
    phone: string;
    address: string;
    education: string | null;
    position: string;
    salary: number | string;
};

defineProps<{
    errors: Record<string, string>;
    employee?: EmployeeForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="full_name">Full name</label>
            <input id="full_name" name="full_name" required :value="employee?.full_name" :class="fieldClass" />
            <p v-if="errors.full_name" class="text-sm text-rose-600">{{ errors.full_name }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="phone">Phone</label>
            <input id="phone" name="phone" required :value="employee?.phone" :class="fieldClass" />
            <p v-if="errors.phone" class="text-sm text-rose-600">{{ errors.phone }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="position">Position</label>
            <input id="position" name="position" required :value="employee?.position" :class="fieldClass" />
            <p v-if="errors.position" class="text-sm text-rose-600">{{ errors.position }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="address">Address</label>
            <input id="address" name="address" required :value="employee?.address" :class="fieldClass" />
            <p v-if="errors.address" class="text-sm text-rose-600">{{ errors.address }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="education">Education</label>
            <input id="education" name="education" :value="employee?.education ?? ''" :class="fieldClass" />
            <p v-if="errors.education" class="text-sm text-rose-600">{{ errors.education }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="salary">Salary</label>
            <input
                id="salary"
                name="salary"
                type="number"
                step="0.01"
                required
                :value="employee?.salary"
                :class="fieldClass"
            />
            <p v-if="errors.salary" class="text-sm text-rose-600">{{ errors.salary }}</p>
        </div>
    </div>
</template>

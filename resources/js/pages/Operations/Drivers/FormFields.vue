<script setup lang="ts">
export type DriverForm = {
    full_name: string;
    driver_license_no: string;
    federal_no: string | null;
    license_type: string;
    experience: number | string | null;
    phone: string;
    address: string;
    education: string | null;
    salary: number | string;
};

defineProps<{
    errors: Record<string, string>;
    driver?: DriverForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="full_name">Full name</label>
            <input id="full_name" name="full_name" required :value="driver?.full_name" :class="fieldClass" />
            <p v-if="errors.full_name" class="text-sm text-rose-600">{{ errors.full_name }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="driver_license_no">Driver license no</label>
            <input
                id="driver_license_no"
                name="driver_license_no"
                required
                :value="driver?.driver_license_no"
                :class="fieldClass"
            />
            <p v-if="errors.driver_license_no" class="text-sm text-rose-600">{{ errors.driver_license_no }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="federal_no">Federal no</label>
            <input id="federal_no" name="federal_no" :value="driver?.federal_no ?? ''" :class="fieldClass" />
            <p v-if="errors.federal_no" class="text-sm text-rose-600">{{ errors.federal_no }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="license_type">License type</label>
            <input id="license_type" name="license_type" required :value="driver?.license_type" :class="fieldClass" />
            <p v-if="errors.license_type" class="text-sm text-rose-600">{{ errors.license_type }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="experience">Experience (years)</label>
            <input
                id="experience"
                name="experience"
                type="number"
                :value="driver?.experience ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.experience" class="text-sm text-rose-600">{{ errors.experience }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="phone">Phone number</label>
            <input id="phone" name="phone" required :value="driver?.phone" :class="fieldClass" />
            <p v-if="errors.phone" class="text-sm text-rose-600">{{ errors.phone }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="address">Address</label>
            <input id="address" name="address" required :value="driver?.address" :class="fieldClass" />
            <p v-if="errors.address" class="text-sm text-rose-600">{{ errors.address }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="education">Education</label>
            <input id="education" name="education" :value="driver?.education ?? ''" :class="fieldClass" />
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
                :value="driver?.salary"
                :class="fieldClass"
            />
            <p v-if="errors.salary" class="text-sm text-rose-600">{{ errors.salary }}</p>
        </div>
    </div>
</template>

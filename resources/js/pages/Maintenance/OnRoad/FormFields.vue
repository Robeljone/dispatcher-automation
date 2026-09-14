<script setup lang="ts">
import { computed, ref } from 'vue';

export type VehicleOption = {
    id: number;
    label: string;
};

export type DriverOption = {
    id: number;
    label: string;
};

export type DetailLine = {
    type: string;
    quantity: number | string;
    price: number | string;
};

export type OnRoadForm = {
    vehicle_id: number | string;
    driver_id: number | string;
    details: DetailLine[];
};

const props = defineProps<{
    errors: Record<string, string>;
    vehicles: VehicleOption[];
    drivers: DriverOption[];
    record?: OnRoadForm;
}>();

const details = ref<DetailLine[]>(
    props.record?.details?.length
        ? props.record.details.map((line) => ({ ...line }))
        : [{ type: '', quantity: 1, price: '' }],
);

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';

function addDetail(): void {
    details.value.push({ type: '', quantity: 1, price: '' });
}

function removeDetail(index: number): void {
    details.value.splice(index, 1);

    if (details.value.length === 0) {
        details.value.push({ type: '', quantity: 1, price: '' });
    }
}

function lineSum(lines: { quantity?: number | string; price: number | string }[]): number {
    return lines.reduce((sum, line) => sum + (Number(line.quantity) || 0) * (Number(line.price) || 0), 0);
}

const grandTotal = computed(() => lineSum(details.value));

function money(value: number): string {
    return value.toFixed(2);
}
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium" for="vehicle_id">Vehicle</label>
                <select id="vehicle_id" name="vehicle_id" required :value="record?.vehicle_id" :class="fieldClass">
                    <option value="" disabled>Select a vehicle</option>
                    <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                        {{ vehicle.label }}
                    </option>
                </select>
                <p v-if="errors.vehicle_id" class="text-sm text-rose-600">{{ errors.vehicle_id }}</p>
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium" for="driver_id">Driver</label>
                <select id="driver_id" name="driver_id" required :value="record?.driver_id" :class="fieldClass">
                    <option value="" disabled>Select a driver</option>
                    <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                        {{ driver.label }}
                    </option>
                </select>
                <p v-if="errors.driver_id" class="text-sm text-rose-600">{{ errors.driver_id }}</p>
            </div>
        </div>

        <div
            class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40"
        >
            <div class="flex flex-wrap items-end justify-between gap-3">
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Total amount</p>
                <p class="text-2xl font-semibold text-indigo-700 dark:text-indigo-300">{{ money(grandTotal) }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <div class="flex justify-end">
                <button type="button" class="text-sm font-medium text-indigo-600" @click="addDetail">Add work</button>
            </div>
            <div
                v-for="(detail, index) in details"
                :key="`detail-${index}`"
                class="grid gap-3 sm:grid-cols-[1fr_6rem_10rem_auto]"
            >
                <div class="flex flex-col gap-2">
                    <input
                        :name="`details[${index}][type]`"
                        :value="detail.type"
                        placeholder="Type"
                        :class="fieldClass"
                        @input="detail.type = ($event.target as HTMLInputElement).value"
                    />
                    <p v-if="errors[`details.${index}.type`]" class="text-sm text-rose-600">
                        {{ errors[`details.${index}.type`] }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <input
                        :name="`details[${index}][quantity]`"
                        type="number"
                        min="1"
                        step="1"
                        v-model="detail.quantity"
                        placeholder="Qty"
                        :class="fieldClass"
                    />
                    <p v-if="errors[`details.${index}.quantity`]" class="text-sm text-rose-600">
                        {{ errors[`details.${index}.quantity`] }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <input
                        :name="`details[${index}][price]`"
                        type="number"
                        min="0"
                        step="0.01"
                        v-model="detail.price"
                        placeholder="Price"
                        :class="fieldClass"
                    />
                    <p v-if="errors[`details.${index}.price`]" class="text-sm text-rose-600">
                        {{ errors[`details.${index}.price`] }}
                    </p>
                </div>
                <button type="button" class="self-start text-sm text-rose-600" @click="removeDetail(index)">
                    Remove
                </button>
            </div>
        </div>
    </div>
</template>

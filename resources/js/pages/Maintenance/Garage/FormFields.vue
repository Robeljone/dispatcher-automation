<script setup lang="ts">
import { computed, ref } from 'vue';

export type VehicleOption = {
    id: number;
    label: string;
};

export type DetailLine = {
    type: string;
    quantity: number | string;
    price: number | string;
};

export type SpareLine = {
    name: string;
    quantity: number | string;
    price: number | string;
};

export type GarageForm = {
    vehicle_id: number | string;
    prev_main_date: string | null;
    prev_main_km: number | string | null;
    current_km: number | string;
    inspection_detail: string | null;
    details: DetailLine[];
    spares: SpareLine[];
};

const props = defineProps<{
    errors: Record<string, string>;
    vehicles: VehicleOption[];
    record?: GarageForm;
}>();

const details = ref<DetailLine[]>(
    props.record?.details?.length
        ? props.record.details.map((line) => ({ ...line }))
        : [{ type: '', quantity: 1, price: '' }],
);
const spares = ref<SpareLine[]>(
    props.record?.spares?.length
        ? props.record.spares.map((line) => ({ ...line }))
        : [{ name: '', quantity: 1, price: '' }],
);

const errorKeys = Object.keys(props.errors);
const tab = ref(
    errorKeys.some((key) => key.startsWith('spares.')) && !errorKeys.some((key) => key.startsWith('details.'))
        ? 'spares'
        : 'details',
);

const tabButtonClass = (value: string): string =>
    tab.value === value
        ? 'rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white'
        : 'rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';

function addDetail(): void {
    details.value.push({ type: '', quantity: 1, price: '' });
}

function removeDetail(index: number): void {
    details.value.splice(index, 1);

    if (details.value.length === 0) {
        details.value.push({ type: '', quantity: 1, price: '' });
    }
}

function addSpare(): void {
    spares.value.push({ name: '', quantity: 1, price: '' });
}

function removeSpare(index: number): void {
    spares.value.splice(index, 1);

    if (spares.value.length === 0) {
        spares.value.push({ name: '', quantity: 1, price: '' });
    }
}

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';

function lineSum(lines: { quantity?: number | string; price: number | string }[]): number {
    return lines.reduce((sum, line) => sum + (Number(line.quantity) || 0) * (Number(line.price) || 0), 0);
}

const workTotal = computed(() => lineSum(details.value));
const spareTotal = computed(() => lineSum(spares.value));
const grandTotal = computed(() => workTotal.value + spareTotal.value);

function money(value: number): string {
    return value.toFixed(2);
}
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
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
                <label class="text-sm font-medium" for="prev_main_date">Previous maintenance date</label>
                <input
                    id="prev_main_date"
                    name="prev_main_date"
                    type="date"
                    :value="record?.prev_main_date ?? ''"
                    :class="fieldClass"
                />
                <p v-if="errors.prev_main_date" class="text-sm text-rose-600">{{ errors.prev_main_date }}</p>
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium" for="prev_main_km">Previous km</label>
                <input
                    id="prev_main_km"
                    name="prev_main_km"
                    type="number"
                    min="0"
                    :value="record?.prev_main_km ?? ''"
                    :class="fieldClass"
                />
                <p v-if="errors.prev_main_km" class="text-sm text-rose-600">{{ errors.prev_main_km }}</p>
            </div>
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium" for="current_km">Current km</label>
                <input
                    id="current_km"
                    name="current_km"
                    type="number"
                    min="0"
                    required
                    :value="record?.current_km"
                    :class="fieldClass"
                />
                <p v-if="errors.current_km" class="text-sm text-rose-600">{{ errors.current_km }}</p>
            </div>
            <div class="flex flex-col gap-2 sm:col-span-2 lg:col-span-3">
                <label class="text-sm font-medium" for="inspection_detail">Inspection detail</label>
                <textarea
                    id="inspection_detail"
                    name="inspection_detail"
                    rows="3"
                    :value="record?.inspection_detail ?? ''"
                    :class="fieldClass"
                />
                <p v-if="errors.inspection_detail" class="text-sm text-rose-600">{{ errors.inspection_detail }}</p>
            </div>
        </div>

        <div
            class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40"
        >
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Total amount</p>
                    <p class="text-xs text-slate-500">Work {{ money(workTotal) }} + Spares {{ money(spareTotal) }}</p>
                </div>
                <p class="text-2xl font-semibold text-indigo-700 dark:text-indigo-300">{{ money(grandTotal) }}</p>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap gap-2">
                <button type="button" :class="tabButtonClass('details')" @click="tab = 'details'">Work details</button>
                <button type="button" :class="tabButtonClass('spares')" @click="tab = 'spares'">Spare parts</button>
            </div>

            <div v-show="tab === 'details'" class="flex flex-col gap-3">
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

            <div v-show="tab === 'spares'" class="flex flex-col gap-3">
                <div class="flex justify-end">
                    <button type="button" class="text-sm font-medium text-indigo-600" @click="addSpare">Add spare</button>
                </div>
                <div
                    v-for="(spare, index) in spares"
                    :key="`spare-${index}`"
                    class="grid gap-3 sm:grid-cols-[1fr_6rem_10rem_auto]"
                >
                    <div class="flex flex-col gap-2">
                        <input
                            :name="`spares[${index}][name]`"
                            :value="spare.name"
                            placeholder="Name"
                            :class="fieldClass"
                            @input="spare.name = ($event.target as HTMLInputElement).value"
                        />
                        <p v-if="errors[`spares.${index}.name`]" class="text-sm text-rose-600">
                            {{ errors[`spares.${index}.name`] }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <input
                            :name="`spares[${index}][quantity]`"
                            type="number"
                            min="1"
                            step="1"
                            v-model="spare.quantity"
                            placeholder="Qty"
                            :class="fieldClass"
                        />
                        <p v-if="errors[`spares.${index}.quantity`]" class="text-sm text-rose-600">
                            {{ errors[`spares.${index}.quantity`] }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <input
                            :name="`spares[${index}][price]`"
                            type="number"
                            min="0"
                            step="0.01"
                            v-model="spare.price"
                            placeholder="Price"
                            :class="fieldClass"
                        />
                        <p v-if="errors[`spares.${index}.price`]" class="text-sm text-rose-600">
                            {{ errors[`spares.${index}.price`] }}
                        </p>
                    </div>
                    <button type="button" class="self-start text-sm text-rose-600" @click="removeSpare(index)">
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
export type VehicleForm = {
    vehicle_type: string;
    made_in: string;
    model: string;
    made_year: number | string;
    chassis_no: string;
    motor_no: string;
    body_type: string;
    color: string;
    fuel_type: string;
    motor_cc: number | string | null;
    weight_total: number | string | null;
    weight_single: number | string | null;
    load_amount: number | string | null;
    motor_capacity: string | null;
    total_cylinder: number | string | null;
    plate_no: string;
    pre_plate_no: string | null;
};

defineProps<{
    errors: Record<string, string>;
    vehicle?: VehicleForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="vehicle_type">Vehicle type</label>
            <input
                id="vehicle_type"
                name="vehicle_type"
                required
                :value="vehicle?.vehicle_type"
                :class="fieldClass"
            />
            <p v-if="errors.vehicle_type" class="text-sm text-rose-600">{{ errors.vehicle_type }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="made_in">Made in</label>
            <input id="made_in" name="made_in" required :value="vehicle?.made_in" :class="fieldClass" />
            <p v-if="errors.made_in" class="text-sm text-rose-600">{{ errors.made_in }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="model">Model</label>
            <input id="model" name="model" required :value="vehicle?.model" :class="fieldClass" />
            <p v-if="errors.model" class="text-sm text-rose-600">{{ errors.model }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="made_year">Made year</label>
            <input
                id="made_year"
                name="made_year"
                type="number"
                required
                :value="vehicle?.made_year"
                :class="fieldClass"
            />
            <p v-if="errors.made_year" class="text-sm text-rose-600">{{ errors.made_year }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="chassis_no">Chassis no</label>
            <input
                id="chassis_no"
                name="chassis_no"
                required
                :value="vehicle?.chassis_no"
                :class="fieldClass"
            />
            <p v-if="errors.chassis_no" class="text-sm text-rose-600">{{ errors.chassis_no }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="motor_no">Motor no</label>
            <input id="motor_no" name="motor_no" required :value="vehicle?.motor_no" :class="fieldClass" />
            <p v-if="errors.motor_no" class="text-sm text-rose-600">{{ errors.motor_no }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="body_type">Body type</label>
            <input id="body_type" name="body_type" required :value="vehicle?.body_type" :class="fieldClass" />
            <p v-if="errors.body_type" class="text-sm text-rose-600">{{ errors.body_type }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="color">Color</label>
            <input id="color" name="color" required :value="vehicle?.color" :class="fieldClass" />
            <p v-if="errors.color" class="text-sm text-rose-600">{{ errors.color }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="fuel_type">Fuel type</label>
            <input id="fuel_type" name="fuel_type" required :value="vehicle?.fuel_type" :class="fieldClass" />
            <p v-if="errors.fuel_type" class="text-sm text-rose-600">{{ errors.fuel_type }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="motor_cc">Motor CC</label>
            <input id="motor_cc" name="motor_cc" type="number" :value="vehicle?.motor_cc ?? ''" :class="fieldClass" />
            <p v-if="errors.motor_cc" class="text-sm text-rose-600">{{ errors.motor_cc }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="weight_total">Weight total</label>
            <input
                id="weight_total"
                name="weight_total"
                type="number"
                step="0.01"
                :value="vehicle?.weight_total ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.weight_total" class="text-sm text-rose-600">{{ errors.weight_total }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="weight_single">Weight single</label>
            <input
                id="weight_single"
                name="weight_single"
                type="number"
                step="0.01"
                :value="vehicle?.weight_single ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.weight_single" class="text-sm text-rose-600">{{ errors.weight_single }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="load_amount">Load amount</label>
            <input
                id="load_amount"
                name="load_amount"
                type="number"
                step="0.01"
                :value="vehicle?.load_amount ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.load_amount" class="text-sm text-rose-600">{{ errors.load_amount }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="motor_capacity">Motor capacity</label>
            <input
                id="motor_capacity"
                name="motor_capacity"
                :value="vehicle?.motor_capacity ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.motor_capacity" class="text-sm text-rose-600">{{ errors.motor_capacity }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="total_cylinder">Total cylinder</label>
            <input
                id="total_cylinder"
                name="total_cylinder"
                type="number"
                :value="vehicle?.total_cylinder ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.total_cylinder" class="text-sm text-rose-600">{{ errors.total_cylinder }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="plate_no">Plate no</label>
            <input id="plate_no" name="plate_no" required :value="vehicle?.plate_no" :class="fieldClass" />
            <p v-if="errors.plate_no" class="text-sm text-rose-600">{{ errors.plate_no }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="pre_plate_no">Previous plate no</label>
            <input
                id="pre_plate_no"
                name="pre_plate_no"
                :value="vehicle?.pre_plate_no ?? ''"
                :class="fieldClass"
            />
            <p v-if="errors.pre_plate_no" class="text-sm text-rose-600">{{ errors.pre_plate_no }}</p>
        </div>
    </div>
</template>

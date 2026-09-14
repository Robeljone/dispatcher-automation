<script setup lang="ts">
export type StockOption = {
    id: number;
    item_name: string;
    stock_quantity: number;
    buy_price: string;
};

export type SelectOption = {
    id: number;
    label: string;
};

export type ItemRequestForm = {
    stock_id: number | string | null;
    request_by: number | string;
    quantity: number | string;
    date_requested: string;
    vehicle_id: number | string;
};

defineProps<{
    errors: Record<string, string>;
    stocks: StockOption[];
    vehicles: SelectOption[];
    drivers: SelectOption[];
    itemRequest?: ItemRequestForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="stock_id">Item</label>
            <p class="text-xs text-slate-500">Format: name=quantity-price</p>
            <select id="stock_id" name="stock_id" required :value="itemRequest?.stock_id ?? ''" :class="fieldClass">
                <option value="" disabled>Select an item</option>
                <option v-for="stock in stocks" :key="stock.id" :value="stock.id">
                    {{ stock.item_name }}={{ stock.stock_quantity }}-{{ stock.buy_price }}
                </option>
            </select>
            <p v-if="errors.stock_id" class="text-sm text-rose-600">{{ errors.stock_id }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="request_by">Driver</label>
            <select id="request_by" name="request_by" required :value="itemRequest?.request_by" :class="fieldClass">
                <option value="" disabled>Select a driver</option>
                <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                    {{ driver.label }}
                </option>
            </select>
            <p v-if="errors.request_by" class="text-sm text-rose-600">{{ errors.request_by }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="vehicle_id">Vehicle</label>
            <select id="vehicle_id" name="vehicle_id" required :value="itemRequest?.vehicle_id" :class="fieldClass">
                <option value="" disabled>Select a vehicle</option>
                <option v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                    {{ vehicle.label }}
                </option>
            </select>
            <p v-if="errors.vehicle_id" class="text-sm text-rose-600">{{ errors.vehicle_id }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="quantity">Quantity</label>
            <input
                id="quantity"
                name="quantity"
                type="number"
                min="1"
                required
                :value="itemRequest?.quantity"
                :class="fieldClass"
            />
            <p v-if="errors.quantity" class="text-sm text-rose-600">{{ errors.quantity }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="date_requested">Date requested</label>
            <input
                id="date_requested"
                name="date_requested"
                type="date"
                required
                :value="itemRequest?.date_requested"
                :class="fieldClass"
            />
            <p v-if="errors.date_requested" class="text-sm text-rose-600">{{ errors.date_requested }}</p>
        </div>
    </div>
</template>

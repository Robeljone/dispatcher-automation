<script setup lang="ts">
export type CatalogOption = {
    id: number;
    name: string;
    brand: string;
};

export type StockForm = {
    item_id: number | string;
    stock_quantity: number | string;
    min_quantity: number | string;
    buy_price: number | string;
};

defineProps<{
    errors: Record<string, string>;
    items?: CatalogOption[];
    stock?: StockForm & { item_name?: string };
    lockItem?: boolean;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="item_id">Item</label>
            <select
                v-if="!lockItem"
                id="item_id"
                name="item_id"
                required
                :value="stock?.item_id"
                :class="fieldClass"
            >
                <option value="" disabled>Select an item</option>
                <option v-for="item in items" :key="item.id" :value="item.id">
                    {{ item.name }} · {{ item.brand }}
                </option>
            </select>
            <input v-else type="hidden" name="item_id" :value="stock?.item_id" />
            <p v-if="lockItem" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm dark:border-slate-700">
                {{ stock?.item_name }}
            </p>
            <p v-if="errors.item_id" class="text-sm text-rose-600">{{ errors.item_id }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="stock_quantity">Quantity</label>
            <input
                id="stock_quantity"
                name="stock_quantity"
                type="number"
                min="0"
                required
                :value="stock?.stock_quantity"
                :class="fieldClass"
            />
            <p v-if="errors.stock_quantity" class="text-sm text-rose-600">{{ errors.stock_quantity }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="min_quantity">Minimum quantity</label>
            <input
                id="min_quantity"
                name="min_quantity"
                type="number"
                min="0"
                required
                :value="stock?.min_quantity"
                :class="fieldClass"
            />
            <p v-if="errors.min_quantity" class="text-sm text-rose-600">{{ errors.min_quantity }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="buy_price">Buy price</label>
            <input
                id="buy_price"
                name="buy_price"
                type="number"
                min="0"
                step="0.01"
                required
                :value="stock?.buy_price"
                :class="fieldClass"
            />
            <p v-if="errors.buy_price" class="text-sm text-rose-600">{{ errors.buy_price }}</p>
        </div>
    </div>
</template>

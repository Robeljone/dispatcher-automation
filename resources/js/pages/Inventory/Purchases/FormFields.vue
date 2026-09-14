<script setup lang="ts">
export type CatalogOption = {
    id: number;
    name: string;
    brand?: string;
};

export type PurchaseForm = {
    item_id: number | string;
    quantity: number | string;
    supplier_id: number | string;
    price: number | string;
};

defineProps<{
    errors: Record<string, string>;
    items: CatalogOption[];
    suppliers: CatalogOption[];
    purchase?: PurchaseForm;
}>();

const fieldClass =
    'rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950';
</script>

<template>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="item_id">Item</label>
            <select id="item_id" name="item_id" required :value="purchase?.item_id" :class="fieldClass">
                <option value="" disabled>Select an item</option>
                <option v-for="item in items" :key="item.id" :value="item.id">
                    {{ item.name }}<span v-if="item.brand"> · {{ item.brand }}</span>
                </option>
            </select>
            <p v-if="errors.item_id" class="text-sm text-rose-600">{{ errors.item_id }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id" required :value="purchase?.supplier_id" :class="fieldClass">
                <option value="" disabled>Select a supplier</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.name }}
                </option>
            </select>
            <p v-if="errors.supplier_id" class="text-sm text-rose-600">{{ errors.supplier_id }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="quantity">Quantity</label>
            <input
                id="quantity"
                name="quantity"
                type="number"
                min="1"
                required
                :value="purchase?.quantity"
                :class="fieldClass"
            />
            <p v-if="errors.quantity" class="text-sm text-rose-600">{{ errors.quantity }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="price">Price</label>
            <input
                id="price"
                name="price"
                type="number"
                min="0"
                step="0.01"
                required
                :value="purchase?.price"
                :class="fieldClass"
            />
            <p v-if="errors.price" class="text-sm text-rose-600">{{ errors.price }}</p>
        </div>
        <div class="flex flex-col gap-2">
            <label class="text-sm font-medium" for="attachment">Attachment</label>
            <input id="attachment" name="attachment" type="file" :class="fieldClass" />
            <p v-if="errors.attachment" class="text-sm text-rose-600">{{ errors.attachment }}</p>
        </div>
    </div>
</template>

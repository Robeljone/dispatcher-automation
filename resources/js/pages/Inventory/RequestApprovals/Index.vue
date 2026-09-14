<script setup lang="ts">
import { updateItemRequest, updatePurchase } from '@/actions/App/Http/Controllers/RequestApprovalController';
import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, setLayoutProps } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

setLayoutProps({
    title: 'Request approval',
});

defineOptions({
    layout: AppLayout,
});

const props = defineProps<{
    purchases: {
        id: number;
        item_name: string;
        supplier_name: string;
        quantity: number;
        price: string;
    }[];
    itemRequests: {
        id: number;
        item_name: string;
        quantity: number;
        vehicle: string;
        driver: string;
        total: string;
    }[];
    tab: string;
}>();

const tab = ref(props.tab === 'item-requests' ? 'item-requests' : 'purchases');

watch(
    () => props.tab,
    (value) => {
        tab.value = value === 'item-requests' ? 'item-requests' : 'purchases';
    },
);

const tabButtonClass = (value: string): string =>
    tab.value === value
        ? 'rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white'
        : 'rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
</script>

<template>
    <Head title="Request approval" />
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap gap-2">
            <button type="button" :class="tabButtonClass('purchases')" @click="tab = 'purchases'">
                Purchases
            </button>
            <button type="button" :class="tabButtonClass('item-requests')" @click="tab = 'item-requests'">
                Item requests
            </button>
        </div>

        <section
            v-if="tab === 'purchases'"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-medium">Item</th>
                            <th class="px-4 py-3 font-medium">Supplier</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Price</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="purchases.length === 0">
                            <td class="px-4 py-4 text-slate-500" colspan="5">No pending purchases.</td>
                        </tr>
                        <tr
                            v-for="purchase in purchases"
                            :key="purchase.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">{{ purchase.item_name }}</td>
                            <td class="px-4 py-3">{{ purchase.supplier_name }}</td>
                            <td class="px-4 py-3">{{ purchase.quantity }}</td>
                            <td class="px-4 py-3">{{ purchase.price }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Form v-bind="updatePurchase.form(purchase.id)">
                                        <input type="hidden" name="decision" value="approved" />
                                        <input type="hidden" name="tab" value="purchases" />
                                        <button type="submit" class="text-sm font-medium text-emerald-600">Approve</button>
                                    </Form>
                                    <Form v-bind="updatePurchase.form(purchase.id)">
                                        <input type="hidden" name="decision" value="rejected" />
                                        <input type="hidden" name="tab" value="purchases" />
                                        <button type="submit" class="text-sm font-medium text-rose-600">Reject</button>
                                    </Form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section
            v-else
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-medium">Item</th>
                            <th class="px-4 py-3 font-medium">Vehicle</th>
                            <th class="px-4 py-3 font-medium">Qty</th>
                            <th class="px-4 py-3 font-medium">Driver</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="itemRequests.length === 0">
                            <td class="px-4 py-4 text-slate-500" colspan="6">No pending item requests.</td>
                        </tr>
                        <tr
                            v-for="itemRequest in itemRequests"
                            :key="itemRequest.id"
                            class="border-t border-slate-100 dark:border-slate-800"
                        >
                            <td class="px-4 py-3 font-medium">{{ itemRequest.item_name }}</td>
                            <td class="px-4 py-3">{{ itemRequest.vehicle }}</td>
                            <td class="px-4 py-3">{{ itemRequest.quantity }}</td>
                            <td class="px-4 py-3">{{ itemRequest.driver }}</td>
                            <td class="px-4 py-3">{{ itemRequest.total }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Form v-bind="updateItemRequest.form(itemRequest.id)">
                                        <input type="hidden" name="decision" value="approved" />
                                        <input type="hidden" name="tab" value="item-requests" />
                                        <button type="submit" class="text-sm font-medium text-emerald-600">Approve</button>
                                    </Form>
                                    <Form v-bind="updateItemRequest.form(itemRequest.id)">
                                        <input type="hidden" name="decision" value="rejected" />
                                        <input type="hidden" name="tab" value="item-requests" />
                                        <button type="submit" class="text-sm font-medium text-rose-600">Reject</button>
                                    </Form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>

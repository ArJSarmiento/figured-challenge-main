<!--
    This Page is used to display the inventory of the products.
-->
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BackButton from '@/Components/BackButton.vue';
import { Head, Link } from '@inertiajs/inertia-vue3'

defineProps({
    products: Array,
});

const formatPrice = (price) => {
    return parseFloat(price).toFixed(2);
}

</script>

<template>
    <Head title="Inventory" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inventory
            </h2>
        </template>

        <div class="p-12 overflow-x-scroll flex-col">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                    <tr>
                        <th class="p-2 whitespace-nowrap">
                            <div class="font-semibold text-center">Id</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                            <div class="font-semibold text-center">Price/unit</div>
                        </th>
                        <th class="p-2 whitespace-nowrap">
                            <div class="font-semibold text-center">Quantity</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <tr v-for="product in products" :key="product.id">
                        <td class="p-2 whitespace-nowrap">
                            <div class="text-center"> {{ product.id }}</div>
                        </td>
                        <td class="p-2 whitespace-nowrap">
                            <div class="text-center"> ${{ formatPrice(product.unit_price) }}</div>
                        </td>
                        <td class="p-2 whitespace-nowrap">
                            <div class="text-center font-medium">{{ product.quantity }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <BackButton class="mt-6 float-right p-0" >
                <Link href="/dashboard" class="w-full h-full">
                    Close
                </Link>
            </BackButton>
        </div>
    </AuthenticatedLayout>

</template>

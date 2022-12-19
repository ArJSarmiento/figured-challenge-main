<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import  { Head, Link }  from '@inertiajs/inertia-vue3'

defineProps({
    total_price: Number,
    applied_products: Array,
    isError: Boolean,
    errorMessage: String,
});

const formatPrice =  (price) => {
    return parseFloat(price).toFixed(2);
}

</script>

<template>
    <Head title="Summary" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Summary
            </h2>
        </template>

        <div class="p-12">
            <div v-if="!isError">
                <h1 class="text-4xl font-bold text-green-500">
                   ${{ formatPrice(total_price) }}
                   <span class="text-xl text-gray-400">
                        in total
                    </span>
                </h1>

                <table class="table-auto w-full mt-10">
                    <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                        <tr>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Price/unit</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Quantity</div>
                            </th>
                            <th class="p-2 whitespace-nowrap">
                                <div class="font-semibold text-left">Price</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <tr v-for="product in applied_products" :key="product.id">
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left"> ${{ formatPrice(product.price) }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left font-medium text-green-500">{{ product.quantity }}</div>
                            </td>
                            <td class="p-2 whitespace-nowrap">
                                <div class="text-left font-medium text-green-500">${{ formatPrice(product.total_price) }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>

                <h1 class="text-4xl font-medium text-gray-900">
                    Error
                </h1>
                <p class="mt-3 text-m text-gray-600">
                    {{ errorMessage }}
                </p>

            </div>
            <Link href="/dashboard" class="mt-6 flex justify-end">
                <SecondaryButton >
                    Close
                </SecondaryButton>
            </Link>
        </div>
    </AuthenticatedLayout>

</template>

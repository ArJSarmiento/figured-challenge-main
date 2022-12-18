<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link } from '@inertiajs/inertia-vue3'

defineProps({
    total_price: Number,
    applied_products: Array,
    errorModal: Boolean,
    errorMessage: String,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Summary
            </h2>
        </template>

        <h1>
            ${{ total_price }}
        </h1>

        <ul v-for="product in applied_products" :key="product.id">
            <li>
                {{ product.quantity }}
            </li>

            <li>
                ${{ product.price }}
            </li>

        </ul>
    </AuthenticatedLayout>

    <Modal show="errorModal" @close="errorModal = false">
        <div class="p-12">
            <h1>
                Error
            </h1>
            <p>
                {{ errorMessage }}
            </p>

            <Link href="/dashboard">
                <SecondaryButton >
                    Close
                </SecondaryButton>
            </Link>
        </div>
    </Modal>

</template>

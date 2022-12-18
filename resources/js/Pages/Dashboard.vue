<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    quantity: 0
});

const submit = () => {
    form.post(route('apply-products'));
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <form @submit.prevent="submit" class="py-12 px-12">
            <div>
                <InputLabel for="quantity" value="Quantity" />

                <TextInput
                    id="quantity"
                    type="number"
                    min="0"
                    class="mt-1 block w-full"
                    v-model="form.quantity"
                    required
                    autofocus
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Apply
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

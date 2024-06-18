<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    products: Object
});
console.log(props.products)
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Home</h2>
        </template>

       <div class="max-w-7xl mt-8 mx-auto">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <Link :href="route('product.show', product.id)" v-for="product in products" :key="product.id" class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <img class="w-full h-56 object-cover object-center" :src="product.image" alt="avatar">
                    <div class="p-4">
                        <p class="text-indigo-500 text-md font-semibold">{{ product.name }}</p>
                        <p class="text-gray-400 text-sm">{{ product.description }}</p>
                        
                        <p class="text-gray-400 text-sm">{{ product.price }}</p>
                    </div>
                </Link>
            </div>
        </div>
        

    </AuthenticatedLayout>
</template>

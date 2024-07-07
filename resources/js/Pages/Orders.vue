<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  orders: Array
});


</script>
<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Orders</h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
              <h1 class="text-2xl font-bold mb-4">My Orders</h1>
  
              <div v-if="orders.length > 0" class="mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="order in orders" :key="order.id">
                      <td class="px-6 py-4 whitespace-nowrap">{{ order.id }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">{{ new Date(order.created_at).toLocaleString() }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">${{ (order.amount_total / 100).toFixed(2) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <Link :href="route('order.show', order.id)" class="text-indigo-600 hover:text-indigo-900">View</Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
  
              <div v-else>
                <p class="text-gray-600">You have no orders.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
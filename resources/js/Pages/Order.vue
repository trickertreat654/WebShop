<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


const props = defineProps({

    order: Object
});

console.log(props.order)

</script>
<template>
    <AuthenticatedLayout>

       
        <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Order Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-bold mb-4">Order #{{ order.id }}</h1>
            <p class="mb-4">Order Date: {{ new Date(order.created_at).toLocaleString() }}</p>

            <div class="mb-6">
              <h3 class="text-lg font-semibold">Shipping Address</h3>
              <p>{{ order.shipping_address.line1 }}</p>
              <p>{{ order.shipping_address.city }}, {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }}</p>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold">Billing Address</h3>
              <p>{{ order.billing_address.line1 }}</p>
              <p>{{ order.billing_address.city }}, {{ order.billing_address.state }} {{ order.billing_address.postal_code }}</p>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold">Order Items</h3>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="item in order.items" :key="item.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.quantity }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${{ (item.price / 100).toFixed(2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${{ (item.amount_total / 100).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold">Order Total</h3>
              <p>Subtotal: ${{ (order.amount_subtotal / 100).toFixed(2) }}</p>
              <p>Tax: ${{ (order.amount_tax / 100).toFixed(2) }}</p>
              <p>Shipping: ${{ (order.amount_shipping / 100).toFixed(2) }}</p>
              <p class="font-bold">Total: ${{ (order.amount_total / 100).toFixed(2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>



</AuthenticatedLayout>
</template>
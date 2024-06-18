<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';


const props = defineProps({
    cart: Object,
    items: Object
});

console.log(props.cart)
console.log(props.items)

const form = useForm({
  items: props.items.reduce((acc, item) => {
    acc[item.id] = { quantity: item.quantity };
    return acc;
  }, {})
});

// Compute total price
const total = computed(() => {
  return props.items.reduce((sum, item) => {
    return sum + item.price * item.quantity;
  }, 0);
});

// Function to remove item from cart
function removeFromCart(itemId) {
 router.delete(route('cart.remove', itemId), {
    preserveState: true,
    onSuccess: () => {
      alert('Item removed from cart!');
    }
  });
}

function incrementQuantity(itemId) {
  router.patch(route('cart.increment', itemId))
}

function decrementQuantity(itemId) {
    
   
    router.patch(route('cart.decrement', itemId))
   
}


// Function to handle checkout
function checkout() {
  form.post('/checkout', {
    preserveState: true,
    onSuccess: () => {
      alert('Checkout successful!');
    }
  });
}
</script>
<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>
    <div v-if="$page.props.cartCount > 0" class="flex flex-col space-y-4">
      <div v-for="item in props.items" :key="item.id" class="flex items-center justify-between p-4 border rounded-md">
        <div class="flex items-center space-x-4">
          <img :src="item.image" alt="Product Image" class="w-20 h-20 object-cover rounded-md">
          <div>
            <h2 class="text-lg font-semibold">{{item.product +' '+item.variant }}</h2>
            <!-- <p class="text-gray-700">Variant: {{ item.variant.color }}</p> -->
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-lg font-semibold">${{  (item.price / 100).toFixed(2) }}</span>
          <span class="text-lg font-semibold"> {{ item.quantity }}</span>
          <!-- <input 
            type="number" 
            v-model.number="form.items[item.id].quantity" 
            min="1" 
            class="w-16 text-center py-2 px-3 border border-gray-300 rounded-md"> -->
           
            <button @click="incrementQuantity(item.id)" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none">+</button>
            <button @click="decrementQuantity(item.id)" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 focus:outline-none">-</button>
          <button 
            @click="removeFromCart(item.id)" 
            class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 focus:outline-none">
            Remove
          </button>
        </div>
      </div>
      <div class="flex justify-end space-x-4 mt-6">
        <span class="text-xl font-semibold">Total: ${{ (total / 100).toFixed(2) }}</span>
        <button 
          @click="checkout" 
          class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 focus:outline-none">
          Checkout
        </button>
      </div>
    </div>
    <div v-else class="text-center">
      <p class="text-lg">Your cart is empty.</p>
    </div>
  </div>

    </AuthenticatedLayout>

</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, onMounted } from 'vue';

const props = defineProps({
    product: Object
});
console.log(props.product)


const selectedImage = ref(props.product.images[0]);

const form = useForm({
  product_id: props.product.id,
  variant: props.product.variants[0].id
});

const selectedVariant = computed(() => {
  return props.product.variants.find(variant => variant.id === form.variant);
});

function addToCart() {
  form.post(route('product.add-to-cart'),  {
    preserveState: true,
    onSuccess: () => {
      alert('Product added to cart!');
    }
  });
}
</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
    <div class="flex flex-col lg:flex-row">
      <div class="lg:w-1/2">
        <div class="mb-4">
          <!-- <img :src="selectedImage" alt="Product Image" class="w-full h-auto rounded-md" /> -->
          <img :src="'/'+selectedImage" alt="Product Image" class="w-full h-auto rounded-md" />
        </div>
        <div class="flex space-x-2">
          <img 
            v-for="image in props.product.images" 
            :key="image" 
            :src="'/'+image" 
            alt="Thumbnail" 
            class="w-20 h-20 object-cover rounded cursor-pointer border border-gray-200"
            @click="selectedImage = image"
          />
        </div>
      </div>
      <div class="lg:w-1/2 lg:pl-6">
        <h1 class="text-2xl font-bold mb-4">{{ product.name }}</h1>
        <p class="text-gray-700 mb-6">{{ product.description }}</p>
        
        <div class="mb-4">
          <label for="variant" class="block text-sm font-medium text-gray-700">Select Variant</label>
          <select 
            v-model="form.variant" 
            id="variant" 
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <option v-for="variant in props.product.variants" :key="variant.id" :value="variant.id">
              {{ variant.color }} - {{ variant.size }}
            </option>
          </select>
        </div>
        
        <div class="flex items-center">
          <span class="text-3xl font-bold text-gray-900 mr-4">${{ (props.product.price / 100).toFixed(2) }}</span>
        
          <button 
            @click="addToCart" 
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  </div>
      

    </AuthenticatedLayout>
</template>
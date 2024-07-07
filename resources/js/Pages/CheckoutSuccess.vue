<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
    sessionId: String
});
console.log(props.sessionId);

</script>

<template>
  <AuthenticatedLayout>
  <div>
    <h1>Checkout Success</h1>
    <p>Thank you for your purchase!</p>
    <p>Your order ID is: {{ props.sessionId }}</p>
  </div>
  </AuthenticatedLayout>
</template>
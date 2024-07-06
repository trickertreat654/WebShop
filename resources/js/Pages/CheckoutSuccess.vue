<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
    sessionId: String
});

</script>

<template>
  <div>
    <h1>Checkout Success</h1>
    <p>Thank you for your purchase!</p>
    <p>Your order ID is: {{ sessionId }}</p>
  </div>
</template>
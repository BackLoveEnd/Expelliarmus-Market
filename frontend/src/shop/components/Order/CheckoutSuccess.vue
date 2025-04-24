<script setup>
defineProps({
  orderId: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <section class="container mx-auto text-center space-y-6">
    <h2 class="text-4xl font-bold text-green-600">🎉 Thanks for ordering!</h2>
    <p class="text-lg text-gray-700">We will contact you in the near future.</p>
    <span class="text-sm text-gray-500">Order ID: {{ orderId }}</span>
    <div class="mx-auto space-x-6">
      <router-link
          to="/"
          class="text-center underline underline-offset-4 decoration-2 hover:decoration-yellow-500">
        Home
      </router-link>
      <router-link to="/" class="text-center underline underline-offset-4 decoration-2 hover:decoration-yellow-500">My
        Orders
      </router-link>
    </div>
  </section>
</template>

<style scoped>

</style>
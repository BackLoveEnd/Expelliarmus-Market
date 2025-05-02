<script setup>
import {useTruncator} from "@/composables/useTruncator.js";

defineProps({
  product: Object,
  quantity: Number | String,
  totalPrice: Number | String
});

const truncator = useTruncator();
</script>

<template>
  <div class="flex justify-between items-center p-3 border rounded mb-2">
    <div class="flex items-center gap-3">
      <div class="bg-gray-500 rounded w-10 h-10 overflow-hidden">
        <img :src="product.preview_image" alt="Product Image" class="w-full h-full object-cover">
      </div>
      <router-link
          :to="{ name: 'product-page', params: { productId: product.id, productSlug: product.slug}}"
          class="text-blue-500 hover:underline underline-offset-4 decoration-blue-500">
        {{ truncator.truncateString(product.title, 30) }}
      </router-link>
    </div>
    <div class="flex items-center gap-2 text-sm text-gray-500 text-right">
      <span>{{ quantity }}x</span>
      <span class="font-semibold text-black">${{ product.price_per_unit }}</span>
    </div>
  </div>
</template>

<style scoped>

</style>
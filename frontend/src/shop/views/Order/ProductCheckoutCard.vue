<template>
  <div class="flex justify-between w-full gap-x-6">
    <router-link :to="{ name: 'product-page', params: {productId: productLink.id, productSlug: productLink.slug}}"
                 class="flex items-center gap-x-6">
      <img :src="image" :alt="productName" class="max-w-14 max-h-14"/>
      <span class="text-base font-normal underline underline-offset-2 text-blue-400">{{ productName }}</span>
    </router-link>
    <div class="flex items-center justify-center gap-x-2">
      <span v-if="quantity">{{ quantity }}x</span>
      <div class="space-x-2" v-if="discount">
        <span class="line-through decoration-red-500">${{ price.toFixed(2) }}</span>
        <span class="font-semibold">${{ discount.new_price.toFixed(2) }}</span>
      </div>
      <span v-else>${{ price.toFixed(2) }}</span>
    </div>
  </div>
</template>

<script setup>
import {defineProps} from "vue";

const props = defineProps({
  image: {
    type: String,
    required: true,
  },
  productName: {
    type: String,
    required: true,
  },
  price: {
    type: Number,
    required: true,
  },
  quantity: {
    type: Number,
  },
  productLink: {
    type: Object,
    required: true
  },
  discount: {
    type: Object,
    default: null
  }
});
</script>

<style scoped></style>
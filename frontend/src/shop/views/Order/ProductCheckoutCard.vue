<template>
  <div class="flex justify-between w-full gap-x-6">
    <div class="flex items-center gap-x-6">
      <img :src="image" :alt="productName" class="max-w-14 max-h-14" />
      <span class="text-base font-normal">{{ productName }}</span>
    </div>
    <div class="flex items-center">
      <span class="text-base font-normal">{{ price }}</span>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

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
    type: String,
    required: true,
  },
});
</script>

<style scoped></style>
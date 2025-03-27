<template>
  <span class="font-semibold text-3xl max-w-lg">{{ title }}</span>
  <span class="text-xs">Article: {{ article }}</span>
  <div class="flex gap-x-4 items-center">
    <star-rating :rating="0" :review-number="0"></star-rating>
    <div class="border-l-2 border-gray-300 h-full"></div>
    <span class="text-green-500 text-sm">{{ status }}</span>
  </div>
  <div class="space-y-8">
    <div class="flex items-center gap-x-4">
      <span class="text-2xl font-normal">${{ price }}</span>
      <span v-show="quantityChanged" class="text-sm"
      >Per unit: ${{ pricePerUnit }}</span
      >
    </div>
    <p class="text-sm max-w-lg">
      {{ titleDesc }}
    </p>
    <hr class="h-[2px] bg-gray-300 border-0"/>
  </div>
</template>

<script setup>
import {defineProps, ref, watch} from "vue";
import StarRating from "@/components/Card/StarRating.vue";

const props = defineProps({
  pricePerUnit: {
    type: String,
    required: true,
  },
  price: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  titleDesc: {
    type: String,
  },
  article: {
    type: String,
    required: true,
  },
  status: {
    type: String
  }
});

const quantityChanged = ref(false);

watch([() => props.price, () => props.pricePerUnit], () => {
  quantityChanged.value = props.price !== props.pricePerUnit;
});
</script>

<style scoped></style>

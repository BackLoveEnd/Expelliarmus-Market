<template>
  <span class="font-semibold text-3xl max-w-lg">{{ title }}</span>
  <span class="text-xs">Article: {{ article }}</span>
  <div class="flex gap-x-4 items-center">
    <star-rating :rating="0" :review-number="0"></star-rating>
    <div class="border-l-2 border-gray-300 h-full"></div>
    <span :class="getStatusColor(status?.color)" class="text-sm">{{ status?.label }}</span>
  </div>
  <div class="space-y-8">
    <div class="flex items-center gap-x-4">
      <div v-if="discount !== null" class="flex flex-col gap-y-2">
        <div class="flex gap-x-4 items-center">
          <span class="text-xl font-normal line-through decoration-red-500">${{ discount.old_price }}</span>
          <span class="text-2xl font-semibold">${{ price }}</span>
          <span class="text-base font-semibold">Deal <span
              class="underline decoration-blue-500 underline-offset-2 decoration-2 text-blue-500">-{{
              discount.percentage
            }}</span>!</span>
        </div>
        <div class="text-sm flex items-center gap-x-2">
          <i class="pi pi-clock"/>
          <span>Until {{ new Date(discount.end_at).toLocaleString() }}</span>
        </div>
      </div>
      <span class="text-2xl font-normal" v-else>${{ price }}</span>
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
  discount: {
    type: Object,
    default: null
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
    type: Object
  }
});

const quantityChanged = ref(false);

watch([() => props.price, () => props.pricePerUnit], () => {
  quantityChanged.value = props.price !== props.pricePerUnit;
});

function getStatusColor(colorType) {
  switch (colorType) {
    case "success":
      return "text-green-500";
    case "danger":
      return "text-red-500";
    default:
      return "text-gray-500";
  }
}
</script>

<style scoped></style>

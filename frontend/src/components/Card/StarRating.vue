<template>
  <div class="flex items-center gap-1">
    <div class="flex space-x-1">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        v-for="star in 5"
        :class="[
          'w-5 h-5',
          star <= rating ? 'text-[#FFAD33]' : 'text-gray-300',
        ]"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
        />
      </svg>
    </div>
    <div>
      <span class="text-xs">({{ reviewNumber }})</span>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  rating: {
    type: Number,
    default: 0,
  },
  reviewNumber: {
    type: Number,
    default: 0,
  },
});
</script>

<style scoped></style>

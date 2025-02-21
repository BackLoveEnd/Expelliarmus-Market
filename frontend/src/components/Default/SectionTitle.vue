<template>
  <div class="flex items-center gap-x-4 mb-10">
    <div class="bg-[#db4444] w-5 h-10 rounded-md"></div>
    <span class="text-[#db4444] font-semibold">{{ title }}</span>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
});
</script>

<style scoped></style>
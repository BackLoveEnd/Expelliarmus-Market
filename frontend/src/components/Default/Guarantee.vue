<template>
  <div class="flex flex-col items-center gap-6">
    <div
      class="w-20 h-20 rounded-full bg-[#c1c1c1] flex justify-center items-center"
    >
      <div
        class="w-14 h-14 rounded-full bg-black flex justify-center items-center"
      >
        <slot></slot>
      </div>
    </div>
    <div class="flex flex-col gap-2 text-center">
      <p class="text-xl font-semibold">{{ title }}</p>
      <p class="text-sm">{{ text }}</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  title: String,
  text: String,
});
</script>

<style scoped></style>

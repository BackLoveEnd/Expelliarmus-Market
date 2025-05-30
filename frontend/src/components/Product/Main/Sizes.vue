<template>
  <div class="flex items-center gap-x-6">
    <span class="text-xl">Size:</span>
    <div class="flex gap-x-4">
      <div v-for="(size, index) in sizes" :key="index">
        <label class="inline-flex items-center">
          <input
            type="radio"
            name="size"
            :value="size.value"
            class="hidden peer"
          />
          <span
            class="w-8 h-8 flex justify-center items-center cursor-pointer border-2 border-gray-500 rounded-md peer-checked:bg-red-500 peer-checked:border-red-500 peer-checked:text-white text-black text-sm"
          >
            {{ size.name }}
          </span>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  sizes: Array,
});
</script>

<style scoped></style>
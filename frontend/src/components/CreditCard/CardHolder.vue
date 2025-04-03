<template>
  <div>
    <label for="full_name" class="mb-2 block text-sm font-medium text-gray-900"
      >Full name (as displayed on card)*</label
    >
    <input
      type="text"
      id="full_name"
      v-model="value"
      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
      placeholder="Bonnie Green"
      required
    />
  </div>
</template>

<script setup>
import { computed, defineProps } from "vue";

const props = defineProps({
  modelValue: String,
});
const emit = defineEmits(["update:modelValue"]);

const value = computed({
  get: () => props.modelValue,
  set: (newValue) => emit("update:modelValue", newValue),
});
</script>

<style scoped></style>

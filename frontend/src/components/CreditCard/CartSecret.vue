<template>
  <div>
    <label
      for="cvv-input"
      class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-900"
    >
      CVV*
    </label>
    <input
      type="text"
      id="cvv-input"
      v-model="value"
      aria-describedby="helper-text-explanation"
      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
      placeholder="•••"
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

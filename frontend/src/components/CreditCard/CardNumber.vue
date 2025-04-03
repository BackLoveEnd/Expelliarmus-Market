<template>
  <div>
    <label
      for="card-number-input"
      class="mb-2 block text-sm font-medium text-gray-900"
    >
      Card number*
    </label>
    <input
      type="text"
      id="card-number-input"
      v-model="value"
      class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pe-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
      placeholder="xxxx-xxxx-xxxx-xxxx"
      pattern="^4[0-9]{12}(?:[0-9]{3})?$"
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

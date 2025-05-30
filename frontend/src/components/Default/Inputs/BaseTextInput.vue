<template>
  <div class="space-y-2 w-full">
    <label v-if="label" :for="id"
      >{{ label }}<span v-if="required" class="text-red-800">*</span></label
    >
    <div
      class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
      :class="bodyClass"
    >
      <input
        :id="id"
        :name="name"
        :type="type"
        :placeholder="placeholder"
        v-model="value"
        class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
        :class="inputClass"
        :required="required"
      />
    </div>
    <span v-if="error" class="text-sm text-red-600 ml-px">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed, defineProps } from "vue";

const props = defineProps({
  label: {
    type: String,
  },
  id: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: "text",
  },
  placeholder: {
    type: String,
    default: "",
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  modelValue: String,
  bodyClass: {
    type: String,
    default: null,
  },
  inputClass: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["update:modelValue"]);

const value = computed({
  get: () => props.modelValue,
  set: (newValue) => emit("update:modelValue", newValue),
});
</script>

<style scoped></style>

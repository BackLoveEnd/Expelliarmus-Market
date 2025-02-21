<template>
  <div class="space-y-2">
    <label v-if="label" :for="id"
      >{{ label }}<span v-if="required" class="text-red-800">*</span></label
    >
    <div
      class="flex items-center rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
      :class="bodyClass"
    >
      <select
        :id="id"
        :name="name"
        v-model="value"
        class="w-full bg-transparent outline-none text-gray-700 text-base"
        :class="inputClass"
        :required="required"
      >
        <option value="" disabled hidden class="text-gray-700">
          {{ placeholder }}
        </option>
        <option
          class="bg-transparent"
          v-for="option in options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
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
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: null,
  },
  placeholder: {
    type: String,
    default: "",
  },
  options: {
    type: Array,
    required: true,
    default: () => [],
  },
  modelValue: String | Number,
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
  get: () => props.modelValue || "",
  set: (newValue) => emit("update:modelValue", newValue),
});
</script>

<style scoped></style>

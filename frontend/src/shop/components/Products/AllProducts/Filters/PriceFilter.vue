<script setup>
import Slider from "primevue/slider";
import {onMounted, ref} from "vue";

const props = defineProps({
  modelValue: Array
});

const emit = defineEmits(["update:modelValue"]);

let timeout;

const minPrice = ref();
const maxPrice = ref();

function updateValue(event) {
  clearTimeout(timeout);
  timeout = setTimeout(() => {
    emit("update:modelValue", event);
  }, 300);
}

onMounted(() => {
  minPrice.value = parseFloat(props.modelValue[0]);
  maxPrice.value = parseFloat(props.modelValue[1]);
});

function handleInputChange(index, event) {
  const value = parseFloat(event.target.value);

  const newModelValue = [...props.modelValue];

  newModelValue[index] = value;

  emit("update:modelValue", newModelValue);
}
</script>

<template>
  <div class="flex flex-col items-center my-2 gap-y-4">
    <div class="flex justify-center">
      <Slider
          range
          :modelValue="modelValue"
          :min="minPrice"
          :max="maxPrice"
          @update:modelValue="updateValue"
          class="w-56"
      />
    </div>
    <div class="flex justify-between w-56">
      <input
          type="number"
          :value="modelValue[0]"
          @input="handleInputChange(0, $event)"
          class="w-16 text-center text-sm border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
      />
      <input
          type="number"
          :value="modelValue[1]"
          @input="handleInputChange(1, $event)"
          class="w-16 text-center text-sm border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
      />
    </div>
    <span class="text-xs text-gray-400">
      *The filtering will take into account all products options, even if the default price displayed is higher or lower.
    </span>
  </div>
</template>

<style scoped>
input[type="number"] {
  appearance: none;
  -moz-appearance: textfield;
  -webkit-appearance: none;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>

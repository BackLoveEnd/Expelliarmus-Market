<template>
  <div class="flex h-full">
    <button
      @click="decreaseQuantity"
      class="group py-1 px-3 border border-gray-400 rounded-l-md bg-white transition-all duration-300 hover:bg-[#db4444] hover:shadow-sm hover:shadow-gray-300 hover:text-white"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="size-6"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
      </svg>
    </button>
    <input
      type="text"
      v-model="quantity"
      class="w-24 font-semibold text-gray-900 cursor-pointer text-base py-[4px] px-3 outline-0 border-y border-gray-400 bg-transparent placeholder:text-gray-900 text-center hover:bg-gray-50"
      placeholder="1"
    />
    <button
      @click="increaseQuantity"
      class="group py-1 px-3 border border-gray-400 rounded-r-md bg-white transition-all duration-300 hover:bg-[#db4444] hover:shadow-sm hover:shadow-gray-300 hover:text-white"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="size-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M12 4.5v15m7.5-7.5h-15"
        />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { defineProps, nextTick, ref, watch } from "vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: [Number, String],
});

const quantity = ref(props.modelValue || 1);

watch(quantity, (newValue) => {
  const parsedValue = parseInt(newValue, 10);
  if (isNaN(parsedValue) || parsedValue <= 0) {
    nextTick(() => {
      quantity.value = 1;
    });
  } else {
    quantity.value = parsedValue;
  }
  emit("update:modelValue", quantity.value);
});

function increaseQuantity() {
  ++quantity.value;
  emit("update:modelValue", quantity.value);
}

function decreaseQuantity() {
  if (quantity.value > 1) {
    --quantity.value;
    emit("update:modelValue", quantity.value);
  }
}
</script>

<style scoped></style>

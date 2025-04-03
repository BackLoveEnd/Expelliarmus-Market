<script setup>
import {computed, defineEmits, defineProps} from "vue";

const props = defineProps({
  title: String,
  itemsToShow: Number,
  widthBetweenItems: Number,
  additionalClasses: String,
  itemsLength: Number,
  modelValue: Number,
});

const emit = defineEmits(["update:modelValue"]);

const maxIndex = computed(() => Math.max(0, Math.ceil(props.itemsLength / props.itemsToShow) - 1));

function next() {
  if (props.modelValue < maxIndex.value) {
    emit("update:modelValue", props.modelValue + 1);
  }
}

function previous() {
  if (props.modelValue > 0) {
    emit("update:modelValue", props.modelValue - 1);
  }
}

const sliderStyle = computed(() => {
  const offset = props.modelValue * props.widthBetweenItems * props.itemsToShow;
  return {
    transform: `translateX(-${offset}px)`,
    width: `${props.widthBetweenItems * props.itemsLength}px`,
  };
});
</script>

<template>
  <div class="flex justify-between mb-10">
    <p class="text-4xl font-semibold">{{ title }}</p>
    <div class="flex space-x-4">
      <button
          @click="previous"
          class="w-11 h-11 bg-[#b9b9b9] rounded-full flex items-center justify-center"
          :disabled="modelValue === 0">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
        </svg>
      </button>
      <button
          @click="next"
          class="w-11 h-11 bg-[#b9b9b9] rounded-full flex items-center justify-center"
          :disabled="modelValue >= maxIndex">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
        </svg>
      </button>
    </div>
  </div>
  <div class="overflow-hidden ">
    <div :class="['flex transition-transform duration-300 mb-2', additionalClasses]" :style="sliderStyle">
      <slot></slot>
    </div>
  </div>
</template>

<template>
  <div class="flex justify-between mb-10">
    <p class="text-4xl font-semibold">{{ title }}</p>
    <div class="flex space-x-4">
      <button
        @click="previous"
        class="w-11 h-11 bg-[#b9b9b9] rounded-full flex items-center justify-center"
        :disabled="currentIndex === 0"
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
            d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"
          />
        </svg>
      </button>
      <button
        @click="next"
        class="w-11 h-11 bg-[#b9b9b9] rounded-full flex items-center justify-center"
        :disabled="currentIndex >= maxIndex"
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
            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
          />
        </svg>
      </button>
    </div>
  </div>
  <div class="overflow-hidden">
    <div
      :class="['flex transition-transform duration-300', additionalClasses]"
      :style="sliderStyle"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, ref, useSlots } from "vue";

const props = defineProps({
  title: {
    type: String,
    default: "Title",
  },
  itemsToShow: {
    type: Number,
    default: 5,
  },
  widthBetweenItems: Number,
  additionalClasses: {
    type: String,
    default: "",
  },
});

const currentIndex = ref(0);

const maxIndex = computed(() => {
  const totalItems = useSlots().default?.().length;
  return Math.max(0, Math.ceil(totalItems / props.itemsToShow) - 1);
});

const sliderStyle = computed(() => {
  const itemWidth = props.widthBetweenItems;
  const totalItems = useSlots().default?.().length;
  const offset = currentIndex.value * itemWidth * props.itemsToShow;
  return {
    transform: `translateX(-${offset}px)`,
    width: `${itemWidth * totalItems}px`,
  };
});

function next() {
  if (currentIndex.value < maxIndex.value) {
    currentIndex.value++;
  }
}

function previous() {
  if (currentIndex.value > 0) {
    currentIndex.value--;
  }
}
</script>

<style scoped>
.product-card {
  flex: 0 0 calc(100% / var(--items-to-show));
  box-sizing: border-box;
  max-width: calc(100% / var(--items-to-show));
}

:root {
  --items-to-show: 5;
}
</style>

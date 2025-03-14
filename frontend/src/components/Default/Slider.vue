<script setup>
import {onMounted, ref} from "vue";

const currentIndex = ref(0);

const props = defineProps({
  slides: Array,
});

function goToSlide(index) {
  currentIndex.value = index;
}

function autoSlide() {
  setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % props.slides.length;
  }, 3000);
}

onMounted(() => autoSlide());
</script>

<template>
  <div class="max-w-[1080px] max-h-[512px] relative overflow-hidden">
    <div
        class="flex transition-transform duration-500"
        :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
    >
      <div
          class="flex flex-shrink-0 w-full"
          v-for="(slide, index) in slides"
          :key="index"
      >
        <img :src="slide" alt="image" class="object-cover w-full h-full"/>
      </div>
    </div>
    <div
        class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2"
    >
      <button
          v-for="(slide, index) in slides"
          :key="index"
          @click="goToSlide(index)"
          :class="[
          'w-4 h-4 rounded-full flex items-center justify-center',
          currentIndex === index ? 'bg-white' : 'bg-gray-400',
        ]"
      >
        <span
            :class="[
            'w-2.5 h-2.5 rounded-full',
            currentIndex === index ? 'bg-[#e8a439]' : 'bg-gray-400',
          ]"
        >
        </span>
      </button>
    </div>
  </div>
</template>

<style scoped></style>

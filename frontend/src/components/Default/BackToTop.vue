<template>
  <div v-if="isVisible" class="fixed bottom-14 right-14">
    <button
      @click="scrollToTop"
      class="w-12 h-12 bg-[#f5f5f5] rounded-full flex justify-center items-center"
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
          d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"
        />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { useScrolling } from "@/composables/useScrolling.js";

const isVisible = ref(false);

const { scrollToTop } = useScrolling();

function showScrollButton() {
  isVisible.value = window.scrollY > 100;
}

onMounted(() => window.addEventListener("scroll", showScrollButton));
onBeforeUnmount(() => window.removeEventListener("scroll", showScrollButton));
</script>

<style lang="scss" scoped></style>

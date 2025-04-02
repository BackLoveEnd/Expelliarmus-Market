<template>
  <div class="relative">
    <router-link @click.prevent="useScrolling().scrollToTop()" to="/wishlist" class="text-black">
      <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-6 h-6"
      >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
        />
      </svg>
    </router-link>
    <div
        v-show="wishlistStore.totalItems > 0"
        class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-red-500 flex items-center justify-center"
    >
      <span class="text-white text-xs font-bold">{{ wishlistStore.totalItems }}</span>
    </div>
  </div>
</template>

<script setup>
import {useWishlistStore} from "@/stores/useWishlistStore.js";
import {useScrolling} from "@/composables/useScrolling.js";
import {onMounted} from "vue";

const wishlistStore = useWishlistStore();

onMounted(async () => {
  await wishlistStore.fetchWishlist().catch((e) => {
  });
});
</script>

<style scoped></style>

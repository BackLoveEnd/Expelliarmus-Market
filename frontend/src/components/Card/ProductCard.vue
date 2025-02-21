<template>
  <div class="w-272 h-auto flex flex-col gap-4 group">
    <div class="relative overflow-hidden">
      <img
        src="https://dummyimage.com/271x251/f5f5f5/000"
        class="rounded"
        alt="Product Image"
      />
      <button
        @click="addToWishlist"
        :class="{ active: isInWishlist }"
        class="wishlist w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
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
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
          />
        </svg>
      </button>
      <button
        @click="addToCart"
        :class="{ 'bg-[#db4444]': isInCart, 'bg-black': !isInCart }"
        class="absolute bottom-0 left-0 w-full text-center py-2 text-white opacity-0 translate-y-full transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100"
      >
        {{ isInCart ? "Remove From Cart" : "Add To Cart" }}
      </button>
    </div>
    <div class="flex flex-col space-y-2">
      <p class="font-medium">Product name</p>
      <p class="font-medium">$140</p>
      <star-rating :rating="4" :review-number="50" />
    </div>
  </div>
</template>

<script setup>
import StarRating from "@/components/Card/StarRating.vue";
import { inject, onUnmounted, ref } from "vue";
import { useAddToWishlist } from "@/composables/useAddToWishlist.js";

const isInCart = ref(false);

const emitter = inject("emitter");

const { isInWishlist, addToWishlist } = useAddToWishlist(emitter);

function addToCart() {
  isInCart.value = !isInCart.value;

  isInCart.value
    ? emitter.emit("add-to-cart")
    : emitter.emit("remove-from-cart");
}

onUnmounted(() => {
  emitter.off("add-to-cart");
  emitter.off("remove-from-cart");
});
</script>

<style scoped>
.wishlist svg {
  stroke: currentColor;
}

.wishlist.active svg {
  fill: red;
  stroke: red;
}
</style>
